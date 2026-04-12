<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Event extends Model
{
    protected $fillable = [
        'name', 'description', 'location', 'event_date',
        'price', 'is_free', 'quota', 'registered_count', 'is_active', 'image_path', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
