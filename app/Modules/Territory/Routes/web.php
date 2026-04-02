<?php

use App\Modules\Territory\Controllers\TerritorialUnitController;
use App\Modules\Territory\Controllers\TerritoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('territory')->name('territory.')->group(function (): void {
    Route::resource('territories', TerritoryController::class)
        ->except(['show', 'destroy'])
        ->middleware('idempotency');

    Route::resource('units', TerritorialUnitController::class)
        ->except(['show', 'destroy'])
        ->middleware('idempotency');
});
