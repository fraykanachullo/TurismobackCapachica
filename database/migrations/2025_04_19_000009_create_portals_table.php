<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('portals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subdomain')->unique();
            $table->string('default_language')->default('es');
            $table->string('logo_url')->nullable();
            $table->string('primary_color')->default('#3490dc');
            $table->string('secondary_color')->default('#6c757d');
            $table->string('font_family')->default('Inter');
            $table->string('layout_template')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('portals');
    }
};