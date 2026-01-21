<?php

use Illuminate\Support\Facades\Route;

use Src\Reports\SubscriptionReport\Infrastructure\Controllers\SubscriptionReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/download-report', [SubscriptionReportController::class, 'download']);
