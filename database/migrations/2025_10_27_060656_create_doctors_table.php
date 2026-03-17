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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->OnDelete('cascade');
            $table->foreignId('specialization_id')->constrained('specializations')->OnDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('license_number')->unique();
            $table->integer('years_of_experience');
            $table->text('qualification');
            $table->text('bio');
            $table->string('profile_photo');
            $table->decimal('consultation_fee');
            $table->decimal('rating')->default(0.0);
            $table->integer('total_reviews')->default(0);
            $table->string('hospital_affiliation');
            $table->boolean('video_consultation_available');
            $table->json('languages_spoken');
            $table->boolean('is_verified');
            $table->boolean('is_available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
