<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;

// Rotas públicas
Route::post('/auth/login', [ApiAuthController::class, 'login']);

// Rotas protegidas
Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [ApiAuthController::class, 'logout']);
    Route::get('/auth/user', [ApiAuthController::class, 'user']);
    Route::post('/auth/refresh', [ApiAuthController::class, 'refresh']);

    // Rotas de planos
    Route::get('/plans', function () {
        return \App\Models\Plan::active()->get();
    });

    Route::post('/plans/assign', function (Request $request) {
        // Implementar atribuição de plano via API
        return response()->json(['message' => 'Plano atribuído via API']);
    });

    // Rotas de admin
    Route::middleware('scope:admin')->group(function () {
        Route::get('/admin/stats', function () {
            // Implementar estatísticas do admin via API
            return response()->json(['message' => 'Estatísticas do admin']);
        });
    });
});
