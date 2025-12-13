<?php

namespace App\Http\Controllers;

use App\Models\FinanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Barryvdh\DomPDF\Facade\Pdf;

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
    /**
 * Display history (Riwayat Keuangan - Read Only).
 */

    public function previewPdf(Request $request)
    {
        $periode = $request->get('periode');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = FinanceRecord::with('user');

        // Priority: Date range over periode
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($periode) {
            $query->where('periode', $periode);
        } else {
            // Default to current month
            $periode = date('Y-m');
            $query->where('periode', $periode);
        }

        $financeRecords = $query->orderBy('tanggal', 'desc')->get();

        // Get budget target
        $budgetTarget = null;
        if ($periode) {
            $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$periode])->first();
        } elseif ($startDate) {
            $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [date('Y-m', strtotime($startDate))])->first();
        }

        // Calculate totals
        $totalPemasukan = $financeRecords->where('tipe', 'income')->sum('jumlah');
        $totalPengeluaran = $financeRecords->where('tipe', 'expense')->sum('jumlah');
        $saldoSisa = $budgetTarget ? ($budgetTarget->budget_bulanan - $totalPengeluaran) : 0;

        // Return preview view instead of streaming PDF directly
        return view('finance.finance-records.pdf-preview', compact(
            'financeRecords',
            'periode',
            'startDate',
            'endDate',
            'budgetTarget',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoSisa'
        ));
    }

    /**
     * Download PDF Laporan Keuangan
     */
    public function downloadPdf(Request $request)
    {
        $periode = $request->get('periode');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = FinanceRecord::with('user');

        // Priority: Date range over periode
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($periode) {
            $query->where('periode', $periode);
        } else {
            // Default to current month
            $periode = date('Y-m');
            $query->where('periode', $periode);
        }

        $financeRecords = $query->orderBy('tanggal', 'desc')->get();

        // Get budget target
        $budgetTarget = null;
        if ($periode) {
            $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$periode])->first();
        } elseif ($startDate) {
            $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [date('Y-m', strtotime($startDate))])->first();
        }

        // Calculate totals
        $totalPemasukan = $financeRecords->where('tipe', 'income')->sum('jumlah');
        $totalPengeluaran = $financeRecords->where('tipe', 'expense')->sum('jumlah');
        $saldoSisa = $budgetTarget ? ($budgetTarget->budget_bulanan - $totalPengeluaran) : 0;

        $pdf = Pdf::loadView('finance.finance-records.pdf', compact(
            'financeRecords',
            'periode',
            'startDate',
            'endDate',
            'budgetTarget',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoSisa'
        ));

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        // Generate filename
        $filename = 'Laporan-Keuangan';
        if ($periode) {
            $filename .= '-' . \Carbon\Carbon::parse($periode . '-01')->format('F-Y');
        } elseif ($startDate && $endDate) {
            $filename .= '-' . \Carbon\Carbon::parse($startDate)->format('dMY') . '-' . \Carbon\Carbon::parse($endDate)->format('dMY');
        }

        // Download PDF
        return $pdf->download($filename . '.pdf');
    }

    public function history(Request $request)
    {
        // Default to current month if it has a budget target
        $currentMonth = date('Y-m');
        $hasCurrentMonthTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$currentMonth])->exists();

        $periode = $request->get('periode');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Only auto-select current month if this is the first visit (no query params at all)
        if (!$request->has('periode') && !$request->has('start_date') && !$request->has('end_date') && $hasCurrentMonthTarget) {
            $periode = $currentMonth;
        }

        $query = FinanceRecord::with('user');

        // Priority: Date range over periode
        if ($startDate && $endDate) {
            // If date range provided, use date range (ignore periode)
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($periode) {
            // If only periode selected, filter by periode
            $query->where('periode', $periode);
        }
        // If periode is empty string (user selected "Semua Periode"), show all data

        $financeRecords = $query->orderBy('tanggal', 'desc')->get();

        // Get budget target for selected periode
        $budgetTarget = null;
        if ($periode) {
            $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$periode])->first();
        } elseif ($startDate) {
            // Get budget target based on start date month
            $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [date('Y-m', strtotime($startDate))])->first();
        }

        // Get available periods
        $availablePeriods = \App\Models\BudgetTarget::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as periode")
            ->orderBy('tanggal', 'desc')
            ->pluck('periode')
            ->unique();

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
