<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();              // Token unik untuk QR
            $table->string('qr_code_path')->nullable();         // Path file QR code
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->string('used_by')->nullable();              // Nama petugas yang scan
            $table->string('used_device')->nullable();          // Device petugas
            $table->timestamps();
            
            // Index kritis untuk performa scan
            $table->index('token');
            $table->index('is_used');
        });
    }
    public function down(): void { Schema::dropIfExists('tickets'); }
};
