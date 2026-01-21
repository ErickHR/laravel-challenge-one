<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/v1/report')->group(base_path('src/Reports/SubscriptionReport/Infrastructure/Routes/web.php'));
