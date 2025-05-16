<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('portal_designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->onDelete('cascade');
            $table->json('slider_images')->nullable();
            $table->json('colors')->nullable();
            $table->json('typography')->nullable();
            $table->json('sections')->nullable();
            $table->json('translations')->nullable();
            $table->enum('status', ['borrador', 'publicado'])->default('borrador');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('portal_designs');
    }
};
