<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
         $products = Product::all();
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
         return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $request->validate([
        'nama_produk'      => 'required|string|max:255',
        'deskripsi'        => 'nullable|string',
        'barcode'          => 'required|string|max:255|unique:products,barcode',
        'kategori'         => 'required|string|max:255',
        'harga'            => 'required|numeric',
        'satuan'           => 'required|string|max:255',
        'foto_produk'      => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'min_stok_alert'   => 'required|integer',
        'status'           => 'required|in:available,unavailable',
    ]);

    // Upload File Foto
    $fotoPath = $request->file('foto_produk')->store('produk', 'public');

    Product::create([
        'nama_produk'      => $request->nama_produk,
        'deskripsi'        => $request->deskripsi,
        'barcode'          => $request->barcode,
        'kategori'         => $request->kategori,
        'harga'            => $request->harga,
        'satuan'           => $request->satuan,
        'foto_produk'      => $fotoPath,
        'min_stok_alert'   => $request->min_stok_alert,
        'status'           => $request->status,
    ]);

    return redirect()->route('products.index')
        ->with('success', 'Product berhasil ditambahkan!');
}


    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
     public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'barcode' => 'required|string|max:255|unique:products,barcode,' . $product->id,
            'kategori' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'satuan' => 'required|string|max:255',
            'foto_produk' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'min_stok_alert' => 'required|integer',
            'status' => 'required|in:available,unavailable',
        ]);

        // Jika upload foto baru
        if ($request->hasFile('foto_produk')) {
            // Hapus foto lama
            if ($product->foto_produk && Storage::disk('public')->exists($product->foto_produk)) {
                Storage::disk('public')->delete($product->foto_produk);
            }

            $fotoPath = $request->file('foto_produk')->store('produk', 'public');
            $product->foto_produk = $fotoPath;
        }

        $product->update($request->except('foto_produk'));

        return redirect()->route('products.index')->with('success', 'Product berhasil diperbarui!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
         $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
