<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoriesMenusController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\OrderScheduleController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\TypeOrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuotaController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripayCallbackController;

Route::options('{any}', function () {
    return response()->json([], 204);
})->where('any', '.*');

Route::post('login', [AuthController::class, "login"]);
Route::post('register', [AuthController::class, "register"]);
Route::post('send-otp', [AuthController::class, 'sendOtp']);
Route::post('verify', [AuthController::class, "verify"]);
Route::post('reset-password', [AuthController::class, "reset_password"]);
Route::apiResource('menu', MenuController::class);
Route::apiResource('category', CategoriesMenusController::class);
Route::apiResource('categores_menus', MenuController::class);
Route::apiResource('order', OrderController::class);
Route::apiResource('order_item', OrderItemController::class);
Route::apiResource('order_schedule', OrderScheduleController::class);
Route::apiResource('order_status', OrderStatusController::class);
Route::apiResource('payment', PaymentController::class);
Route::apiResource('payment_method', PaymentMethodController::class);
Route::apiResource('timetable', TimetableController::class);
Route::apiResource('type_order', TypeOrderController::class);
Route::apiResource('user', UserController::class);
Route::post('/user/{id}/change-password', [UserController::class, 'changePassword']);
Route::patch('/order/{id}/status', [OrderController::class, 'updateStatus']);
Route::get('/cetak-laporan', [OrderController::class, 'print']);

Route::delete('/user/{id}', [UserController::class, 'destroy']);

Route::get('/produk-terjual-per-hari', [OrderController::class, 'ordersPerDay']);

Route::get('/regular-quota', [QuotaController::class, 'getRegularQuota']);
Route::get('/custom-quota', [QuotaController::class, 'getCustomQuota']);
Route::get('/report/monthly', [ReportController::class, 'monthlyReport']);

Route::post('/tripay/callback', [TripayCallbackController::class, 'handle']);

Route::delete('/order/{id}', [OrderController::class, 'destroyorder']);

