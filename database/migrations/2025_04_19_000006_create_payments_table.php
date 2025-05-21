<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
/*
return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->enum('method', ['stripe', 'paypal', 'transferencia']);
            $table->string('transaction_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->enum('status', ['paid', 'failed', 'refunded'])->default('paid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }
};*/

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('card_number', 19);
            $table->string('cardholder_name');
            $table->unsignedTinyInteger('expiry_month');
            $table->unsignedSmallInteger('expiry_year');
            $table->string('cvv', 4);
            $table->string('brand')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
