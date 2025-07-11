<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

use App\Models\order;

Route::get('/tes-relasi', function () {
    $tanggalAwal = now()->startOfMonth()->format('Y-m-d');
    $tanggalAkhir = now()->endOfMonth()->format('Y-m-d');
    
    $order = order::with(['user', 'typeOrder', 'status'])
                    ->where('status_id', 5)
                    ->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir])
                    ->first();

    // dd($order->user->username);
    dd($order);
    // dd($order->typeOrder->name);
});
Route::get('/tes', function () {
    $order = order::with(['user', 'typeOrder', 'status'])
                    ->where('status_id', 5)
                    ->first();
});
Route::get('/cetak-laporan', [OrderController::class, 'print']);