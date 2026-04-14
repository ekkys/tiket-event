<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $blueprint) {
            $blueprint->dateTime('booking_starts_at')->nullable()->after('event_date');
            $blueprint->dateTime('booking_ends_at')->nullable()->after('booking_starts_at');
            $blueprint->text('terms_and_conditions')->nullable()->after('booking_ends_at');
            $blueprint->text('highlights')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['booking_starts_at', 'booking_ends_at', 'terms_and_conditions', 'highlights']);
        });
    }
};
