<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/pay/easymoney', [PaymentController::class, 'payWithEasyMoney']);
Route::post('/pay/superwalletz', [PaymentController::class, 'payWithSuperWalletz']);
Route::post('/webhook/superwalletz', [PaymentController::class, 'handleSuperWalletzWebhook']);
