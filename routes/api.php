<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/status', function () {
    return response()->json(['status' => 'ok']);
});

Route::prefix('/v1/report')->group(base_path('src/Reports/SubscriptionReport/Infrastructure/Routes/api.php'));
