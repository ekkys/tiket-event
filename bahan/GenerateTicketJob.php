<?php
// ============================================================
// FILE: app/Jobs/GenerateTicketJob.php
// ============================================================
namespace App\Jobs;

use App\Models\Registration;
use App\Models\Ticket;
use App\Mail\TicketMail;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GenerateTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public Registration $registration)
    {
        $this->onQueue('tickets');
    }

    public function handle(): void
    {
        // Cegah generate duplikat
        if ($this->registration->ticket()->exists()) {
            Log::info('Ticket already exists', ['reg_id' => $this->registration->id]);
            return;
        }

        // Generate token unik
        $token = Ticket::generateToken();

        // URL yang akan di-encode ke QR code
        $verifyUrl = route('scanner.verify.page', ['token' => $token]);

        // Generate QR Code
        $qrCode = new QrCode(
            data: $verifyUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 400,
            margin: 20,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255),
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Simpan QR code ke storage
        $path     = 'qrcodes/' . $this->registration->registration_code . '.png';
        Storage::disk('public')->put($path, $result->getString());

        // Simpan ticket ke database
        $ticket = Ticket::create([
            'registration_id' => $this->registration->id,
            'token'           => $token,
            'qr_code_path'    => $path,
        ]);

        // Update status registration
        $this->registration->update(['ticket_generated' => true]);

        // Kirim email tiket
        Mail::to($this->registration->email)
            ->queue(new TicketMail($this->registration, $ticket));

        Log::info('Ticket generated', [
            'registration_id' => $this->registration->id,
            'token'           => $token,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateTicketJob failed', [
            'registration_id' => $this->registration->id,
            'error'           => $exception->getMessage(),
        ]);
    }
}

// ============================================================
// FILE: app/Mail/TicketMail.php
// ============================================================
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
        $this->onQueue('emails');
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
