<?php

use App\Modules\Shelter\Controllers\ShelterController;
use Illuminate\Support\Facades\Route;

Route::resource('shelters', ShelterController::class)
    ->except(['show', 'destroy'])
    ->middleware('idempotency');

Route::patch('shelters/{shelter}/deactivate', [ShelterController::class, 'deactivate'])
    ->name('shelters.deactivate')
    ->middleware('idempotency');
