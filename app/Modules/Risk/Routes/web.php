<?php

use App\Modules\Risk\Controllers\RiskAreaController;
use Illuminate\Support\Facades\Route;

Route::name('risk.')->group(function (): void {
    Route::resource('risk-areas', RiskAreaController::class)
        ->except(['show', 'destroy'])
        ->parameters(['risk-areas' => 'risk_area'])
        ->names('areas')
        ->middleware('idempotency');

    Route::patch('risk-areas/{risk_area}/deactivate', [RiskAreaController::class, 'deactivate'])
        ->name('areas.deactivate')
        ->middleware('idempotency');
});
