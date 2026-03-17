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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->decimal('amount');
            $table->enum('currency', allowed: ['UGX', 'USD'])->default('UGX');
            $table->string('phone_number')->nullable();
            $table->enum('payment_method', allowed: ['mtn_mobile_money', 'airtel_mobile_money', 'card', 'cash', 'flutterwave']);
            $table->enum('payment_provider', allowed: ['mtn', 'airtel', 'flutterwave', 'cash', 'stripe']);
            $table->enum('status', allowed: ['pending', 'refunded', 'processing', 'completed', 'failed']);
            $table->string('transaction_reference')->unique();
            $table->string('provider_reference')->nullable();
            $table->json('provider_response')->nullable();
            $table->dateTime('initiated_at');
            $table->dateTime('completed_at')->nullable();
            $table->text('failed_reason')->nullable();
            $table->decimal('refund_amount')->nullable();
            $table->dateTime('refunded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
