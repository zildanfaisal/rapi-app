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
    public function index(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));

        $financeRecords = FinanceRecord::with('user')
            ->where('periode', $periode)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Get budget target for this periode
        $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$periode])->first();

        // Calculate totals
        $totalPemasukan = $financeRecords->where('tipe', 'income')->sum('jumlah');
        $totalPengeluaran = $financeRecords->where('tipe', 'expense')->sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // Get available periods from Budget Targets (not from finance records)
        $availablePeriods = \App\Models\BudgetTarget::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as periode")
            ->orderBy('tanggal', 'desc')
            ->pluck('periode')
            ->unique();

        return view('finance.finance-records.index', compact('financeRecords', 'periode', 'budgetTarget', 'totalPemasukan', 'totalPengeluaran', 'saldo', 'availablePeriods'));
    }

    /**
     * Display history (Riwayat Keuangan - Read Only).
     */
    public function history(Request $request)
    {
        $periode = $request->get('periode');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = FinanceRecord::with('user');

        // Filter by periode if selected
        if ($periode) {
            $query->where('periode', $periode);
        }

        // Filter by date range if provided
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $financeRecords = $query->orderBy('tanggal', 'desc')->get();

        // Get budget target for selected periode
        $budgetTarget = null;
        if ($periode) {
            $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$periode])->first();
        }

        // Get available periods
        $availablePeriods = FinanceRecord::selectRaw('DISTINCT periode')
            ->whereNotNull('periode')
            ->orderBy('periode', 'desc')
            ->pluck('periode');

        return view('finance.finance-records.history', compact('financeRecords', 'periode', 'startDate', 'endDate', 'budgetTarget', 'availablePeriods'));
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

        // Auto set periode from tanggal (YYYY-MM)
        $validated['periode'] = date('Y-m', strtotime($validated['tanggal']));

        FinanceRecord::create($validated);

        return redirect()->route('finance-records.index', ['periode' => $validated['periode']])->with('success', 'Data keuangan berhasil ditambahkan');
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

        // Auto set periode from tanggal (YYYY-MM)
        $validated['periode'] = date('Y-m', strtotime($validated['tanggal']));

        $financeRecord->update($validated);

        return redirect()->route('finance-records.index', ['periode' => $validated['periode']])->with('success', 'Data keuangan berhasil diperbarui');
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
