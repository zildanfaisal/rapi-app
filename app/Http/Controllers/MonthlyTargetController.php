<?php

namespace App\Http\Controllers;

use App\Models\MonthlyTarget;
use App\Models\Invoice;
use Illuminate\Http\Request;

class MonthlyTargetController extends Controller
{
    public function index(Request $request)
    {
        $targets = MonthlyTarget::orderByDesc('created_at')->paginate(20);

        // Compute paid-only actuals per target for display consistency
        $targets->getCollection()->transform(function($t){
            $paidSum = Invoice::query()
                ->whereDate('tanggal_invoice', '>=', $t->start_date)
                ->whereDate('tanggal_invoice', '<=', $t->end_date)
                ->where('status_pembayaran', 'paid')
                ->sum('grand_total');

            // Remaining amount to hit the target (paid invoices only)
            $remaining = max(0, ($t->target_amount ?? 0) - ($paidSum ?? 0));
            $t->computed_remaining = $remaining;

            // Auto-update status to 'achieved' when target reached
            if ($remaining <= 0 && $t->status !== 'achieved') {
                try {
                    $t->update([
                        'status' => 'achieved',
                        'achieved_total' => 0,
                    ]);
                } catch (\Throwable $e) {
                    // ignore persistence errors to avoid breaking listing
                }
            } else {
                // Keep achieved_total reflecting remaining for consistency
                try {
                    $t->update(['achieved_total' => $remaining]);
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            return $t;
        });

        return view('penjualan.monthly-target.index', compact('targets'));
    }

    public function create()
    {
        return view('penjualan.monthly-target.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['created_by'] = $request->user()->id ?? null;

        $actuals = Invoice::query()
            ->whereDate('tanggal_invoice', '>=', $data['start_date'])
            ->whereDate('tanggal_invoice', '<=', $data['end_date'])
            ->where('status_pembayaran', 'paid')
            ->sum('grand_total');

        $achievedRemaining = max(0, ($data['target_amount'] - ($actuals ?? 0)));
        $data['achieved_total'] = $achievedRemaining;

        $today = date('Y-m-d');
        if ($achievedRemaining <= 0) $status = 'achieved';
        elseif ($today >= $data['start_date'] && $today <= $data['end_date']) $status = 'ongoing';
        else $status = 'missed';
        $data['status'] = $status;

        $target = MonthlyTarget::create($data);
        return redirect()->route('monthly-targets.index')->with('success', 'Target bulanan dibuat');
    }

    public function show(MonthlyTarget $monthlyTarget)
    {
        // Recompute actuals for display accuracy and auto-sync status
        $actuals = Invoice::query()
            ->whereDate('tanggal_invoice', '>=', $monthlyTarget->start_date)
            ->whereDate('tanggal_invoice', '<=', $monthlyTarget->end_date)
            ->where('status_pembayaran', 'paid')
            ->sum('grand_total');

        $remaining = max(0, ($monthlyTarget->target_amount ?? 0) - ($actuals ?? 0));
        if ($remaining <= 0 && $monthlyTarget->status !== 'achieved') {
            try {
                $monthlyTarget->update([
                    'status' => 'achieved',
                    'achieved_total' => 0,
                ]);
            } catch (\Throwable $e) {
                // ignore
            }
        } else {
            try {
                $monthlyTarget->update(['achieved_total' => $remaining]);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $invoices = Invoice::query()
            ->whereDate('tanggal_invoice', '>=', $monthlyTarget->start_date)
            ->whereDate('tanggal_invoice', '<=', $monthlyTarget->end_date)
            ->where('status_pembayaran', 'paid')
            ->orderByDesc('tanggal_invoice')->paginate(20);
        return view('penjualan.monthly-target.show', compact('monthlyTarget', 'actuals', 'invoices'));
    }

    public function edit(MonthlyTarget $monthlyTarget)
    {
        return view('penjualan.monthly-target.edit', compact('monthlyTarget'));
    }

    public function update(Request $request, MonthlyTarget $monthlyTarget)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $actuals = Invoice::query()
            ->whereDate('tanggal_invoice', '>=', $data['start_date'])
            ->whereDate('tanggal_invoice', '<=', $data['end_date'])
            ->where('status_pembayaran', 'paid')
            ->sum('grand_total');

        $achievedRemaining = max(0, ($data['target_amount'] - ($actuals ?? 0)));
        $data['achieved_total'] = $achievedRemaining;

        $today = date('Y-m-d');
        if ($achievedRemaining <= 0) $status = 'achieved';
        elseif ($today >= $data['start_date'] && $today <= $data['end_date']) $status = 'ongoing';
        else $status = 'missed';
        $data['status'] = $status;

        $monthlyTarget->update($data);
        return redirect()->route('monthly-targets.index')->with('success', 'Target bulanan diperbarui');
    }

    public function destroy(MonthlyTarget $monthlyTarget)
    {
        $monthlyTarget->delete();
        return redirect()->route('monthly-targets.index')->with('success', 'Target bulanan dihapus');
    }
}
