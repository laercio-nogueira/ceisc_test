<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\AdminPlanController;
use App\Http\Controllers\PaymentController;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    Route::post('/plans/assign', [PlanController::class, 'assignPlan'])->name('plans.assign');
    Route::get('/plans/my-plan', [PlanController::class, 'myPlan'])->name('plans.my-plan');

    Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/plans', [AdminPlanController::class, 'index'])->name('admin.plans.index');
    Route::get('/admin/plans/{userPlan}', [AdminPlanController::class, 'show'])->name('admin.plans.show');
    Route::put('/admin/plans/{userPlan}/status', [AdminPlanController::class, 'updateStatus'])->name('admin.plans.update-status');
    Route::get('/admin/users/{user}/plans', [AdminPlanController::class, 'userPlans'])->name('admin.plans.user-plans');
});

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    });
});
