<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Event extends Model
{
    protected $fillable = [
        'name', 'description', 'highlights', 'location', 'event_date',
        'booking_starts_at', 'booking_ends_at', 'terms_and_conditions',
        'price', 'is_free', 'quota', 'registered_count', 'is_active', 'image_path', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'event_date'        => 'datetime',
        'booking_starts_at' => 'datetime',
        'booking_ends_at'   => 'datetime',
        'is_free'           => 'boolean',
        'is_active'         => 'boolean',
        'price'             => 'decimal:2',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function isQuotaAvailable(): bool
    {
        return $this->registered_count < $this->quota;
    }

    public function isBookingOpen(): bool
    {
        $now = now();
        
        if ($this->booking_starts_at && $now->lt($this->booking_starts_at)) {
            return false;
        }

        if ($this->booking_ends_at && $now->gt($this->booking_ends_at)) {
            return false;
        }

        return true;
    }

    public function getBookingStatus(): string
    {
        $now = now();

        if (!$this->is_active) return 'Nonaktif';
        if ($this->booking_starts_at && $now->lt($this->booking_starts_at)) return 'Belum Buka';
        if ($this->booking_ends_at && $now->gt($this->booking_ends_at)) return 'Sudah Tutup';
        if (!$this->isQuotaAvailable()) return 'Kuota Penuh';

        return 'Buka';
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
