<?php

use App\Http\Controllers\PayPalController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\TelegramMadelineController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\WeebHookMercadoPagoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/paypal/create-order', [PayPalController::class, 'createOrder'])->middleware('web', 'auth:sanctum');
Route::post('/paypal/capture-order', [PayPalController::class, 'captureOrder'])->middleware('web', 'auth:sanctum');

Route::post('/telegram/webhook', [TelegramController::class, 'webhook'])->name('telegram.webhook');
Route::post('/telegram/import/contact', [TelegramMadelineController::class, 'importContact'])->name('telegram.contact.import');
Route::post('/telegram/send/message', [TelegramMadelineController::class, 'sendMessage'])->name('telegram.send.message');
Route::delete('/telegram/delete/contact', [TelegramMadelineController::class, 'destroyContact'])->name('telegram.contact.delete');
// Route::post('/paypal/create-order', [PayPalController::class, 'createOrder']);
// Route::post('/paypal/capture-order', [PayPalController::class, 'captureOrder']);

// Route::post('/web/hook/mercado/pago/pago', [WeebHookMercadoPagoController::class, 'webhookMercadoPagoPago'])->name('mercadopago.webhook');

//SMS
Route::post('/send-sms', [TwilioController::class, 'send'])->name('sms');
Route::post('/send-what', [TwilioController::class, 'sendWhats'])->name('what');

