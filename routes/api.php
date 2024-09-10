<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HandlePaymentNotifController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/posts', [ProductController::class,'posts']);
    Route::patch('/posts/update/{id}', [ProductController::class,'update']);
    Route::delete('/posts/delete/{id}', [ProductController::class,'delete']);
    Route::post('buy', [TransactionController::class, 'buy']);
    Route::get('/logout', [AuthController::class,'logout']);
    Route::get('/me', [AuthController::class,'me']);
});
Route::post('midtrans/notif-hook', HandlePaymentNotifController::class);
Route::post('/login', [AuthController::class,'login']);
