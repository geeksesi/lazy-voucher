<?php

namespace Database\Factories;

use App\Enums\VoucherAmountTypeEnum;
use App\Enums\VoucherUsageLimitTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => str_replace(" ", "_", fake()->realText()),
            'amount' => rand(10, 90),
            'amount_type' => Arr::random(VoucherAmountTypeEnum::cases()),
            'usage_limit' => rand(1, 90),
            'usage_limit_type' => Arr::random(VoucherUsageLimitTypeEnum::cases()),
            'expired_at' => now()->subDays(rand(1, 10))->addDays(rand(1, 10))
        ];
    }
}
