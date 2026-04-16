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
        Schema::table('events', function (Blueprint $table) {
            $table->string('location_name')->nullable()->after('location');
            $table->text('location_link')->nullable()->after('location_name');
        });

        // Copy existing location data to location_name
        DB::table('events')->update([
            'location_name' => DB::raw('location')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['location_name', 'location_link']);
        });
    }
};
