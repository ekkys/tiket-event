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
        Schema::table('registrations', function (Blueprint $table) {
            // Drop existing indexes if they exist (from previous migration)
            // $table->dropIndex(['email']); 
            // $table->dropIndex(['event_id']);

            // Add unique composite indexes
            $table->unique(['event_id', 'email'], 'registrations_event_email_unique');
            $table->unique(['event_id', 'phone'], 'registrations_event_phone_unique');
            $table->unique(['event_id', 'id_number'], 'registrations_event_id_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropUnique('registrations_event_email_unique');
            $table->dropUnique('registrations_event_phone_unique');
            $table->dropUnique('registrations_event_id_number_unique');
        });
    }
};
