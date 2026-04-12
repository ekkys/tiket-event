<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Registration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_id', 'full_name', 'email', 'phone', 'id_number',
        'gender', 'institution', 'address', 'payment_status',
        'order_id', 'amount_paid', 'payment_method', 'paid_at',
        'ticket_generated', 'registration_code'
    ];

    protected $casts = [
        'paid_at'          => 'datetime',
        'ticket_generated' => 'boolean',
        'amount_paid'      => 'decimal:2',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }

    public static function generateRegistrationCode(): string
    {
        do {
            $code = 'REG-' . strtoupper(Str::random(8));
        } while (self::where('registration_code', $code)->exists());
        return $code;
    }

    public function isPaid(): bool
    {
        return in_array($this->payment_status, ['paid', 'free']);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount_paid, 0, ',', '.');
    }
}
