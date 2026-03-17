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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number')->unique()->nullable();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->date('appointment_date');
            $table->time('appointment_time')->nullable();
            $table->enum('appointment_type', allowed: ['in-person', 'video', 'phone', 'virtual']);
            $table->enum('status', allowed: ['confirmed', 'pending', 'cancelled', 'no_show', 'completed']);
            $table->text('reason_for_visit');
            $table->text('symptoms')->nullable();
            $table->text('notes')->nullable();
            $table->enum('payment_status', allowed: ['pending', 'paid', 'refunded', 'unpaid'])->default('pending');
            $table->decimal('consultation_fee');
            $table->enum('cancelled_by', allowed: ['doctor', 'patient', 'system'])->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->date('cancelled_at')->nullable();
            $table->date('reminded_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
