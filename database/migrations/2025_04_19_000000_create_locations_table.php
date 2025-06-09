<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();

            // Campos básicos
            $table->string('name');
            $table->enum('type', ['comunidad', 'centro_poblado']);

            // Descripciones y metadata
            $table->text('descripcion_corta')->nullable();
            $table->text('descripcion_larga')->nullable();
            $table->text('atractivos')->nullable();
            $table->unsignedInteger('habitantes')->nullable();

            // Estado y media
            $table->enum('estado', ['activa', 'inactiva'])->default('activa');
            $table->string('imagen')->nullable();
            $table->json('galeria')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
