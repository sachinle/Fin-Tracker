<?php
// File: app/Http/Controllers/IncomeController.php

namespace App\Http\Controllers;

use App\Models\MonthlyRecord;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $latest     = MonthlyRecord::where('user_id', auth()->id())->latest()->first();
        $records    = MonthlyRecord::where('user_id', auth()->id())->latest()->take(5)->get();  // recent table
        $allRecords = MonthlyRecord::where('user_id', auth()->id())->latest()->get();           // JS chart switching

        // Prepare data for JavaScript charts
        $allRecordsForJs = $allRecords->map(fn($r) => [
            'id'            => $r->id,
            'month'         => $r->month,
            'income'        => (float)$r->income,
            'loan'          => (float)$r->loan,
            'expenses'      => (float)$r->expenses,
            'miscellaneous' => (float)$r->miscellaneous,
            'savings'       => (float)$r->savings,
        ]);

        // Corrected aggregates:
        //   Total Income   = sum of all income fields
        //   Total Savings  = sum of auto-calculated savings (income - all deductions)
        //   Total Expenses = sum of loan + expenses + miscellaneous (all actual outflows)
        $totalIncome   = MonthlyRecord::where('user_id', auth()->id())->sum('income');
        $totalSavings  = MonthlyRecord::where('user_id', auth()->id())->sum('savings');
        $totalExpenses = MonthlyRecord::where('user_id', auth()->id())->selectRaw('SUM(loan + expenses + miscellaneous) as total')
                             ->value('total') ?? 0;
        $totalCount    = MonthlyRecord::where('user_id', auth()->id())->count();

        return view('dashboard', compact(
            'latest', 'records', 'allRecords', 'allRecordsForJs',
            'totalIncome', 'totalSavings', 'totalExpenses', 'totalCount'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────────

    public function create()
    {
        return view('income.create');
    }

    // ─────────────────────────────────────────────────────────────
    // STORE
    // User enters: month, income, loan, expenses, miscellaneous
    // System computes: savings, remaining_balance (both same value)
    // ─────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month'         => 'required|string|max:50',
            'income'        => 'required|numeric|min:0',
            'loan'          => 'nullable|numeric|min:0',
            'expenses'      => 'nullable|numeric|min:0',
            'miscellaneous' => 'nullable|numeric|min:0',
        ]);

        // Default nulls to 0
        $validated['loan']          = $validated['loan']          ?? 0;
        $validated['expenses']      = $validated['expenses']      ?? 0;
        $validated['miscellaneous'] = $validated['miscellaneous'] ?? 0;

        // Auto-calculate savings (what remains after all real deductions)
        $savings = MonthlyRecord::calculateSavings($validated);

        $validated['savings']           = $savings;
        $validated['remaining_balance'] = $savings; // kept in sync

        $validated['user_id'] = auth()->id();
        MonthlyRecord::create($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Monthly record saved successfully!');
    }

    // ─────────────────────────────────────────────────────────────
    // SHOW — detailed single-month page (from History row click)
    // ─────────────────────────────────────────────────────────────

    public function show($id)
    {
        $record = MonthlyRecord::where('user_id', auth()->id())->findOrFail($id);
        return view('income.show', compact('record'));
    }

    // ─────────────────────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $record = MonthlyRecord::where('user_id', auth()->id())->findOrFail($id);
        return view('income.edit', compact('record'));
    }

    // ─────────────────────────────────────────────────────────────
    // UPDATE
    // Same logic as store: savings is recalculated, never user-provided
    // ─────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $record = MonthlyRecord::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'month'         => 'required|string|max:50',
            'income'        => 'required|numeric|min:0',
            'loan'          => 'nullable|numeric|min:0',
            'expenses'      => 'nullable|numeric|min:0',
            'miscellaneous' => 'nullable|numeric|min:0',
        ]);

        $validated['loan']          = $validated['loan']          ?? 0;
        $validated['expenses']      = $validated['expenses']      ?? 0;
        $validated['miscellaneous'] = $validated['miscellaneous'] ?? 0;

        $savings = MonthlyRecord::calculateSavings($validated);

        $validated['savings']           = $savings;
        $validated['remaining_balance'] = $savings;

        $record->update($validated);

        return redirect()->route('history')
            ->with('success', 'Record updated successfully!');
    }

    // ─────────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        MonthlyRecord::where('user_id', auth()->id())->findOrFail($id)->delete();

        return redirect()->route('history')
            ->with('success', 'Record deleted successfully!');
    }

    // ─────────────────────────────────────────────────────────────
    // HISTORY
    // ─────────────────────────────────────────────────────────────

    public function history()
    {
        $records = MonthlyRecord::where('user_id', auth()->id())->latest()->paginate(10);
        return view('income.history', compact('records'));
    }
}