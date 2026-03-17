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
        Schema::create('medical__records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->enum('record_type', allowed: ['lab_result', 'consultation_note', 'vaccination', 'imaging']);
            $table->string('title');
            $table->text('description');
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->date('recorded_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical__records');
    }
};
