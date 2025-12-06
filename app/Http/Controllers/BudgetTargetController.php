<?php

namespace App\Http\Controllers;

use App\Models\BudgetTarget;
use Illuminate\Http\Request;

class BudgetTargetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $budgetTargets = BudgetTarget::orderBy('tanggal', 'desc')->get();
        return view('finance.budget-target.index', compact('budgetTargets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('finance.budget-target.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'budget_bulanan' => 'required|numeric|min:0',
        ], [
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'budget_bulanan.required' => 'Budget bulanan harus diisi',
            'budget_bulanan.numeric' => 'Budget bulanan harus berupa angka',
            'budget_bulanan.min' => 'Budget bulanan tidak boleh kurang dari 0',
        ]);

        BudgetTarget::create($validated);

        return redirect()->route('budget-target.index')->with('success', 'Target anggaran berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(BudgetTarget $budgetTarget)
    {
        return view('finance.budget-target.show', compact('budgetTarget'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BudgetTarget $budgetTarget)
    {
        return view('finance.budget-target.edit', compact('budgetTarget'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BudgetTarget $budgetTarget)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'budget_bulanan' => 'required|numeric|min:0',
        ], [
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'budget_bulanan.required' => 'Budget bulanan harus diisi',
            'budget_bulanan.numeric' => 'Budget bulanan harus berupa angka',
            'budget_bulanan.min' => 'Budget bulanan tidak boleh kurang dari 0',
        ]);

        $budgetTarget->update($validated);

        return redirect()->route('budget-target.index')->with('success', 'Target anggaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BudgetTarget $budgetTarget)
    {
        $budgetTarget->delete();

        return redirect()->route('budget-target.index')->with('success', 'Target anggaran berhasil dihapus');
    }
}
