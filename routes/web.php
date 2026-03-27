<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncomeController;

// Redirect root → dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// Dashboard
Route::get('/dashboard', [IncomeController::class, 'dashboard'])->name('dashboard');

// History list
Route::get('/history', [IncomeController::class, 'history'])->name('history');

// Show detailed single-month page (from History row click)
Route::get('/income/{id}/detail', [IncomeController::class, 'show'])->name('income.show');

// Create form
Route::get('/income/create', [IncomeController::class, 'create'])->name('income.create');

// Store (POST)
Route::post('/income', [IncomeController::class, 'store'])->name('income.store');

// Edit form
Route::get('/income/{id}/edit', [IncomeController::class, 'edit'])->name('income.edit');

// Update (PUT)
Route::put('/income/{id}', [IncomeController::class, 'update'])->name('income.update');

// Delete (DELETE)
Route::delete('/income/{id}', [IncomeController::class, 'destroy'])->name('income.destroy');