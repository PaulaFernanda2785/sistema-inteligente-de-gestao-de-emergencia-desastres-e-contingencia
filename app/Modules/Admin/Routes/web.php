<?php

use App\Modules\Admin\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->middleware('idempotency')->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('idempotency')->name('users.update');
    Route::patch('/users/{user}/deactivate', [UserController::class, 'deactivate'])->middleware('idempotency')->name('users.deactivate');
});
