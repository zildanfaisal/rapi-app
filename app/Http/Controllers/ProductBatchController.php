<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductBatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Tampilkan batch + relasi product
        $batches = ProductBatch::with('product')->get();

        return view('product-batches.index', compact('batches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil hanya kolom yang dibutuhkan
        $products = Product::select('id', 'nama_produk', 'barcode')->get();

        return view('product-batches.create', compact('products'));
    }





    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barcode'           => 'required|string|exists:products,barcode',
            'batch_number'      => 'required|digits:5',
            'harga_beli'        => 'required|',
            'tanggal_masuk'     => 'required|date',
            'tanggal_expired'   => 'required|date',
            'quantity_masuk'    => 'required|integer|min:1',
            'quantity_sekarang' => 'required|integer|min:0',
            'supplier'          => 'nullable|string|max:255',
            'status'            => 'required|in:active,expired,sold_out',
        ]);

        $product = Product::where('barcode', $request->barcode)->first();

        ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => $request->batch_number,
            'harga_beli' => $request->harga_beli,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_expired' => $request->tanggal_expired,
            'quantity_masuk' => $request->quantity_masuk,
            'quantity_sekarang' => $request->quantity_sekarang,
            'supplier' => $request->supplier,
            'status' => $request->status,
        ]);


        return redirect()->route('product-batches.index')
            ->with('success', 'Batch produk berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductBatch $productBatch)
    {
        $products = Product::all(); // untuk dropdown product
        return view('product-batches.edit', compact('productBatch', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductBatch $productBatch)
{
    $request->validate([
        'barcode'           => 'required|string|exists:products,barcode',
        'batch_number'      => 'required|digits:5',
        'harga_beli'        => 'required|',
        'tanggal_masuk'     => 'required|date',
        'tanggal_expired'   => 'required|date',
        'quantity_masuk'    => 'required|integer|min:1',
        'quantity_sekarang' => 'required|integer|min:0',
        'supplier'          => 'nullable|string|max:255',
        'status'            => 'required|in:active,expired,sold_out',
    ]);

    $product = Product::where('barcode', $request->barcode)->first();

    $productBatch->update([
        'product_id'        => $product->id,
        'batch_number'      => $request->batch_number,
        'harga_beli'        => $request->harga_beli,
        'tanggal_masuk'     => $request->tanggal_masuk,
        'tanggal_expired'   => $request->tanggal_expired,
        'quantity_masuk'    => $request->quantity_masuk,
        'quantity_sekarang' => $request->quantity_sekarang,
        'supplier'          => $request->supplier,
        'status'            => $request->status,
    ]);

    return redirect()->route('product-batches.index')
                     ->with('success', 'Batch produk berhasil diperbarui!');
}


    /**
     * Remove the specified resource.
     */
    public function destroy(ProductBatch $productBatch)
    {
        $productBatch->delete();

        return redirect()->route('product-batches.index')
            ->with('success', 'Batch produk berhasil dihapus!');
    }

    public function report(Request $request)
    {
        $filter = $request->filter;
        $start = $request->start_date;
        $end = $request->end_date;
        if ($request->start_date) {
            $start = $request->start_date . '-01';
        }

        if ($request->end_date) {
            $end = \Carbon\Carbon::createFromFormat('Y-m', $request->end_date)->endOfMonth()->format('Y-m-d');
        }


        $query = ProductBatch::with('product');

        if ($filter == 'tanggal_masuk' && $start && $end) {
            $query->whereBetween('tanggal_masuk', [$start, $end]);
        }

        if ($filter == 'tanggal_expired' && $start && $end) {
            $query->whereBetween('tanggal_expired', [$start, $end]);
        }

        $batches = $query->get();

        $pdf = \PDF::loadView('product-batches.report', [
            'batches' => $batches,
            'filter' => $filter,
            'start' => $start,
            'end' => $end,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('laporan_batch_produk.pdf');
    }

}
