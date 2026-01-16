<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SubscriptionReportController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/status', function () {
    return response()->json(['status' => 'ok']);
});

// Route::apiResource('/subscription-report', SubscriptionReportController::class);

Route::get('/subscription-report/generate-report/download', [SubscriptionReportController::class, 'generateReportV1']);
Route::post('/subscription-report/generate-report/jobs/maatwebsite', [SubscriptionReportController::class, 'generateReportV2']);
Route::post('/subscription-report/generate-report/jobs/csv', [SubscriptionReportController::class, 'generateReportV3']);
