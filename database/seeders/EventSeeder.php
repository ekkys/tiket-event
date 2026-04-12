<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::create([
            'name'       => env('EVENT_NAME', 'Tiket Event 2025'),
            'event_date' => env('EVENT_DATE', '2025-08-01 08:00:00'),
            'price'      => env('EVENT_PRICE', 50000),
            'is_free'    => env('EVENT_IS_FREE', false),
            'quota'      => env('EVENT_QUOTA', 4500),
            'is_active'  => true,
        ]);
    }
}
