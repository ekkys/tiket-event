<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;

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

    public function download($token)
    {
        $ticket = Ticket::where('token', $token)->firstOrFail();
        
        if (!Storage::disk('public')->exists($ticket->qr_code_path)) {
            abort(404, 'QR Code tidak ditemukan');
        }

        return Storage::disk('public')->download(
            $ticket->qr_code_path, 
            'Ticket-' . $ticket->registration->registration_code . '.png'
        );
    }
}
