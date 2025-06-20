<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            // Relaciones polimórficas
            $table->morphs('itineraryable');
            // Opcional: orden por día y hora
            $table->unsignedInteger('day_number')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            // Detalle del itinerario
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('itineraries');
    }
};
