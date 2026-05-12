<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\RequesterController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('api/requesters', [RequesterController::class, 'api'])->name('api.requesters');
Route::get('api/projects/search', [ProjectController::class, 'apiSearch'])->name('api.projects.search');
Route::resource('requesters', RequesterController::class)->only(['index', 'store', 'destroy']);
Route::get('projects/{project}/report', [ProjectController::class, 'report'])->name('projects.report');
Route::patch('projects/{project}/close', [ProjectController::class, 'close'])->name('projects.close');
Route::patch('projects/{project}/reopen', [ProjectController::class, 'reopen'])->name('projects.reopen');
Route::resource('projects', ProjectController::class);

Route::post('transactions/quick-store', [TransactionController::class, 'quickStore'])->name('transactions.quickStore');
Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
Route::resource('transactions', TransactionController::class);
