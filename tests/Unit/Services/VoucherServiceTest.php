<?php

namespace Tests\Unit\Services;

use App\Enums\VoucherAmountTypeEnum;
use App\Enums\VoucherUsageLimitTypeEnum;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherServiceTest extends TestCase
{
    use RefreshDatabase;
    protected VoucherService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(VoucherService::class);
    }

    public function test_can_make_voucher_with_valid_data()
    {
        $voucher = $this->service->create(50, VoucherAmountTypeEnum::PERCENTAGE, now()->addDay(), "JJS");

        $this->assertDatabaseCount(app(Voucher::class)->getTable(), 1);
    }

    public function test_can_make_voucher_with_invalid_amount_when_its_percentage()
    {
        $voucher = $this->service->create(110, VoucherAmountTypeEnum::PERCENTAGE, now()->addDay(), "JJS");
        $this->assertEquals(100, $voucher->amount);

        $voucher = $this->service->create(-10, VoucherAmountTypeEnum::PERCENTAGE, now()->addDay(), "JJS2");
        $this->assertEquals(0, $voucher->amount);

        $this->assertDatabaseCount(app(Voucher::class)->getTable(), 2);
    }

    public function test_can_make_voucher_with_invalid_usage_limit()
    {
        $voucher = $this->service->create(10, VoucherAmountTypeEnum::PERCENTAGE, now()->addDay(), "12sa", -10, VoucherUsageLimitTypeEnum::PER_REDEEMER);
        $this->assertEquals(0, $voucher->usage_limit);

        $voucher = $this->service->create(10, VoucherAmountTypeEnum::PERCENTAGE, now()->addDay(), "12sa1", -10, VoucherUsageLimitTypeEnum::NO_LIMIT);
        $this->assertEquals(null, $voucher->usage_limit);

        $this->assertDatabaseCount(app(Voucher::class)->getTable(), 2);
    }

    public function test_can_make_voucher_with_random_code()
    {
        foreach (range(1, 5) as $counter) {
            $voucher = $this->service->create(10, VoucherAmountTypeEnum::PERCENTAGE, now()->addDay());
            $this->assertNotEmpty($voucher->code);
        }

        $this->assertDatabaseCount(app(Voucher::class)->getTable(), 5);
    }
}
