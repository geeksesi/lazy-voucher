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
        Schema::create('voucher_ables', function (Blueprint $table) {
            $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('CASCADE');
            $table->morphs('voucher_able');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_voucher_able');
    }
};
