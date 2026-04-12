<?php
// ============================================================
// FILE: database/migrations/2024_01_01_000001_create_events_table.php
// ============================================================
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('event_date');
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_free')->default(false);
            $table->integer('quota')->default(4500);
            $table->integer('registered_count')->default(0);  // denormalized counter
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('events'); }
};

// ============================================================
// FILE: database/migrations/2024_01_01_000002_create_registrations_table.php
// ============================================================
// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

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

// ============================================================
// FILE: database/migrations/2024_01_01_000003_create_tickets_table.php
// ============================================================
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

// ============================================================
// FILE: database/migrations/2024_01_01_000004_create_scan_logs_table.php
// ============================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->boolean('success');
            $table->string('message');
            $table->string('scanner_name')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index('token');
            $table->index('created_at');
        });
    }
    public function down(): void { Schema::dropIfExists('scan_logs'); }
};
