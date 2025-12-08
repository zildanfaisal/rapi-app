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

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['customer', 'user'])->orderByDesc('created_at')->paginate(20);
        return view('penjualan.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = \App\Models\Customer::all();
        $products = \App\Models\Product::all();
        
        return view('penjualan.invoices.create', compact('customers', 'products'));
    }

    public function edit(Invoice $invoice)
    {
        $customers = \App\Models\Customer::all();
        $products = \App\Models\Product::all();
        $invoice->load(['items']);
        return view('penjualan.invoices.edit', compact('invoice', 'customers', 'products'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data, $invoice) {
            $wasPaid = $invoice->status_pembayaran === 'paid';
            $invoice->update([
                'customer_id' => $data['customer_id'],
                'user_id' => $data['user_id'],
                'tanggal_invoice' => $data['tanggal_invoice'],
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
                'status_pembayaran' => $data['status_pembayaran'],
                'alasan_cancel' => $data['alasan_cancel'] ?? null,
            ]);

            // Update items
            $invoice->items()->delete();
            $grandTotal = 0;
            foreach ($data['items'] as $item) {
                $subTotal = (int)$item['quantity'] * (float)$item['harga'];
                $grandTotal += $subTotal;
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'harga' => $item['harga'],
                    'sub_total' => $subTotal,
                ]);
            }

            $invoice->update(['grand_total' => $grandTotal]);

            // Adjust points based on status transitions
            $isNowPaid = $invoice->status_pembayaran === 'paid';
            $pointsForTotal = intdiv((int) round($grandTotal), 100000);
            try {
                $customer = \App\Models\Customer::find($invoice->customer_id);
                if ($customer) {
                    if ($customer->point === null) {
                        $customer->point = 0;
                        $customer->save();
                    }
                    if ($isNowPaid && !$wasPaid) {
                        // transitioned to paid: add points
                        if ($pointsForTotal > 0) {
                            $customer->increment('point', $pointsForTotal);
                        }
                    } elseif (!$isNowPaid && $wasPaid) {
                        // transitioned away from paid: remove points previously awarded for this invoice total
                        if ($pointsForTotal > 0) {
                            $newPoints = max(0, (int) $customer->point - $pointsForTotal);
                            $customer->update(['point' => $newPoints]);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // silently ignore point adjustment errors
            }

            return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully');
        });
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
                    'quantity' => $item['quantity'],
                    'harga' => $item['harga'],
                    'sub_total' => $subTotal,
                ]);
            }

            $invoice->update(['grand_total' => $grandTotal]);

            // Award points only if initial status is paid
            if (($invoice->status_pembayaran ?? 'unpaid') === 'paid') {
                try {
                    $customer = \App\Models\Customer::find($invoice->customer_id);
                    if ($customer) {
                        if ($customer->point === null) {
                            $customer->point = 0;
                            $customer->save();
                        }
                        $earnedPoints = intdiv((int) round($grandTotal), 100000);
                        if ($earnedPoints > 0) {
                            $customer->increment('point', $earnedPoints);
                        }
                    }
                } catch (\Throwable $e) {
                    // silently ignore point awarding errors
                }
            }

            return redirect()->route('invoices.index')->with('success', 'Invoice created successfully');
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
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully');
    }

    public function indexSetor()
    {
        $invoices = Invoice::with(['customer', 'user'])
            ->where('status_pembayaran', 'paid')
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('penjualan.invoices.setor_index', compact('invoices'));
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
        ]);

        return redirect()->route('invoices.setor')->with('success', 'Setor updated successfully');
    }
}
