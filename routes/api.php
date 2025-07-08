<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;

Route::post('/auth/login', [ApiAuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [ApiAuthController::class, 'logout']);
    Route::get('/auth/user', [ApiAuthController::class, 'user']);
    Route::post('/auth/refresh', [ApiAuthController::class, 'refresh']);

    Route::get('/plans', function () {
        return \App\Models\Plan::active()->get();
    });

    Route::post('/plans/assign', function (Request $request) {
        return response()->json(['message' => 'Plano atribuído via API']);
    });

    Route::middleware('scope:admin')->group(function () {
        Route::get('/admin/stats', function () {
            return response()->json(['message' => 'Estatísticas do admin']);
        });
    });
});
