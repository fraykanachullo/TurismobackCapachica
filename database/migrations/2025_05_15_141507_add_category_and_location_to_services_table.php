<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('services', function (Blueprint $table) {
            // Asegúrate de haber corrido antes las migraciones de locations y categories
            $table->foreignId('category_id')
                  ->after('company_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('location_id')
                  ->after('category_id')
                  ->nullable()
                  ->constrained('locations')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');

            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
        });
    }
};
