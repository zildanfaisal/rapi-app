<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianItem;
use App\Models\PembayaranPembelian;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Supplier;
use App\Traits\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PembelianController extends Controller
{
    use ActivityLogger;

    public function index(Request $request)
    {
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $statusFilter = $request->string('status_pembayaran')->toString();

        $pembelians = $this->filteredPembelianQuery($dateFrom, $dateTo, $statusFilter)
            ->with(['supplier', 'user', 'items.product', 'pembayarans'])
            ->get();

        $paidFilter = Pembelian::query()
            ->when($dateFrom, fn($q) => $q->whereDate('tanggal_pembelian', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('tanggal_pembelian', '<=', $dateTo));

        $totalCount = (clone $paidFilter)->count();
        $paidCount = (clone $paidFilter)->where('status_pembayaran', 'paid')->count();
        $totalPaid = (clone $paidFilter)->where('status_pembayaran', 'paid')->sum('grand_total');
        $totalSetor = (clone $paidFilter)->where('status_pembayaran', 'paid')
            ->where('status_setor', 'sudah')
            ->sum('grand_total');

        return view('pembelian.index', compact('pembelians', 'totalPaid', 'totalSetor', 'paidCount', 'totalCount', 'dateFrom', 'dateTo', 'statusFilter'));
    }

    public function exportPdf(Request $request)
    {
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $statusFilter = $request->string('status_pembayaran')->toString();

        $pembelians = $this->filteredPembelianQuery($dateFrom, $dateTo, $statusFilter)
            ->with(['supplier', 'user', 'items.product', 'pembayarans'])
            ->get();

        $statusLabel = match ($statusFilter) {
            'paid' => 'Lunas',
            'unpaid' => 'Belum Lunas',
            'partial' => 'Cicilan',
            'overdue' => 'Terlambat',
            'cancelled' => 'Batal',
            default => 'Semua Status',
        };

        $pdf = Pdf::loadView('pembelian.export_pdf', compact(
            'pembelians',
            'dateFrom',
            'dateTo',
            'statusFilter',
            'statusLabel'
        ))->setPaper('a4', 'landscape');

        $filename = 'Laporan-Pembelian-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->stream($filename);
    }

    public function exportExcel(Request $request)
    {
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $statusFilter = $request->string('status_pembayaran')->toString();

        $pembelians = $this->filteredPembelianQuery($dateFrom, $dateTo, $statusFilter)
            ->with(['supplier', 'items.product', 'pembayarans'])
            ->get();

        $statusLabel = match ($statusFilter) {
            'paid' => 'Lunas',
            'unpaid' => 'Belum Lunas',
            'partial' => 'Cicilan',
            'overdue' => 'Terlambat',
            'cancelled' => 'Batal',
            default => 'Semua Status',
        };

        $content = view('pembelian.export_excel', compact(
            'pembelians',
            'dateFrom',
            'dateTo',
            'statusLabel'
        ))->render();

        $filename = 'Laporan-Pembelian-' . now()->format('Ymd-His') . '.xls';

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();
        $products = Product::orderBy('nama_produk')->get();
        return view('pembelian.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string|max:255|unique:pembelians,invoice_number',
            'supplier_type' => 'required|in:existing,new',
            'supplier_id' => 'required_if:supplier_type,existing|nullable|exists:suppliers,id',
            'supplier_name' => 'required_if:supplier_type,new|nullable|string|max:255',
            'supplier_no_hp' => 'nullable|string|max:255',
            'supplier_email' => 'nullable|email|max:255',
            'supplier_alamat' => 'nullable|string|max:1000',
            'tanggal_pembelian' => 'required|date',
            'metode_pembayaran' => 'nullable|string|max:255',
            'status_pembayaran' => 'required|in:paid,unpaid,overdue,cancelled,partial',
            'bukti_setor' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.tanggal_expired' => 'nullable|date',
            'cicilan_jumlah_bayar' => 'nullable|numeric|min:1',
            'cicilan_tanggal_bayar' => 'nullable|date',
            'cicilan_catatan' => 'nullable|string',
        ]);

        $grandTotal = 0;
        if ($request->has('items')) {
            foreach ($request->items as $item) {
                $grandTotal += (int)($item['quantity'] ?? 0) * (float)($item['harga'] ?? 0);
            }
        }
        if ($request->status_pembayaran === 'unpaid' && $request->filled('cicilan_jumlah_bayar') && $request->cicilan_jumlah_bayar > $grandTotal) {
            $selisih = $request->cicilan_jumlah_bayar - $grandTotal;
            return back()->withErrors(['cicilan_jumlah_bayar' => 'Jumlah bayar cicilan tidak boleh melebihi total tagihan (Rp ' . number_format($grandTotal, 0, ',', '.') . '). Kelebihan Rp ' . number_format($selisih, 0, ',', '.') . '.'])->withInput();
        }

        return DB::transaction(function () use ($request) {
            // Handle supplier
            if ($request->supplier_type === 'new') {
                $supplier = Supplier::create([
                    'nama_supplier' => $request->supplier_name,
                    'no_hp' => $request->supplier_no_hp,
                    'email' => $request->supplier_email,
                    'alamat' => $request->supplier_alamat,
                ]);
                $supplierId = $supplier->id;
            } else {
                $supplierId = $request->supplier_id;
            }

            $pembelian = Pembelian::create([
                'invoice_number' => $request->invoice_number ?: 'PB-' . Str::upper(Str::random(8)),
                'supplier_id' => $supplierId,
                'user_id' => Auth::id(),
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'grand_total' => 0,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => $request->status_pembayaran,
                'status_setor' => $request->hasFile('bukti_setor') ? 'sudah' : 'belum',
                'bukti_setor' => $request->hasFile('bukti_setor')
                    ? $request->file('bukti_setor')->store('bukti-setor-pembelian', 'public')
                    : null,
                'alasan_cancel' => null,
            ]);

            $grandTotal = 0;
            foreach ($request->items as $item) {
                $subTotal = (int) $item['quantity'] * (float) $item['harga'];
                $grandTotal += $subTotal;

                // Auto-create new batch for this product
                $product = Product::find($item['product_id']);
                $now = now();
                $datePart = $now->format('dmy');
                $firstLetter = strtoupper(mb_substr($product->nama_produk ?? 'X', 0, 1));
                $timePart = $now->format('Hi');
                // Add random digit to avoid collision when multiple items saved same second
                $batchNumber = 'SN-' . $datePart . '-' . $firstLetter . $timePart . rand(0, 9);

                $batch = ProductBatch::create([
                    'product_id' => $item['product_id'],
                    'batch_number' => $batchNumber,
                    'quantity_masuk' => $item['quantity'],
                    'quantity_sekarang' => $item['quantity'],
                    'tanggal_masuk' => $request->tanggal_pembelian,
                    'tanggal_expired' => $item['tanggal_expired'] ?? null,
                    'status' => 'active',
                ]);

                PembelianItem::create([
                    'pembelian_id' => $pembelian->id,
                    'product_id' => $item['product_id'],
                    'batch_id' => $batch->id,
                    'quantity' => $item['quantity'],
                    'harga' => $item['harga'],
                    'sub_total' => $subTotal,
                ]);
            }

            $pembelian->update(['grand_total' => $grandTotal]);

            if ($request->status_pembayaran === 'paid') {
                if ($grandTotal > 0) {
                    $pembelian->pembayarans()->create([
                        'jumlah_bayar' => $grandTotal,
                        'tanggal_bayar' => $request->tanggal_pembelian,
                        'metode_pembayaran' => $request->metode_pembayaran ?? 'tunai',
                        'bukti_setor' => $pembelian->bukti_setor,
                        'catatan' => 'Pembayaran lunas (Otomatis saat pembuatan)',
                    ]);
                }
            } elseif ($request->status_pembayaran === 'unpaid') {
                if ($request->filled('cicilan_jumlah_bayar') && $request->cicilan_jumlah_bayar > 0) {
                    $pembelian->pembayarans()->create([
                        'jumlah_bayar' => $request->cicilan_jumlah_bayar,
                        'tanggal_bayar' => $request->cicilan_tanggal_bayar ?? now()->toDateString(),
                        'metode_pembayaran' => $request->metode_pembayaran ?? 'tunai',
                        'bukti_setor' => $pembelian->bukti_setor,
                        'catatan' => $request->cicilan_catatan,
                    ]);
                }

                // Update status berdasarkan riwayat pembayaran
                $totalBayar = $pembelian->pembayarans()->sum('jumlah_bayar');
                if ($totalBayar >= $grandTotal) {
                    $pembelian->update([
                        'status_pembayaran' => 'paid',
                        'status_setor' => 'sudah',
                    ]);
                } elseif ($totalBayar > 0) {
                    $pembelian->update([
                        'status_pembayaran' => 'partial',
                        'status_setor' => 'belum',
                    ]);
                }
            }

            self::logCreate($pembelian, 'Pembelian', 'Pembelian');

            return redirect()->route('pembelian.index')
                ->with('success', 'Pembelian berhasil ditambahkan!');
        });
    }

    public function show(Pembelian $pembelian)
    {
        $pembelian->load(['supplier', 'user', 'items.product', 'items.batch', 'pembayarans']);

        return view('pembelian.show', compact('pembelian'));
    }

    public function kwitansiPdf(Pembelian $pembelian)
    {
        $pembelian->load(['supplier', 'user', 'items.product', 'pembayarans']);

        $pdf = Pdf::loadView('pembelian.kwitansi_pdf', compact('pembelian'))
            ->setPaper('a4');

        $filename = 'Kwitansi-Pembelian-' . ($pembelian->invoice_number ?? $pembelian->id) . '.pdf';

        return $pdf->stream($filename);
    }

    public function storePembayaran(Request $request, Pembelian $pembelian)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|string|max:255',
            'bukti_setor' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'catatan' => 'nullable|string',
        ]);

        $sisa = $pembelian->sisa_tagihan;

        if ($request->jumlah_bayar > $sisa) {
            return back()->withErrors(['jumlah_bayar' => 'Jumlah bayar tidak boleh melebihi sisa tagihan (Rp ' . number_format($sisa, 0, ',', '.') . ')']);
        }

        DB::transaction(function () use ($request, $pembelian, $sisa) {
            $buktiSetorPath = null;
            if ($request->hasFile('bukti_setor')) {
                $buktiSetorPath = $request->file('bukti_setor')->store('bukti-setor-pembelian', 'public');
            }

            $pembelian->pembayarans()->create([
                'jumlah_bayar' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'bukti_setor' => $buktiSetorPath,
                'catatan' => $request->catatan,
            ]);

            // Update status pembayaran
            $totalBayar = $pembelian->pembayarans()->sum('jumlah_bayar');
            if ($totalBayar >= $pembelian->grand_total) {
                $pembelian->update([
                    'status_pembayaran' => 'paid',
                    'status_setor' => 'sudah',
                ]);
            } else {
                $pembelian->update([
                    'status_pembayaran' => 'partial',
                    'status_setor' => 'belum',
                ]);
            }
        });

        return back()->with('success', 'Pembayaran berhasil ditambahkan!');
    }

    public function edit(Pembelian $pembelian)
    {
        $pembelian->load(['items.product', 'items.batch']);
        $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();
        $products = Product::orderBy('nama_produk')->get();

        return view('pembelian.edit', compact('pembelian', 'suppliers', 'products'));
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        $request->validate([
            'invoice_number' => 'required|string|max:255|unique:pembelians,invoice_number,' . $pembelian->id,
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal_pembelian' => 'required|date',
            'metode_pembayaran' => 'nullable|string|max:255',
            'status_pembayaran' => 'required|in:paid,unpaid,overdue,cancelled,partial',
            'bukti_setor' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.tanggal_expired' => 'nullable|date',
            'cicilan_jumlah_bayar' => 'nullable|numeric|min:1',
            'cicilan_tanggal_bayar' => 'nullable|date',
            'cicilan_catatan' => 'nullable|string',
        ]);

        $grandTotal = 0;
        if ($request->has('items')) {
            foreach ($request->items as $item) {
                $grandTotal += (int)($item['quantity'] ?? 0) * (float)($item['harga'] ?? 0);
            }
        }
        if ($request->status_pembayaran === 'unpaid' && $request->filled('cicilan_jumlah_bayar') && $request->cicilan_jumlah_bayar > $grandTotal) {
            $selisih = $request->cicilan_jumlah_bayar - $grandTotal;
            return back()->withErrors(['cicilan_jumlah_bayar' => 'Jumlah bayar cicilan tidak boleh melebihi total tagihan (Rp ' . number_format($grandTotal, 0, ',', '.') . '). Kelebihan Rp ' . number_format($selisih, 0, ',', '.') . '.'])->withInput();
        }

        return DB::transaction(function () use ($request, $pembelian) {
            $oldValues = $pembelian->only([
                'invoice_number', 'supplier_id', 'user_id', 'tanggal_pembelian',
                'grand_total', 'metode_pembayaran', 'status_pembayaran',
                'status_setor', 'bukti_setor', 'alasan_cancel',
            ]);

            // Delete old items and their batches
            $pembelian->load('items.batch');
            foreach ($pembelian->items as $oldItem) {
                if ($oldItem->batch) {
                    $oldItem->batch->delete();
                }
                $oldItem->delete();
            }

            // Re-create items and batches
            $grandTotal = 0;
            foreach ($request->items as $item) {
                $subTotal = (int) $item['quantity'] * (float) $item['harga'];
                $grandTotal += $subTotal;

                $product = Product::find($item['product_id']);
                $now = now();
                $datePart = $now->format('dmy');
                $firstLetter = strtoupper(mb_substr($product->nama_produk ?? 'X', 0, 1));
                $timePart = $now->format('Hi');
                $batchNumber = 'SN-' . $datePart . '-' . $firstLetter . $timePart . rand(0, 9);

                $batch = ProductBatch::create([
                    'product_id' => $item['product_id'],
                    'batch_number' => $batchNumber,
                    'quantity_masuk' => $item['quantity'],
                    'quantity_sekarang' => $item['quantity'],
                    'tanggal_masuk' => $request->tanggal_pembelian,
                    'tanggal_expired' => $item['tanggal_expired'] ?? null,
                    'status' => 'active',
                ]);

                PembelianItem::create([
                    'pembelian_id' => $pembelian->id,
                    'product_id' => $item['product_id'],
                    'batch_id' => $batch->id,
                    'quantity' => $item['quantity'],
                    'harga' => $item['harga'],
                    'sub_total' => $subTotal,
                ]);
            }

            $data = [
                'invoice_number' => $request->invoice_number,
                'supplier_id' => $request->supplier_id,
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'grand_total' => $grandTotal,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => $request->status_pembayaran,
            ];

            if ($request->hasFile('bukti_setor')) {
                $data['bukti_setor'] = $request->file('bukti_setor')->store('bukti-setor-pembelian', 'public');
                $data['status_setor'] = 'sudah';
            }

            $pembelian->update($data);

            if ($request->status_pembayaran === 'paid') {
                if ($pembelian->sisa_tagihan > 0) {
                    $pembelian->pembayarans()->create([
                        'jumlah_bayar' => $pembelian->sisa_tagihan,
                        'tanggal_bayar' => $request->tanggal_pembelian,
                        'metode_pembayaran' => $request->metode_pembayaran ?? 'tunai',
                        'bukti_setor' => $pembelian->bukti_setor,
                        'catatan' => 'Pelunasan (Otomatis dari ubah status)',
                    ]);
                }
            } elseif ($request->status_pembayaran === 'unpaid') {
                // Hapus riwayat lama (reset dari 0) jika diubah ke unpaid dari halaman edit
                $pembelian->pembayarans()->delete();

                if ($request->filled('cicilan_jumlah_bayar') && $request->cicilan_jumlah_bayar > 0) {
                    $pembelian->pembayarans()->create([
                        'jumlah_bayar' => $request->cicilan_jumlah_bayar,
                        'tanggal_bayar' => $request->cicilan_tanggal_bayar ?? now()->toDateString(),
                        'metode_pembayaran' => $request->metode_pembayaran ?? 'tunai',
                        'bukti_setor' => $pembelian->bukti_setor,
                        'catatan' => $request->cicilan_catatan,
                    ]);
                }

                // Update status berdasarkan riwayat pembayaran yang ada sekarang (seharusnya menjadi partial atau unpaid)
                $totalBayar = $pembelian->pembayarans()->sum('jumlah_bayar');
                if ($totalBayar >= $pembelian->grand_total) {
                    $pembelian->update([
                        'status_pembayaran' => 'paid',
                        'status_setor' => 'sudah',
                    ]);
                } elseif ($totalBayar > 0) {
                    $pembelian->update([
                        'status_pembayaran' => 'partial',
                        'status_setor' => 'belum',
                    ]);
                } else {
                    $pembelian->update([
                        'status_pembayaran' => 'unpaid',
                        'status_setor' => 'belum',
                    ]);
                }
            }

            $newValues = $pembelian->only([
                'invoice_number', 'supplier_id', 'user_id', 'tanggal_pembelian',
                'grand_total', 'metode_pembayaran', 'status_pembayaran',
                'status_setor', 'bukti_setor', 'alasan_cancel',
            ]);

            self::logUpdate($pembelian, 'Pembelian', $oldValues, $newValues, 'Pembelian');

            return redirect()->route('pembelian.index')
                ->with('success', 'Pembelian berhasil diperbarui!');
        });
    }

    public function destroy(Pembelian $pembelian)
    {
        self::logDelete($pembelian, 'Pembelian', 'Pembelian');

        $pembelian->delete();

        return redirect()->route('pembelian.index')
            ->with('success', 'Pembelian berhasil dihapus!');
    }

    public function updatePembayaran(Request $request, PembayaranPembelian $pembayaran)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|string|max:255',
            'bukti_setor' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'catatan' => 'nullable|string',
        ]);

        $data = [
            'jumlah_bayar' => $request->jumlah_bayar,
            'tanggal_bayar' => $request->tanggal_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
            'catatan' => $request->catatan,
        ];

        if ($request->metode_pembayaran === 'tunai') {
            $data['bukti_setor'] = null;
        } elseif ($request->hasFile('bukti_setor')) {
            $data['bukti_setor'] = $request->file('bukti_setor')->store('bukti-setor-pembelian', 'public');
        }

        $pembayaran->update($data);

        // Update status pembelian
        $pembelian = $pembayaran->pembelian;
        $totalBayar = $pembelian->pembayarans()->sum('jumlah_bayar');
        if ($totalBayar >= $pembelian->grand_total) {
            $pembelian->update(['status_pembayaran' => 'paid', 'status_setor' => 'sudah']);
        } elseif ($totalBayar > 0) {
            $pembelian->update(['status_pembayaran' => 'partial', 'status_setor' => 'belum']);
        } else {
            $pembelian->update(['status_pembayaran' => 'unpaid', 'status_setor' => 'belum']);
        }

        return back()->with('success', 'Riwayat pembayaran berhasil diperbarui!');
    }

    public function destroyPembayaran(PembayaranPembelian $pembayaran)
    {
        $pembelian = $pembayaran->pembelian;
        $pembayaran->delete();

        // Update status pembelian
        $totalBayar = $pembelian->pembayarans()->sum('jumlah_bayar');
        if ($totalBayar >= $pembelian->grand_total) {
            $pembelian->update(['status_pembayaran' => 'paid', 'status_setor' => 'sudah']);
        } elseif ($totalBayar > 0) {
            $pembelian->update(['status_pembayaran' => 'partial', 'status_setor' => 'belum']);
        } else {
            $pembelian->update(['status_pembayaran' => 'unpaid', 'status_setor' => 'belum']);
        }

        return back()->with('success', 'Riwayat pembayaran berhasil dihapus!');
    }

    private function filteredPembelianQuery(?string $dateFrom, ?string $dateTo, ?string $statusFilter): Builder
    {
        return Pembelian::query()
            ->when($dateFrom, fn($q) => $q->whereDate('tanggal_pembelian', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('tanggal_pembelian', '<=', $dateTo))
            ->when($statusFilter, fn($q) => $q->where('status_pembayaran', $statusFilter))
            ->orderByDesc('created_at');
    }
}
