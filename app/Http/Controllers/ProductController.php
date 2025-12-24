<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductBatch;
use App\Models\InvoiceItem;
use DNS1D;
use App\Traits\ActivityLogger;

class ProductController extends Controller
{
    use ActivityLogger;

    public function index()
    {
        $products = Product::with(['batches', 'latestBatch'])->get();

        foreach ($products as $product) {
            $totalStok = (int) $product->batches->sum('quantity_sekarang');
            if ($totalStok <= 0 && $product->status !== 'unavailable') {
                $product->update(['status' => 'unavailable']);
            } elseif ($totalStok > 0 && $product->status !== 'available') {
                $product->update(['status' => 'available']);
            }
        }

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk'      => 'required|string|max:255',
            'deskripsi'        => 'nullable|string',
            'barcode'          => 'required|string|max:255|unique:products,barcode',
            'kategori'         => 'required|string|max:255',
            'harga'            => 'required|',
            'satuan'           => 'required|string|max:255',
            'foto_produk'      => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'min_stok_alert'   => 'required|integer',
            'status'           => 'required|in:available,unavailable',
        ]);

        $fotoPath = $request->file('foto_produk')->store('produk', 'public');

        $product = Product::create([
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

        self::logCreate($product, 'Produk');

        return redirect()->route('products.index')
            ->with('success', 'Product berhasil ditambahkan!');
    }

    public function show($id)
    {
        $product = Product::with(['batches', 'latestBatch'])->findOrFail($id);

        $stokMasuk = ProductBatch::where('product_id', $id)
            ->get()
            ->map(function ($batch) {
                return [
                    'type' => 'masuk',
                    'batch_number' => $batch->batch_number,
                    'quantity' => $batch->quantity_masuk,
                    'tanggal' => $batch->tanggal_masuk,
                    'keterangan' => 'Pemasukan Batch',
                ];
            });

        $stokKeluar = InvoiceItem::where('product_id', $id)
            ->with(['invoice', 'batch'])
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'keluar',
                    'batch_number' => optional($item->batch)->batch_number ?? '-',
                    'quantity' => $item->quantity,
                    'tanggal' => optional($item->invoice)->tanggal_invoice,
                    'keterangan' => 'Penjualan',
                ];
            });

        $riwayatStok = $stokMasuk
            ->merge($stokKeluar)
            ->sortBy('tanggal')
            ->values();

        return view('products.show', compact(
            'product',
            'riwayatStok'
        ));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'nama_produk'      => 'required|string|max:255',
            'barcode'          => 'required|string|max:255|unique:products,barcode,' . $product->id,
            'kategori'         => 'required|string|max:255',
            'harga'            => 'required|',
            'satuan'           => 'required|string|max:255',
            'foto_produk'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'min_stok_alert'   => 'required|integer',
            'status'           => 'required|in:available,unavailable',
        ]);

        $oldValues = $product->only([
            'nama_produk', 'deskripsi', 'barcode', 'kategori',
            'harga', 'satuan', 'min_stok_alert', 'status'
        ]);

        if ($request->hasFile('foto_produk')) {
            if ($product->foto_produk && Storage::disk('public')->exists($product->foto_produk)) {
                Storage::disk('public')->delete($product->foto_produk);
            }

            $fotoPath = $request->file('foto_produk')->store('produk', 'public');
            $product->foto_produk = $fotoPath;
        }

        $product->update($request->except('foto_produk'));


        $newValues = $product->only([
            'nama_produk', 'deskripsi', 'barcode', 'kategori',
            'harga', 'satuan', 'min_stok_alert', 'status'
        ]);
        self::logUpdate($product, 'Produk', $oldValues, $newValues);

        return redirect()->route('products.index')
            ->with('success', 'Product berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        self::logDelete($product, 'Produk');

        if ($product->foto_produk && Storage::disk('public')->exists($product->foto_produk)) {
            Storage::disk('public')->delete($product->foto_produk);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function downloadBarcode($id)
    {
        $product = Product::findOrFail($id);

        $barcodePng = DNS1D::getBarcodePNG($product->barcode, 'C128', 2, 80);
        $barcodeBinary = base64_decode($barcodePng);
        $barcodeImage = imagecreatefromstring($barcodeBinary);

        $barcodeWidth = imagesx($barcodeImage);
        $barcodeHeight = imagesy($barcodeImage);

        $padding = 10;
        $newWidth = $barcodeWidth + ($padding * 2);
        $newHeight = $barcodeHeight + 40;

        $final = imagecreatetruecolor($newWidth, $newHeight);

        $white = imagecolorallocate($final, 255, 255, 255);
        $black = imagecolorallocate($final, 0, 0, 0);

        imagefilledrectangle($final, 0, 0, $newWidth, $newHeight, $white);

        imagecopy(
            $final,
            $barcodeImage,
            $padding,
            0,
            0, 0,
            $barcodeWidth,
            $barcodeHeight
        );

        $text = $product->barcode;
        $textX = ($newWidth / 2) - (strlen($text) * 4);
        $textY = $barcodeHeight + 10;

        imagestring($final, 5, $textX, $textY, $text, $black);

        ob_start();
        imagepng($final);
        $result = ob_get_clean();

        imagedestroy($barcodeImage);
        imagedestroy($final);

        return response($result)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="barcode-'.$product->nama_produk.'.png"');
    }
}
