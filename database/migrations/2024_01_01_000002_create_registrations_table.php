<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            
            // Data Pribadi
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 20);
            $table->string('id_number', 20)->nullable();       // NIK / ID
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('institution')->nullable();          // Institusi/Asal
            $table->text('address')->nullable();
            
            // Status Pembayaran
            $table->enum('payment_status', ['pending', 'paid', 'free', 'failed', 'refunded'])
                  ->default('pending');
            $table->string('order_id')->unique()->nullable();   // Midtrans order ID
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->string('payment_method')->nullable();       // qris, transfer, etc
            $table->timestamp('paid_at')->nullable();
            
            // Tiket
            $table->boolean('ticket_generated')->default(false);
            $table->string('registration_code', 20)->unique(); // REG-XXXXXXXX
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk performa
            $table->index('email');
            $table->index('payment_status');
            $table->index('event_id');
            $table->index('registration_code');
        });
    }
    public function down(): void { Schema::dropIfExists('registrations'); }
};
