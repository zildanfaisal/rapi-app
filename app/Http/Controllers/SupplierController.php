<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use ActivityLogger;

    public function index()
    {
        $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email',
            'alamat' => 'nullable|string|max:1000',
        ]);

        $supplier = Supplier::create([
            'nama_supplier' => $request->nama_supplier,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'alamat' => $request->alamat,
        ]);

        self::logCreate($supplier, 'Supplier', 'Supplier');

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email,' . $supplier->id,
            'alamat' => 'nullable|string|max:1000',
        ]);

        $oldValues = $supplier->only([
            'nama_supplier',
            'no_hp',
            'email',
            'alamat',
        ]);

        $supplier->update([
            'nama_supplier' => $request->nama_supplier,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'alamat' => $request->alamat,
        ]);

        $newValues = $supplier->only([
            'nama_supplier',
            'no_hp',
            'email',
            'alamat',
        ]);

        self::logUpdate($supplier, 'Supplier', $oldValues, $newValues, 'Supplier');

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui!');
    }

    public function destroy(Supplier $supplier)
    {
        self::logDelete($supplier, 'Supplier', 'Supplier');

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus!');
    }
}
