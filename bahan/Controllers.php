<?php
// ============================================================
// FILE: app/Http/Controllers/RegistrationController.php
// ============================================================
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Jobs\GenerateTicketJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class RegistrationController extends Controller
{
    public function showForm()
    {
        $event = Event::getActive();
        
        if (!$event->is_active) {
            return view('registration.closed');
        }

        if (!$event->isQuotaAvailable()) {
            return view('registration.full', compact('event'));
        }

        return view('registration.form', compact('event'));
    }

    public function store(Request $request)
    {
        // Rate limiting per IP: max 5 submit per 10 menit
        $key = 'registration:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['rate' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."]);
        }
        RateLimiter::hit($key, 600);

        $validated = $request->validate([
            'full_name'   => 'required|string|max:100',
            'email'       => 'required|email|max:150',
            'phone'       => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
            'id_number'   => 'nullable|string|max:20',
            'gender'      => 'nullable|in:male,female',
            'institution' => 'nullable|string|max:100',
            'address'     => 'nullable|string|max:300',
        ]);

        $event = Event::getActive();

        // Atomic check quota + insert menggunakan DB transaction
        return DB::transaction(function () use ($validated, $event, $request) {
            // Lock row untuk hindari race condition
            $currentEvent = Event::lockForUpdate()->find($event->id);

            if (!$currentEvent->isQuotaAvailable()) {
                return redirect()->route('registration.full')
                    ->with('error', 'Maaf, kuota sudah penuh!');
            }

            // Cek email sudah daftar
            $existing = Registration::where('email', $validated['email'])
                ->where('event_id', $event->id)
                ->first();
            
            if ($existing) {
                return redirect()->route('ticket.show', $existing->ticket?->token ?? $existing->registration_code)
                    ->with('info', 'Anda sudah terdaftar sebelumnya.');
            }

            $registration = Registration::create([
                ...$validated,
                'event_id'          => $event->id,
                'registration_code' => Registration::generateRegistrationCode(),
                'payment_status'    => $event->is_free ? 'free' : 'pending',
                'amount_paid'       => $event->is_free ? 0 : $event->price,
            ]);

            $currentEvent->incrementRegistered();

            if ($event->is_free) {
                // Dispatch job generate tiket ke queue
                GenerateTicketJob::dispatch($registration);
                return redirect()->route('registration.success', $registration->registration_code);
            }

            // Buat order Midtrans
            return redirect()->route('payment.show', $registration->registration_code);
        });
    }

    public function success($code)
    {
        $registration = Registration::where('registration_code', $code)
            ->with(['ticket', 'event'])
            ->firstOrFail();

        return view('registration.success', compact('registration'));
    }
}

// ============================================================
// FILE: app/Http/Controllers/PaymentController.php
// ============================================================
namespace App\Http\Controllers;

use App\Models\Registration;
use App\Jobs\GenerateTicketJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function show($code)
    {
        $registration = Registration::where('registration_code', $code)
            ->with('event')
            ->firstOrFail();

        if ($registration->isPaid()) {
            return redirect()->route('ticket.show', $registration->ticket->token);
        }

        // Generate Snap Token Midtrans
        \Midtrans\Config::$serverKey       = config('midtrans.server_key');
        \Midtrans\Config::$isProduction    = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized     = true;
        \Midtrans\Config::$is3ds           = true;

        $orderId = $registration->registration_code . '-' . time();
        
        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $registration->event->price,
            ],
            'customer_details' => [
                'first_name' => $registration->full_name,
                'email'      => $registration->email,
                'phone'      => $registration->phone,
            ],
            'enabled_payments' => ['gopay', 'qris', 'bank_transfer'],
            'expiry' => [
                'unit'     => 'hours',
                'duration' => 2,
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $registration->update(['order_id' => $orderId]);

        return view('payment.show', compact('registration', 'snapToken'));
    }

    // Webhook dari Midtrans (harus di-exclude dari CSRF)
    public function webhook(Request $request)
    {
        $notif = new \Midtrans\Notification();
        
        $transactionStatus = $notif->transaction_status;
        $orderId           = $notif->order_id;
        $paymentType       = $notif->payment_type;
        $fraudStatus       = $notif->fraud_status ?? null;

        Log::info('Midtrans webhook', compact('transactionStatus', 'orderId', 'paymentType'));

        $registration = Registration::where('order_id', $orderId)->first();
        if (!$registration) return response('OK', 200);

        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            $this->markAsPaid($registration, $paymentType);
        } elseif ($transactionStatus === 'settlement') {
            $this->markAsPaid($registration, $paymentType);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $registration->update(['payment_status' => 'failed']);
        }

        return response('OK', 200);
    }

    private function markAsPaid(Registration $registration, string $paymentType): void
    {
        if ($registration->payment_status === 'paid') return; // idempotent

        $registration->update([
            'payment_status' => 'paid',
            'payment_method' => $paymentType,
            'paid_at'        => now(),
        ]);

        GenerateTicketJob::dispatch($registration);
    }

    // Cek status pembayaran (polling dari frontend)
    public function checkStatus($code)
    {
        $registration = Registration::where('registration_code', $code)
            ->select('payment_status', 'registration_code')
            ->with('ticket:id,registration_id,token')
            ->firstOrFail();

        return response()->json([
            'status'      => $registration->payment_status,
            'ticket_url'  => $registration->ticket
                ? route('ticket.show', $registration->ticket->token)
                : null,
        ]);
    }
}

