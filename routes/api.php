<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Webhook Midtrans (exclude CSRF di bootstrap/app.php)
Route::post('/midtrans/webhook', [PaymentController::class, 'webhook'])
    ->name('midtrans.webhook');
