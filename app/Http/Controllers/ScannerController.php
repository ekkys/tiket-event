<?php

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
        $scannerName = auth()->user()->name;

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid'], 400);
        }

        // Cache hasil verifikasi selama 60 detik (hindari double-scan spam)
        $cacheKey = 'scan_result:' . $token;

        // Cari tiket - bisa lewat token (QR) atau registration_code (Input Manual)
        $ticket = Ticket::where('token', $token)
            ->orWhereHas('registration', function($q) use ($token) {
                $q->where('registration_code', $token);
            })
            ->with(['registration:id,full_name,email,phone,institution,event_id,payment_status',
                    'registration.event:id,name,event_date,user_id'])
            ->first();

        if (!$ticket) {
            $this->logScan($token, false, 'Tiket tidak ditemukan', $scannerName, $request->ip(), null);
            return response()->json([
                'success' => false,
                'status'  => 'invalid',
                'message' => '❌ Tiket tidak valid atau tidak ditemukan',
            ]);
        }

        // Validasi: Hanya pembuat event yang bisa scan
        if ($ticket->registration->event->user_id !== auth()->id()) {
            $this->logScan($token, false, 'Bukan pembuat event (Akses Ditolak)', $scannerName, $request->ip(), $ticket->id);
            return response()->json([
                'success' => false,
                'status'  => 'unauthorized',
                'message' => '❌ Anda tidak memiliki akses untuk menscan tiket event ini.',
            ], 403);
        }

        if (!$ticket->registration->isPaid()) {
            $this->logScan($token, false, 'Belum bayar', $scannerName, $request->ip(), $ticket->id);
            return response()->json([
                'success' => false,
                'status'  => 'unpaid',
                'message' => '💳 Tiket belum lunas pembayaran',
            ]);
        }

        if ($ticket->is_used) {
            $this->logScan($token, false, 'Sudah digunakan', $scannerName, $request->ip(), $ticket->id);
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

        $this->logScan($token, true, 'Berhasil masuk', $scannerName, $request->ip(), $ticket->id);

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

    private function logScan(string $token, bool $success, string $message, string $scanner, string $ip, ?int $ticketId): void
    {
        // Insert log secara async agar scan tidak lambat
        dispatch(function () use ($token, $success, $message, $scanner, $ip, $ticketId) {
            \App\Models\ScanLog::create([
                'token'        => $token,
                'ticket_id'    => $ticketId,
                'success'      => $success,
                'message'      => $message,
                'scanner_name' => $scanner,
                'ip_address'   => $ip,
            ]);
        })->afterResponse();
    }

    // Statistik realtime untuk admin (hanya event miliknya)
    public function stats()
    {
        $userId = auth()->id();
        return Cache::remember('scan_stats_' . $userId, 10, function () use ($userId) {
            return response()->json([
                'total_checked_in' => Ticket::whereHas('registration.event', fn($q) => $q->where('user_id', $userId))
                    ->where('is_used', true)->count(),
                'total_tickets'    => Ticket::whereHas('registration.event', fn($q) => $q->where('user_id', $userId))
                    ->count(),
            ]);
        });
    }
}
