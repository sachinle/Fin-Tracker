<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Redirect root → login (better UX)
Route::get('/', fn() => redirect()->route('login'));// Redirect root → login (better UX)
Route::get('/', fn() => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard (MAIN ENTRY)
    Route::get('/dashboard', [IncomeController::class, 'dashboard'])->name('dashboard');

    // Income Features
    Route::get('/history', [IncomeController::class, 'history'])->name('history');

    Route::get('/income/{id}/detail', [IncomeController::class, 'show'])->name('income.show');

    Route::get('/income/create', [IncomeController::class, 'create'])->name('income.create');

    Route::post('/income', [IncomeController::class, 'store'])->name('income.store');

    Route::get('/income/{id}/edit', [IncomeController::class, 'edit'])->name('income.edit');

    Route::put('/income/{id}', [IncomeController::class, 'update'])->name('income.update');

    Route::delete('/income/{id}', [IncomeController::class, 'destroy'])->name('income.destroy');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Login/Register)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';