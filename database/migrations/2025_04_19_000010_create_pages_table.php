<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('content');
            $table->string('language', 10)->default('es');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pages');
    }
};