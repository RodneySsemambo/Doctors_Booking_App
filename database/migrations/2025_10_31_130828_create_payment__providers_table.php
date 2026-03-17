<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment__providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment')->constrained('payments')->onDelete('cascade');
            $table->enum('provider_name', allowed: ['mtn_momo', 'airtel_money', 'flutterwave', 'stripe']);
            $table->string('api_key');
            $table->string('api_secret');
            $table->string('merchant_code')->nullable();
            $table->json('configuration');
            $table->boolean('is_active');
            $table->boolean('is_test_mode');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment__providers');
    }
};
