<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CalculatorController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/search', [SearchController::class, 'global'])->name('api.search');
Route::get('/calculator', [CalculatorController::class, 'index'])->name('calculator.index');

Route::resource('customers', CustomerController::class)->except(['destroy']);
Route::post('loans/{loan}/settle', [LoanController::class, 'settle'])->name('loans.settle');
Route::resource('loans', LoanController::class)->except(['destroy']);
Route::get('loans/{loan}/document', [LoanController::class, 'document'])->name('loans.document');

Route::resource('payments', PaymentController::class)->only(['index', 'store']);
Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

Route::get('reports', [LoanController::class, 'reports'])->name('reports.index');
