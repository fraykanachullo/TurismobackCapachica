<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // Cambia el ENUM para que acepte 'inactive' además de los previos
    \DB::statement("
        ALTER TABLE `promotions`
        MODIFY `status`
        ENUM('pending','active','expired','inactive')
        NOT NULL
        DEFAULT 'pending'
    ");
}
    /**
     * Reverse the migrations.
     */
    public function down()
{
    \DB::statement("
        ALTER TABLE `promotions`
        MODIFY `status`
        ENUM('pending','active','expired')
        NOT NULL
        DEFAULT 'pending'
    ");
}
};
