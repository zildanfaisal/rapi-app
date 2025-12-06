<?php

namespace App\Http\Controllers;

use App\Models\FinanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceRecordController extends Controller
{
    /**
     * Display a listing of the resource (Input Keuangan).
     */
    public function index()
    {
        $financeRecords = FinanceRecord::with('user')
            ->orderBy('tanggal', 'desc')
            ->get();
        return view('finance.finance-records.index', compact('financeRecords'));
    }

    /**
     * Display history (Riwayat Keuangan - Read Only).
     */
    public function history()
    {
        $financeRecords = FinanceRecord::with('user')
            ->orderBy('tanggal', 'desc')
            ->get();
        return view('finance.finance-records.history', compact('financeRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('finance.finance-records.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|in:income,expense',
            'kategori' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ], [
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'tipe.required' => 'Tipe transaksi harus dipilih',
            'tipe.in' => 'Tipe transaksi tidak valid',
            'kategori.required' => 'Kategori harus diisi',
            'jumlah.required' => 'Jumlah harus diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah tidak boleh kurang dari 0',
        ]);

        $validated['created_by'] = Auth::id();

        FinanceRecord::create($validated);

        return redirect()->route('finance-records.index')->with('success', 'Data keuangan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(FinanceRecord $financeRecord)
    {
        return view('finance.finance-records.show', compact('financeRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinanceRecord $financeRecord)
    {
        return view('finance.finance-records.edit', compact('financeRecord'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FinanceRecord $financeRecord)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|in:income,expense',
            'kategori' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ], [
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'tipe.required' => 'Tipe transaksi harus dipilih',
            'tipe.in' => 'Tipe transaksi tidak valid',
            'kategori.required' => 'Kategori harus diisi',
            'jumlah.required' => 'Jumlah harus diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah tidak boleh kurang dari 0',
        ]);

        $validated['created_by'] = Auth::id();

        $financeRecord->update($validated);

        return redirect()->route('finance-records.index')->with('success', 'Data keuangan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FinanceRecord $financeRecord)
    {
        $financeRecord->delete();

        return redirect()->route('finance-records.index')->with('success', 'Data keuangan berhasil dihapus');
    }
}
