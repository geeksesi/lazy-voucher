<?php

namespace Tests\Unit\Models;

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
}
