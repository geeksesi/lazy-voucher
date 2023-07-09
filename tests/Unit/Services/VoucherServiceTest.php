<?php

namespace Tests\Unit\Services;

use App\Enums\VoucherAmountTypeEnum;
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
}
