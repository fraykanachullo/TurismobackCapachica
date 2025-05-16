<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->string('trade_name')->nullable();
            $table->string('service_type');
            $table->string('contact_email');
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->string('ruc', 11)->unique(); // RUC peruano ti
            $table->string('logo_url')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');

            $table->enum('status', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('companies');
    }
};
