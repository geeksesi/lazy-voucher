<?php

namespace Tests\Unit\Services;

use App\Enums\VoucherAmountTypeEnum;
use App\Enums\VoucherStatusEnum;
use App\Enums\VoucherUsageLimitTypeEnum;
use App\Models\Product;
use App\Models\User;
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

    public function test_voucher_could_have_some_redeemers_like_user()
    {
        $users = User::factory()->count(3)->create();
        $voucher = $this->service->create(10, VoucherAmountTypeEnum::PERCENTAGE, now()->addDay(), null, null, VoucherUsageLimitTypeEnum::NO_LIMIT, $users);
        $this->assertDatabaseCount(app(Voucher::class)->getTable(), 1);
        $this->assertDatabaseCount('redeemers', 3);
        $this->assertDatabaseHas('redeemers', [
            'voucher_id' => $voucher->id,
            'redeemer_id' => $users[0]->id,
            'redeemer_type' => User::class
        ]);
    }

    public function test_voucher_could_have_some_voucher_ables_like_product()
    {
        $products = Product::factory()->count(3)->create();
        $voucher = $this->service->create(10, VoucherAmountTypeEnum::PERCENTAGE, now()->addDay(), null, null, VoucherUsageLimitTypeEnum::NO_LIMIT, [], $products);
        $this->assertDatabaseCount(app(Voucher::class)->getTable(), 1);
        $this->assertDatabaseCount('voucher_ables', 3);
        $this->assertDatabaseHas('voucher_ables', [
            'voucher_id' => $voucher->id,
            'voucher_able_id' => $products[0]->id,
            'voucher_able_type' => Product::class
        ]);
    }

    public function test_active_global_voucher_should_return_active_on_status()
    {
        $voucher = $this->service->create(10, VoucherAmountTypeEnum::PERCENTAGE, now()->addDay());

        $this->assertEquals(VoucherStatusEnum::ACTIVE, $this->service->status($voucher->code));
    }

    public function test_expired_voucher_should_return_invalid()
    {
        $voucher = Voucher::factory()->create(['expired_at' => now()->subDay()]);

        $this->assertEquals(VoucherStatusEnum::INVALID, $this->service->status($voucher->code));
    }

    public function test_active_voucher_with_special_voucher_able_should_return_active()
    {
        $products = Product::factory()->count(3)->create();

        $voucher = $this->service->create(10, VoucherAmountTypeEnum::PERCENTAGE, now()->addDay(), null, null, VoucherUsageLimitTypeEnum::NO_LIMIT, [], $products);

        $this->assertEquals(VoucherStatusEnum::ACTIVE, $this->service->status($voucher->code, null, $products[0]));
    }

    public function test_active_voucher_with_special_voucher_able_and_redeemer_should_return_active()
    {
        $products = Product::factory()->count(3)->create();
        $users = User::factory()->count(3)->create();


        $voucher = $this->service->create(10, VoucherAmountTypeEnum::PERCENTAGE, now()->addDay(), null, null, VoucherUsageLimitTypeEnum::NO_LIMIT, $users, $products);

        $this->assertEquals(VoucherStatusEnum::ACTIVE, $this->service->status($voucher->code, $users[1], $products[0]));
    }

    public function test_active_voucher_with_wrong_special_voucher_able_or_redeemer_should_return_invalid()
    {
        $products = Product::factory()->count(3)->create();
        $users = User::factory()->count(3)->create();


        $voucher = $this->service->create(10, VoucherAmountTypeEnum::PERCENTAGE, now()->addDay(), null, null, VoucherUsageLimitTypeEnum::NO_LIMIT, $users, $products);

        $this->assertEquals(VoucherStatusEnum::INVALID, $this->service->status($voucher->code, User::factory()->create(), $products[0]));
        $this->assertEquals(VoucherStatusEnum::INVALID, $this->service->status($voucher->code, $users[1], Product::factory()->create()));
    }
}
