<?php
// ============================================================
// FILE: routes/web.php
// ============================================================
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// ---- Registrasi ----
Route::get('/',        [RegistrationController::class, 'showForm'])->name('home');
Route::get('/daftar',  [RegistrationController::class, 'showForm'])->name('registration.form');
Route::post('/daftar', [RegistrationController::class, 'store'])->name('registration.store');
Route::get('/daftar/berhasil/{code}', [RegistrationController::class, 'success'])->name('registration.success');
Route::get('/daftar/penuh', fn() => view('registration.full'))->name('registration.full');

// ---- Pembayaran ----
Route::get('/bayar/{code}',          [PaymentController::class, 'show'])->name('payment.show');
Route::get('/bayar/{code}/status',   [PaymentController::class, 'checkStatus'])->name('payment.status');

// ---- Tiket ----
Route::get('/tiket/{token}',         [TicketController::class, 'show'])->name('ticket.show');

// ---- Scanner Petugas ----
Route::get('/scan',                  [ScannerController::class, 'index'])->name('scanner.index');
Route::post('/scan/verify',          [ScannerController::class, 'verify'])->name('scanner.verify');
Route::get('/scan/stats',            [ScannerController::class, 'stats'])->name('scanner.stats');
// URL di-encode ke QR code, redirect ke scanner
Route::get('/v/{token}', function ($token) {
    return redirect()->route('scanner.index', ['token' => $token]);
})->name('scanner.verify.page');

// ---- Admin (dilindungi auth) ----
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                  [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/registrations',     [AdminController::class, 'registrations'])->name('registrations');
    Route::get('/export',            [AdminController::class, 'export'])->name('export');
    Route::get('/scan-logs',         [AdminController::class, 'scanLogs'])->name('scan-logs');
});

// ====================================================
// FILE: routes/api.php
// ====================================================
// Webhook Midtrans (exclude CSRF di bootstrap/app.php)
Route::post('/midtrans/webhook', [PaymentController::class, 'webhook'])
    ->name('midtrans.webhook');
