<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;

Route::get('/paypal/success', [PaymentController::class, 'success'])->name('paypal.success');
Route::get('/paypal/cancel', [PaymentController::class, 'cancel'])->name('paypal.cancel');
