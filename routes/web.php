<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/download-stored-excel', [SubscriptionReportController::class, 'downloadStoredExcel']);
