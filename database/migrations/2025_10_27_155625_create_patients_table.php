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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('profile_photo');
            $table->string('city');
            $table->string('address');
            $table->string('country');
            $table->date('date_of_birth');
            $table->enum('gender', allowed: ['male', 'female', 'other']);
            $table->enum('blood_group', allowed: ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']);
            $table->json('medical_history');
            $table->json('allergies');
            $table->string('emergency_phone');
            $table->string('emergency_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
