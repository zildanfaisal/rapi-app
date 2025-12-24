<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\SetorInvoiceRequest;
use App\Models\ProductBatch;
use App\Models\Product;
use App\Traits\ActivityLogger;

class InvoiceController extends Controller
{
    use ActivityLogger;

    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        Invoice::where('status_pembayaran', 'unpaid')
            ->whereNotNull('tanggal_jatuh_tempo')
            ->whereDate('tanggal_jatuh_tempo', '<', now()->toDateString())
            ->update(['status_pembayaran' => 'overdue']);

        $query = Invoice::with(['customer', 'user'])
            ->when($dateFrom, fn($q) => $q->whereDate('tanggal_invoice', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('tanggal_invoice', '<=', $dateTo))
            ->orderByDesc('created_at');

        $invoices = $query->paginate(20)->appends($request->only('date_from', 'date_to'));

        $paidFilter = Invoice::query()
            ->when($dateFrom, fn($q) => $q->whereDate('tanggal_invoice', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('tanggal_invoice', '<=', $dateTo));

        $totalCount = (clone $paidFilter)->count();
        $paidCount = (clone $paidFilter)->where('status_pembayaran', 'paid')->count();
        $totalPaid = (clone $paidFilter)->where('status_pembayaran', 'paid')->sum('grand_total');
        $totalSetor = (clone $paidFilter)->where('status_pembayaran', 'paid')
            ->where('status_setor', 'sudah')
            ->sum('grand_total');

        return view('penjualan.invoices.index', compact('invoices', 'totalPaid', 'totalSetor', 'paidCount', 'totalCount', 'dateFrom', 'dateTo'));
    }

    public function create()
    {
        $customers = \App\Models\Customer::all();
        $products = \App\Models\Product::all();
        $batches = \App\Models\ProductBatch::query()
            ->where(function ($q) {
                $q->whereNull('tanggal_expired')
                    ->orWhereDate('tanggal_expired', '>=', now()->toDateString());
            })
            ->where('quantity_sekarang', '>', 0)
            ->orderByDesc('created_at')
            ->get();

        return view('penjualan.invoices.create', compact('customers', 'products', 'batches'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $invoice = Invoice::create([
                'invoice_number' => $data['invoice_number'] ?? Str::upper(Str::random(8)),
                'customer_id' => $data['customer_id'],
                'user_id' => $data['user_id'],
                'tanggal_invoice' => $data['tanggal_invoice'],
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
                'tanggal_setor' => null,
                'status_pembayaran' => $data['status_pembayaran'] ?? 'unpaid',
                'status_setor' => $data['status_setor'] ?? 'belum',
                'bukti_setor' => $data['bukti_setor'] ?? null,
                'alasan_cancel' => $data['alasan_cancel'] ?? null,
                'grand_total' => 0,
            ]);

            $grandTotal = 0;
            foreach ($data['items'] as $item) {
                $subTotal = (int)$item['quantity'] * (float)$item['harga'];
                $grandTotal += $subTotal;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_id'],
                    'quantity' => $item['quantity'],
                    'harga' => $item['harga'],
                    'sub_total' => $subTotal,
                ]);

                $batch = ProductBatch::find($item['batch_id']);

                if ($batch) {
                    if ($batch->quantity_sekarang < $item['quantity']) {
                        throw new \Exception("Stok batch #{$batch->id} tidak cukup.");
                    }

                    $batch->decrement('quantity_sekarang', $item['quantity']);
                    $batch->refresh();
                    $batch->refreshStatus();

                    if ($batch->product) {
                        $batch->product->refreshAvailability();
                    } else {
                        if ($p = Product::find($batch->product_id)) {
                            $p->refreshAvailability();
                        }
                    }
                }
            }

            $invoice->update(['grand_total' => $grandTotal]);

            if (($invoice->status_pembayaran ?? 'unpaid') === 'paid') {
                try {
                    $customer = \App\Models\Customer::find($invoice->customer_id);
                    if ($customer) {
                        if ($customer->point === null) {
                            $customer->point = 0;
                            $customer->save();
                        }

                        $customer->increment('point', 1);
                    }
                } catch (\Throwable $e) {
                }
            }


            self::logCreate($invoice, 'Invoice');

            return redirect()->route('invoices.index')->with('success', 'Invoice created successfully');
        });
    }

    public function edit(Invoice $invoice)
    {
        $customers = \App\Models\Customer::all();
        $products = \App\Models\Product::all();
        $invoice->load(['items']);

        $selectedBatchIds = $invoice->items->pluck('batch_id')->filter()->unique()->values()->all();

        $batchQuery = \App\Models\ProductBatch::query()
            ->where(function($q){
                $q->whereNull('tanggal_expired')
                  ->orWhereDate('tanggal_expired', '>=', now()->toDateString());
            });

        if (!empty($selectedBatchIds)) {
            $batchQuery->where(function($q) use ($selectedBatchIds) {
                $q->where('quantity_sekarang', '>', 0)
                  ->orWhereIn('id', $selectedBatchIds);
            });
        } else {
            $batchQuery->where('quantity_sekarang', '>', 0);
        }

        $batches = $batchQuery->orderByDesc('created_at')->get();

        return view('penjualan.invoices.edit', compact('invoice', 'customers', 'products', 'batches'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data, $invoice) {
            $oldValues = $invoice->only([
                'customer_id', 'user_id', 'tanggal_invoice', 'tanggal_jatuh_tempo',
                'status_pembayaran', 'grand_total', 'alasan_cancel'
            ]);

            $oldCustomerId = $invoice->customer_id;
            $oldGrandTotal = (float) ($invoice->grand_total ?? 0);
            $wasPaid = $invoice->status_pembayaran === 'paid';

            $invoice->load(['items']);
            $oldByBatch = [];
            foreach ($invoice->items as $oldItem) {
                if (!empty($oldItem->batch_id)) {
                    $oldByBatch[$oldItem->batch_id] = ($oldByBatch[$oldItem->batch_id] ?? 0) + (int) $oldItem->quantity;
                }
            }

            $newByBatch = [];
            foreach ($data['items'] as $item) {
                if (!empty($item['batch_id'])) {
                    $newByBatch[$item['batch_id']] = ($newByBatch[$item['batch_id']] ?? 0) + (int) $item['quantity'];
                }
            }

            $isCancelled = ($data['status_pembayaran'] ?? null) === 'cancelled';
            if ($isCancelled) {
                $newByBatch = [];
            }

            $allBatchIds = array_unique(array_merge(array_keys($oldByBatch), array_keys($newByBatch)));
            foreach ($allBatchIds as $batchId) {
                $oldQty = $oldByBatch[$batchId] ?? 0;
                $newQty = $newByBatch[$batchId] ?? 0;
                $delta = $newQty - $oldQty;
                $batch = ProductBatch::find($batchId);
                if (!$batch) { continue; }

                if ($delta > 0) {
                    if ((int) $batch->quantity_sekarang < $delta) {
                        throw new \Exception("Stok batch #{$batch->id} tidak cukup.");
                    }
                    $batch->decrement('quantity_sekarang', $delta);
                } elseif ($delta < 0) {
                    $batch->increment('quantity_sekarang', abs($delta));
                }

                $batch->refresh();
                $batch->refreshStatus();
                if ($batch->product) { $batch->product->refreshAvailability(); }
                else if ($p = Product::find($batch->product_id)) { $p->refreshAvailability(); }
            }

            $invoice->update([
                'customer_id' => $data['customer_id'],
                'user_id' => $data['user_id'],
                'tanggal_invoice' => $data['tanggal_invoice'],
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
                'status_pembayaran' => $data['status_pembayaran'],
                'alasan_cancel' => $data['alasan_cancel'] ?? null,
            ]);

            $invoice->items()->delete();

            $grandTotal = 0;
            if (!$isCancelled) {
                foreach ($data['items'] as $item) {
                    $qty = (int) $item['quantity'];
                    $harga = (float) $item['harga'];
                    $subTotal = $qty * $harga;
                    $grandTotal += $subTotal;

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'product_id' => $item['product_id'],
                        'batch_id' => $item['batch_id'] ?? null,
                        'quantity' => $qty,
                        'harga' => $harga,
                        'sub_total' => $subTotal,
                    ]);
                }
            }

            $invoice->update(['grand_total' => $grandTotal]);

            try {
                $suratJalans = \App\Models\SuratJalan::where('invoice_id', $invoice->id)->get();
                foreach ($suratJalans as $sj) {
                    if ($isCancelled) {
                        $sj->update([
                            'status_pembayaran' => 'cancel',
                            'grand_total' => 0,
                            'ongkos_kirim' => 0,
                            'alasan_cancel' => $data['alasan_cancel'] ?? null,
                        ]);
                    } else {
                        $sj->update([
                            'grand_total' => ((float) $grandTotal) + ((float) ($sj->ongkos_kirim ?? 0)),
                            'status_pembayaran' => $invoice->status_pembayaran,
                        ]);
                    }
                }
            } catch (\Throwable $e) {
            }

            $isNowPaid = $invoice->status_pembayaran === 'paid';
            $oldEarnedPoints = $wasPaid ? intdiv((int) round($oldGrandTotal), 100000) : 0;
            $newEarnedPoints = $isNowPaid ? intdiv((int) round($grandTotal), 100000) : 0;

            try {
                if ($oldCustomerId !== $invoice->customer_id) {
                    if ($oldEarnedPoints > 0 && $oldCustomerId) {
                        $oldCustomer = \App\Models\Customer::find($oldCustomerId);
                        if ($oldCustomer) {
                            $curr = (int) ($oldCustomer->point ?? 0);
                            $oldCustomer->update(['point' => max(0, $curr - $oldEarnedPoints)]);
                        }
                    }
                    if ($newEarnedPoints > 0 && $invoice->customer_id) {
                        $newCustomer = \App\Models\Customer::find($invoice->customer_id);
                        if ($newCustomer) {
                            if ($newCustomer->point === null) { $newCustomer->point = 0; $newCustomer->save(); }
                            $newCustomer->increment('point', $newEarnedPoints);
                        }
                    }
                } else {
                    $customer = \App\Models\Customer::find($invoice->customer_id);
                    if ($customer) {
                        if ($customer->point === null) { $customer->point = 0; $customer->save(); }

                        if ($isNowPaid && $wasPaid) {
                            $delta = $newEarnedPoints - $oldEarnedPoints;
                            if ($delta !== 0) {
                                if ($delta > 0) {
                                    $customer->increment('point', $delta);
                                } else {
                                    $curr = (int) ($customer->point ?? 0);
                                    $customer->update(['point' => max(0, $curr + $delta)]);
                                }
                            }
                        } elseif ($isNowPaid && !$wasPaid) {
                            if ($newEarnedPoints > 0) { $customer->increment('point', $newEarnedPoints); }
                        } elseif (!$isNowPaid && $wasPaid) {
                            if ($oldEarnedPoints > 0) {
                                $curr = (int) ($customer->point ?? 0);
                                $customer->update(['point' => max(0, $curr - $oldEarnedPoints)]);
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
            }

            $newValues = $invoice->only([
                'customer_id', 'user_id', 'tanggal_invoice', 'tanggal_jatuh_tempo',
                'status_pembayaran', 'grand_total', 'alasan_cancel'
            ]);
            self::logUpdate($invoice, 'Invoice', $oldValues, $newValues);

            return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully');
        });
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['items', 'customer', 'user', 'transactions', 'surat_jalan']);
        if (request()->ajax()) {
            return view('penjualan.invoices.partials.show_modal', compact('invoice'));
        }
        return view('penjualan.invoices.show', compact('invoice'));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['items', 'customer', 'user']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penjualan.invoices.pdf', compact('invoice'))
            ->setPaper('a4');
        $filename = 'Invoice-' . ($invoice->invoice_number ?? $invoice->id) . '.pdf';
        return $pdf->stream($filename);
    }

    public function destroy(Invoice $invoice)
    {
        self::logDelete($invoice, 'Invoice');

        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully');
    }

    public function indexSetor(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $base = Invoice::with(['customer', 'user'])
            ->where('status_pembayaran', 'paid')
            ->when($dateFrom, fn($q) => $q->whereDate('tanggal_invoice', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('tanggal_invoice', '<=', $dateTo));

        $notDepositedTotal = (clone $base)
            ->where(function ($q) {
                $q->whereNull('status_setor')->orWhere('status_setor', '!=', 'sudah');
            })
            ->sum('grand_total');

        $invoices = (clone $base)
            ->orderByDesc('created_at')
            ->paginate(20)
            ->appends($request->only('date_from', 'date_to'));

        return view('penjualan.invoices.setor_index', compact('invoices', 'notDepositedTotal', 'dateFrom', 'dateTo'));
    }

    public function editSetor(Invoice $invoice)
    {
        $invoice->load(['customer', 'user']);
        return view('penjualan.invoices.setor_edit', compact('invoice'));
    }

    public function updateSetor(SetorInvoiceRequest $request, Invoice $invoice)
    {
        $data = $request->validated();

        if ($request->hasFile('bukti_setor')) {
            if ($invoice->bukti_setor && \Illuminate\Support\Facades\Storage::disk('public')->exists($invoice->bukti_setor)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($invoice->bukti_setor);
            }
            $path = $request->file('bukti_setor')->store('setor', 'public');
            $data['bukti_setor'] = $path;
        }

        $invoice->update([
            'status_setor' => $data['status_setor'],
            'bukti_setor' => $data['bukti_setor'] ?? $invoice->bukti_setor,
            'tanggal_setor' => ($data['status_setor'] === 'sudah') ? now()->toDateString() : null,
        ]);

        return redirect()->route('invoices.setor')->with('success', 'Setor updated successfully');
    }
}
