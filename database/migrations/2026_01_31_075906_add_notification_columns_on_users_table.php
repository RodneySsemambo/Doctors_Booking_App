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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('email_notifications')->default(true)->after('email_verified_at');
            $table->boolean('sms_notifications')->default(true)->after('email_notifications');
            $table->boolean('appointment_reminders')->default(true)->after('sms_notifications');
            $table->boolean('payment_notifications')->default(true)->after('appointment_reminders');
            $table->boolean('new_patient_notifications')->default(true)->after('payment_notifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_notifications',
                'sms_notifications',
                'appointment_reminders',
                'payment_notifications',
                'new_patient_notifications'
            ]);
        });
    }
};
