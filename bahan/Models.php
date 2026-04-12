<?php
// ============================================================
// FILE: app/Models/Event.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Event extends Model
{
    protected $fillable = [
        'name', 'description', 'location', 'event_date',
        'price', 'is_free', 'quota', 'registered_count', 'is_active'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_free'    => 'boolean',
        'is_active'  => 'boolean',
        'price'      => 'decimal:2',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function isQuotaAvailable(): bool
    {
        return $this->registered_count < $this->quota;
    }

    public function getRemainingQuota(): int
    {
        return max(0, $this->quota - $this->registered_count);
    }

    // Cache event aktif selama 5 menit (kurangi query DB)
    public static function getActive(): self
    {
        return Cache::remember('active_event', 300, function () {
            return self::where('is_active', true)->firstOrFail();
        });
    }

    // Increment counter dengan atomic operation (aman concurrent)
    public function incrementRegistered(): void
    {
        self::where('id', $this->id)->increment('registered_count');
        Cache::forget('active_event');
        Cache::forget('event_stats_' . $this->id);
    }
}

// ============================================================
// FILE: app/Models/Registration.php
// ============================================================
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

// ============================================================
// FILE: app/Models/Ticket.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $fillable = [
        'registration_id', 'token', 'qr_code_path',
        'is_used', 'used_at', 'used_by', 'used_device'
    ];

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
}
