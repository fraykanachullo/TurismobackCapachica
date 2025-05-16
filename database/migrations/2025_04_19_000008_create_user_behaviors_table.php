<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_behaviors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('last_seen_service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->json('preferred_categories')->nullable();
            $table->json('viewed_services')->nullable();
            $table->json('clicked_services')->nullable();
            $table->json('reserved_services')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_behaviors');
    }
};