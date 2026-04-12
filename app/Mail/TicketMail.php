<?php

namespace App\Mail;

use App\Models\Registration;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Registration $registration,
        public Ticket $ticket,
    ) {
        // Default queue
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎟️ Tiket ' . $this->registration->event->name . ' - ' . $this->registration->full_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket',
        );
    }
}
