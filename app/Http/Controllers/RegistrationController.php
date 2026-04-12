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
            'event_id'    => 'required|exists:events,id',
            'full_name'   => 'required|string|max:100',
            'email'       => 'required|email|max:150',
            'phone'       => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
            'id_number'   => 'nullable|string|max:20',
            'gender'      => 'nullable|in:male,female',
            'institution' => 'nullable|string|max:100',
            'address'     => 'nullable|string|max:300',
        ]);

        $event = Event::findOrFail($validated['event_id']);

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
