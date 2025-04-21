<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PaymentController;


Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::post('/users', [UserController::class, 'store']);

Route::post('/payments/create', [PaymentController::class, 'create']);
Route::get('/payments/success', [PaymentController::class, 'success']);
Route::get('/payments/cancel', [PaymentController::class, 'cancel']);

// routes/web.php o routes/api.php
Route::get('/paypal/success', [PaymentController::class, 'paypalSuccess'])->name('paypal.success');
Route::get('/paypal/cancel', [PaymentController::class, 'cancel'])->name('paypal.cancel');
