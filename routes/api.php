<?php

use App\Http\Controllers\MedicalPushedController;
use Illuminate\Support\Facades\Route;

Route::prefix('medical')->group(function () {
    Route::post('/process-student', [MedicalPushedController::class, 'processStudent']);
    Route::get('/records', [MedicalPushedController::class, 'index']);
    Route::get('/records/{id}', [MedicalPushedController::class, 'show']);
    Route::post('/records/{id}/retry', [MedicalPushedController::class, 'retry']);
});
