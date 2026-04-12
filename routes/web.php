<?php

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ---- Public Event Listing ----
Route::get('/', [EventController::class , 'index'])->name('home');

// ---- Registrasi ----
Route::get('/daftar', [RegistrationController::class , 'showForm'])->name('registration.form');
Route::get('/daftar/{event}', [RegistrationController::class , 'showForm'])->name('registration.form.specific');
Route::post('/daftar', [RegistrationController::class , 'store'])->name('registration.store');
Route::get('/daftar/berhasil/{code}', [RegistrationController::class , 'success'])->name('registration.success');
Route::get('/daftar/penuh', fn() => view('registration.full'))->name('registration.full');

// ---- Pembayaran ----
Route::get('/bayar/{code}', [PaymentController::class , 'show'])->name('payment.show');
Route::get('/bayar/{code}/status', [PaymentController::class , 'checkStatus'])->name('payment.status');
Route::post('/midtrans/webhook', [PaymentController::class , 'webhook'])->name('payment.webhook');

// ---- Tiket ----
Route::get('/tiket/{token}', [TicketController::class , 'show'])->name('ticket.show');
Route::get('/tiket/{token}/download', [TicketController::class , 'download'])->name('ticket.download');

// ---- Scanner Petugas ----
Route::get('/scan', [ScannerController::class , 'index'])->name('scanner.index');
Route::post('/scan/verify', [ScannerController::class , 'verify'])->name('scanner.verify');
Route::get('/scan/stats', [ScannerController::class , 'stats'])->name('scanner.stats');
// URL di-encode ke QR code, redirect ke scanner
Route::get('/v/{token}', function ($token) {
    return redirect()->route('scanner.index', ['token' => $token]);
})->name('scanner.verify.page');

// ---- Authentication ----
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---- Admin (dilindungi auth) ----
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class , 'dashboard'])->name('dashboard');
    Route::get('/registrations', [AdminController::class , 'registrations'])->name('registrations');
    Route::get('/export', [AdminController::class , 'export'])->name('export');
    Route::get('/scan-logs', [AdminController::class , 'scanLogs'])->name('scan-logs');

    // Event Management
    Route::get('/events', [AdminController::class , 'events'])->name('events');
    Route::get('/events/create', [AdminController::class , 'createEvent'])->name('events.create');
    Route::post('/events', [AdminController::class , 'storeEvent'])->name('events.store');
    Route::get('/events/{event}/edit', [AdminController::class , 'editEvent'])->name('events.edit');
    Route::put('/events/{event}', [AdminController::class , 'updateEvent'])->name('events.update');
    Route::delete('/events/{event}', [AdminController::class , 'deleteEvent'])->name('events.delete');
});
