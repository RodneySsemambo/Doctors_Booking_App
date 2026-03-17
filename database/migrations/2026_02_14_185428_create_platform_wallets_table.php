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
        Schema::create('platform_wallets', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('doctor_earnings', 15, 2)->default(0);
            $table->decimal('platform_earnings', 15, 2)->default(0);
            $table->decimal('total_withdrawn', 15, 2)->default(0);
            $table->decimal('pending_withdrawals', 15, 2)->default(0);
            $table->decimal('available_balance', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_wallets');
    }
};
