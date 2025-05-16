<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/XXXX_XX_XX_create_locations_table.php
public function up()
{
    Schema::create('locations', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->enum('type', ['comunidad','centro_poblado']);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
