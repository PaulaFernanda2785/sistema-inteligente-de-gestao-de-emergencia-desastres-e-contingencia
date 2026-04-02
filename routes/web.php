<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function (): void {
    Route::redirect('/', '/login');

    require app_path('Modules/Auth/Routes/web.php');

    Route::middleware(['auth', 'tenant.context'])->group(function (): void {
        require app_path('Modules/Admin/Routes/web.php');
        require app_path('Modules/Territory/Routes/web.php');
        require app_path('Modules/Risk/Routes/web.php');
        require app_path('Modules/Shelter/Routes/web.php');
    });
});
