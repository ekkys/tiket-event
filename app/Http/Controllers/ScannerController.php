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
        $token = $request->input('token');
        $scannerName = auth()->user()->name;

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid'], 400);
        }

        // Cari tiket - bisa lewat token (QR) atau registration_code (Input Manual)
        $ticket = Ticket::where('token', $token)
            ->orWhereHas('registration', function ($q) use ($token) {
                $q->where('registration_code', $token);
            })
            ->with([
                'registration:id,full_name,email,phone,institution,id_number,gender,address,registration_code,event_id,payment_status',
                'registration.event:id,name,event_date,user_id'
            ])
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'status' => 'invalid',
                'message' => '❌ Tiket tidak valid atau tidak ditemukan',
            ]);
        }

        // Validasi: Hanya pembuat event yang bisa scan
        if ($ticket->registration->event->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'status' => 'unauthorized',
                'message' => '❌ Anda tidak memiliki akses untuk menscan tiket event ini.',
            ], 403);
        }

        if (!$ticket->registration->isPaid()) {
            return response()->json([
                'success' => false,
                'status' => 'unpaid',
                'message' => '💳 Tiket belum lunas pembayaran',
            ]);
        }

        if ($ticket->is_used) {
            return response()->json([
                'success' => false,
                'status' => 'used',
                'message' => '⚠️ Tiket ini SUDAH DIGUNAKAN',
                'used_at' => $ticket->used_at?->format('d M Y H:i'),
                'used_by' => $ticket->used_by,
                'attendee' => [
                    'name' => $ticket->registration->full_name,
                    'email' => $ticket->registration->email,
                    'phone' => $ticket->registration->phone,
                    'institution' => $ticket->registration->institution,
                    'id_number' => $ticket->registration->id_number,
                    'gender' => $ticket->registration->gender == 'male' ? 'Laki-laki' : 'Perempuan',
                    'address' => $ticket->registration->address,
                    'registration_code' => $ticket->registration->registration_code,
                ],
                'event' => $ticket->registration->event->name,
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => 'valid',
            'message' => '✅ TIKET DITEMUKAN - Silakan Cek Identitas',
            'attendee' => [
                'name' => $ticket->registration->full_name,
                'email' => $ticket->registration->email,
                'phone' => $ticket->registration->phone,
                'institution' => $ticket->registration->institution,
                'id_number' => $ticket->registration->id_number,
                'gender' => $ticket->registration->gender == 'male' ? 'Laki-laki' : 'Perempuan',
                'address' => $ticket->registration->address,
                'registration_code' => $ticket->registration->registration_code,
            ],
            'event' => $ticket->registration->event->name,
            'token' => $token,
        ]);
    }

    public function confirm(Request $request)
    {
        $token = $request->input('token');
        $scannerName = auth()->user()->name;

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid'], 400);
        }

        $ticket = Ticket::where('token', $token)
            ->orWhereHas('registration', function ($q) use ($token) {
                $q->where('registration_code', $token);
            })
            ->with(['registration.event'])
            ->first();

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan'], 404);
        }

        if ($ticket->registration->event->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        if ($ticket->is_used) {
            return response()->json(['success' => false, 'message' => 'Tiket sudah digunakan'], 400);
        }

        $updated = Ticket::where('id', $ticket->id)
            ->where('is_used', false)
            ->update([
                'is_used' => true,
                'used_at' => now(),
                'used_by' => $scannerName,
                'used_device' => $request->userAgent(),
            ]);

        if (!$updated) {
            return response()->json(['success' => false, 'message' => 'Tiket baru saja digunakan oleh petugas lain'], 400);
        }

        $this->logScan($token, true, 'Berhasil masuk (Verifikasi Manual)', $scannerName, $request->ip(), $ticket->id);

        return response()->json([
            'success' => true,
            'message' => '✅ Verifikasi Berhasil!',
            'checked_in' => now()->format('d M Y H:i:s'),
        ]);
    }

    private function logScan(string $token, bool $success, string $message, string $scanner, string $ip, ?int $ticketId): void
    {
        // Insert log secara async agar scan tidak lambat
        dispatch(function () use ($token, $success, $message, $scanner, $ip, $ticketId) {
            \App\Models\ScanLog::create([
                'token' => $token,
                'ticket_id' => $ticketId,
                'success' => $success,
                'message' => $message,
                'scanner_name' => $scanner,
                'ip_address' => $ip,
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
                'total_tickets' => Ticket::whereHas('registration.event', fn($q) => $q->where('user_id', $userId))
                    ->count(),
            ]);
        });
    }
}
