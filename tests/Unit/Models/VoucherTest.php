<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VoucherTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_voucher_by_code_and_it_should_not_be_expired(): void
    {
        $voucher = Voucher::factory()->create(['expired_at' => now()->addDay()]);
        $foundedVoucherByCode = Voucher::ByCode($voucher->code)->first();
        $foundedVoucherByCodeAndNotExpired = Voucher::notExpiredByCode($voucher->code)->first();

        $this->assertEquals($foundedVoucherByCode->id, $voucher->id);
        $this->assertEquals($foundedVoucherByCodeAndNotExpired->id, $voucher->id);
    }

    public function test_can_get_voucher_by_code_and_it_should_not_be_expired_even_if_expired_at_is_null(): void
    {
        $voucher = Voucher::factory()->create(['expired_at' => null]);
        $foundedVoucherByCode = Voucher::ByCode($voucher->code)->first();
        $foundedVoucherByCodeAndNotExpired = Voucher::notExpiredByCode($voucher->code)->first();

        $this->assertEquals($foundedVoucherByCode->id, $voucher->id);
        $this->assertEquals($foundedVoucherByCodeAndNotExpired->id, $voucher->id);
    }

    public function test_voucher_could_have_many_voucherable_like_product()
    {
        Voucher::factory()->count(3)->create();

        $voucher = Voucher::factory()->create();
        $product = Product::factory()->hasAttached($voucher)->create();

        $this->assertEquals(1, $voucher->products()->count());
        $this->assertDatabaseHas('voucher_ables', [
            'voucher_id' => $voucher->id,
            'voucher_able_id' => $product->id,
            'voucher_able_type' => Product::class
        ]);
    }
}
