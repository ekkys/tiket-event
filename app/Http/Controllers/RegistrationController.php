<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Jobs\GenerateTicketJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class RegistrationController extends Controller
{
    public function showForm(Event $event = null)
    {
        if (!$event || !$event->exists) {
            $event = Event::where('is_active', true)->first();
        }
        
        if (!$event || !$event->is_active) {
            return view('registration.closed');
        }

        // Cek apakah pendaftaran dibuka/ditutup berdasarkan tanggal
        if (!$event->isBookingOpen()) {
            return redirect()->route('events.show', $event->id)
                ->with('error', 'Pendaftaran untuk event ini sedang ditutup (cek jadwal buka/tutup).');
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
            'event_id'       => 'required|exists:events,id',
            'full_name'      => 'required|string|max:100',
            'email'          => 'required|email|max:150',
            'phone'          => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
            'id_number'      => 'required|string|size:16',
            'gender'         => 'required|in:male,female',
            'institution'    => 'required|string|max:100',
            'address'        => 'required|string|max:300',
            'terms_accepted' => 'required|accepted', // Validasi centang S&K
        ], [
            'email.unique'            => 'Email ini sudah terdaftar untuk event ini.',
            'phone.unique'            => 'Nomor telepon ini sudah terdaftar untuk event ini.',
            'id_number.required'      => 'NIK / No. KTP wajib diisi.',
            'id_number.size'          => 'NIK harus berjumlah 16 digit.',
            'gender.required'         => 'Jenis kelamin wajib dipilih.',
            'institution.required'    => 'Asal instansi wajib diisi.',
            'address.required'        => 'Alamat wajib diisi.',
            'terms_accepted.required' => 'Anda harus menyetujui Syarat & Ketentuan untuk mendaftar.',
            'terms_accepted.accepted' => 'Anda harus menyetujui Syarat & Ketentuan untuk mendaftar.',
        ]);

        // Cek email & nomor hp sudah daftar per event secara manual
        $existing = Registration::where('event_id', $validated['event_id'])
            ->where(function($query) use ($validated) {
                $query->where('email', $validated['email'])
                      ->orWhere('phone', $validated['phone']);
            })->first();

        if ($existing) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau Nomor Telepon sudah terdaftar sebelumnya.'
                ], 422);
            }
            return redirect()->route('ticket.show', $existing->ticket?->token ?? $existing->registration_code)
                ->with('info', 'Anda sudah terdaftar sebelumnya.');
        }


        $event = Event::findOrFail($validated['event_id']);

        // Cek ulang booking window saat submit
        if (!$event->isBookingOpen()) {
            return redirect()->route('events.show', $event->id)
                ->with('error', 'Maaf, waktu pendaftaran sudah berakhir.');
        }

        // Atomic check quota + insert menggunakan DB transaction
        return DB::transaction(function () use ($validated, $event, $request, $existing) {
            // Lock row untuk hindari race condition
            $currentEvent = Event::lockForUpdate()->find($event->id);

            if (!$currentEvent->isQuotaAvailable()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maaf, kuota sudah penuh!'
                    ], 422);
                }
                return redirect()->route('registration.full')
                    ->with('error', 'Maaf, kuota sudah penuh!');
            }

            if ($existing) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda sudah terdaftar sebelumnya.',
                        'redirect' => route('ticket.show', $existing->ticket?->token ?? $existing->registration_code)
                    ], 422);
                }
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
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'registration_code' => $registration->registration_code,
                        'redirect' => route('registration.success', $registration->registration_code)
                    ]);
                }
                return redirect()->route('registration.success', $registration->registration_code);
            }

            // Buat order Midtrans
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('payment.show', $registration->registration_code)
                ]);
            }
            return redirect()->route('payment.show', $registration->registration_code);
        });
    }

    /**
     * Cek status antrian tiket (polling AJAX)
     */
    public function checkStatus($code)
    {
        $registration = Registration::where('registration_code', $code)->first();

        if (!$registration) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        if ($registration->ticket_generated) {
            return response()->json([
                'status'   => 'completed',
                'redirect' => route('registration.success', $code)
            ]);
        }

        // Hitung estimasi antrian (hanya job yang belum dikerjakan di tabel jobs)
        // Note: Ini estimasi sederhana berdasarkan jumlah job yang ada di queue
        $queueCount = DB::table('jobs')->count();

        return response()->json([
            'status'     => 'processing',
            'queue_info' => $queueCount > 0 ? $queueCount : 1
        ]);
    }

    public function success($code)
    {
        $registration = Registration::where('registration_code', $code)
            ->with(['ticket', 'event'])
            ->firstOrFail();

        return view('registration.success', compact('registration'));
    }
}
