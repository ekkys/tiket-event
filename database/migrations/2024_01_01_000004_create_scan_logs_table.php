<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
