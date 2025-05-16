<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['tour', 'hospedaje', 'gastronomia', 'experiencia']);
            $table->text('description');
            $table->string('location');
            $table->decimal('price', 10, 2);
            $table->text('policy_cancellation')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->string('duration')->nullable();
            $table->enum('status', ['pending', 'active', 'paused', 'rejected'])->default('pending');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('services');
    }
};