<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\TicketMail;

class Ticket extends Model
{
    protected $fillable = [
        'registration_id', 'token', 'qr_code_path',
        'is_used', 'used_at', 'used_by', 'used_device'
    ];

    protected static function booted()
    {
        static::created(function ($ticket) {
            Mail::to($ticket->registration->email)
                ->queue(new TicketMail($ticket->registration, $ticket));
        });
    }

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class)->with('event');
    }

    public static function generateToken(): string
    {
        do {
            $token = Str::random(32) . '-' . time();
            $hash  = hash('sha256', $token);
        } while (self::where('token', $hash)->exists());
        return $hash;
    }

    public function getVerifyUrlAttribute(): string
    {
        return route('ticket.verify', $this->token);
    }

    public function getQrUrlAttribute(): string
    {
        if ($this->qr_code_path && Storage::disk('public')->exists($this->qr_code_path)) {
            return Storage::disk('public')->url($this->qr_code_path);
        }
        return ""; // Should not happen ideally
    }
}
