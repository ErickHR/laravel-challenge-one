<?php

use Illuminate\Support\Facades\Route;

use Src\Reports\SubscriptionReport\Infrastructure\Controllers\SubscriptionReportController;

Route::post('/subscription-report/save-report', [SubscriptionReportController::class, 'save']);
