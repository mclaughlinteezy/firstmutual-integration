<?php

use App\Http\Controllers\WebMedicalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('medical.index');
});

Route::prefix('medical')->name('medical.')->group(function () {
    Route::get('/', [WebMedicalController::class, 'index'])->name('index');
    Route::get('/create', [WebMedicalController::class, 'create'])->name('create');
    Route::get('/create-manual', [WebMedicalController::class, 'createManual'])->name('create-manual');
    Route::post('/store', [WebMedicalController::class, 'store'])->name('store');
    Route::post('/store-manual', [WebMedicalController::class, 'storeManual'])->name('store-manual');
    Route::get('/{id}', [WebMedicalController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [WebMedicalController::class, 'edit'])->name('edit');
    Route::put('/{id}', [WebMedicalController::class, 'update'])->name('update');
    Route::post('/{id}/retry', [WebMedicalController::class, 'retry'])->name('retry');
    Route::delete('/{id}', [WebMedicalController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-action', [WebMedicalController::class, 'bulkAction'])->name('bulk-action');
});