// ============================================================
// FILE: app/Http/Controllers/TicketController.php
// ============================================================
namespace App\Http\Controllers;

use App\Models\Ticket;

class TicketController extends Controller
{
    public function show($token)
    {
        $ticket = Ticket::where('token', $token)
            ->with(['registration.event'])
            ->firstOrFail();

        if (!$ticket->registration->isPaid()) {
            return view('ticket.not-paid', compact('ticket'));
        }

        return view('ticket.show', compact('ticket'));
    }
}

// ============================================================
// FILE: app/Http/Controllers/ScannerController.php
// ============================================================
namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\ScanLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ScannerController extends Controller
{
    // Halaman scanner (dilindungi auth/PIN petugas)
    public function index()
    {
        return view('scanner.index');
    }

    // API endpoint scan QR - dioptimasi untuk kecepatan
    public function verify(Request $request)
    {
        $token       = $request->input('token');
        $scannerName = $request->input('scanner_name', 'Petugas');

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid'], 400);
        }

        // Cache hasil verifikasi selama 60 detik (hindari double-scan spam)
        $cacheKey = 'scan_result:' . $token;

        // Cari tiket - query ringan dengan index
        $ticket = Ticket::where('token', $token)
            ->with(['registration:id,full_name,email,phone,institution,event_id,payment_status',
                    'registration.event:id,name,event_date'])
            ->first();

        if (!$ticket) {
            $this->logScan($token, false, 'Tiket tidak ditemukan', $scannerName, $request->ip());
            return response()->json([
                'success' => false,
                'status'  => 'invalid',
                'message' => '❌ Tiket tidak valid atau tidak ditemukan',
            ]);
        }

        if (!$ticket->registration->isPaid()) {
            $this->logScan($token, false, 'Belum bayar', $scannerName, $request->ip());
            return response()->json([
                'success' => false,
                'status'  => 'unpaid',
                'message' => '💳 Tiket belum lunas pembayaran',
            ]);
        }

        if ($ticket->is_used) {
            $this->logScan($token, false, 'Sudah digunakan', $scannerName, $request->ip());
            return response()->json([
                'success'  => false,
                'status'   => 'used',
                'message'  => '⚠️ Tiket ini SUDAH DIGUNAKAN',
                'used_at'  => $ticket->used_at?->format('d M Y H:i'),
                'used_by'  => $ticket->used_by,
                'attendee' => $ticket->registration->full_name,
            ]);
        }

        // Tandai tiket sudah digunakan (atomic update)
        $updated = Ticket::where('id', $ticket->id)
            ->where('is_used', false)   // double-check race condition
            ->update([
                'is_used'    => true,
                'used_at'    => now(),
                'used_by'    => $scannerName,
                'used_device'=> $request->userAgent(),
            ]);

        if (!$updated) {
            // Race condition - tiket baru saja discan orang lain
            return response()->json([
                'success' => false,
                'status'  => 'used',
                'message' => '⚠️ Tiket ini baru saja digunakan (double scan)',
            ]);
        }

        $this->logScan($token, true, 'Berhasil masuk', $scannerName, $request->ip());

        return response()->json([
            'success'     => true,
            'status'      => 'valid',
            'message'     => '✅ TIKET VALID - Silakan Masuk!',
            'attendee'    => [
                'name'        => $ticket->registration->full_name,
                'email'       => $ticket->registration->email,
                'phone'       => $ticket->registration->phone,
                'institution' => $ticket->registration->institution,
            ],
            'event'       => $ticket->registration->event->name,
            'checked_in'  => now()->format('d M Y H:i:s'),
        ]);
    }

    private function logScan(string $token, bool $success, string $message, string $scanner, string $ip): void
    {
        // Insert log secara async agar scan tidak lambat
        dispatch(function () use ($token, $success, $message, $scanner, $ip) {
            \App\Models\ScanLog::create([
                'token'        => $token,
                'success'      => $success,
                'message'      => $message,
                'scanner_name' => $scanner,
                'ip_address'   => $ip,
            ]);
        })->afterResponse();
    }

    // Statistik realtime untuk admin
    public function stats()
    {
        return Cache::remember('scan_stats', 10, function () {
            return response()->json([
                'total_checked_in' => Ticket::where('is_used', true)->count(),
                'total_tickets'    => Ticket::count(),
            ]);
        });
    }
}
