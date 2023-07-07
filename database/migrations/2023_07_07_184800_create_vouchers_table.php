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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string("code")->unique();
            $table->integer("amount");
            /** @var App\Enums\VoucherAmountTypeEnum */
            $table->string("amount_type");

            $table->integer("usage_limit");
            /** @var App\Enums\VoucherAmountTypeEnum */
            $table->string("usage_limit_type");
            $table->dateTime('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
