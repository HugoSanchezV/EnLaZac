<?php

use App\Http\Controllers\PayPalController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\TelegramMadelineController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/paypal/create-order', [PayPalController::class, 'createOrder']);
Route::post('/paypal/capture-order', [PayPalController::class, 'captureOrder']);

Route::post('/telegram/webhook', [TelegramController::class, 'webhook'])->name('telegram.webhook');
Route::post('/telegram/import/contact', [TelegramMadelineController::class, 'importContact'])->name('telegram.contact.import');
Route::post('/telegram/send/message', [TelegramMadelineController::class, 'sendMessage'])->name('telegram.send.message');
Route::delete('/telegram/delete/contact', [TelegramMadelineController::class, 'destroyContact'])->name('telegram.contact.delete');
