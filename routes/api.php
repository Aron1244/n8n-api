<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PaymentController;
use App\Docs\PaymentDocs;


Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::post('/users', [UserController::class, 'store']);

/* Route::group(['middleware' => ['cors']], function() {
    Route::get('/payments', [PaymentController::class,'index']);
}); */
Route::post('/payments/create', [PaymentController::class, 'create']);
Route::get('/payments/success', [PaymentController::class, 'success']);
Route::post('/payments/cancel', [PaymentController::class, 'cancel']);

Route::get('/payments', [PaymentController::class,'index']);
Route::get('/payments/{id}', [PaymentController::class,'show']);