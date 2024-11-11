<?php

use App\Http\Controllers\PayPalController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\WeebHookMercadoPagoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/paypal/create-order', [PayPalController::class, 'createOrder']);
Route::post('/paypal/capture-order', [PayPalController::class, 'captureOrder']);

Route::post('/web/hook/mercado/pago/pago', [WeebHookMercadoPagoController::class, 'webhookMercadoPagoPago']);

//SMS
Route::post('/send-sms', [TwilioController::class, 'send'])->name('sms');
Route::post('/send-what', [TwilioController::class, 'sendWhats'])->name('what');

