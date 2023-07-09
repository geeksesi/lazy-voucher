<?php

namespace App\Services;

use App\Enums\VoucherAmountTypeEnum;
use App\Enums\VoucherUsageLimitTypeEnum;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class VoucherService
{
    public function create(int $amount, VoucherAmountTypeEnum $amountType, Carbon $expiredAt, ?string $code = null, ?int $usageLimit = null, VoucherUsageLimitTypeEnum $limitType = VoucherUsageLimitTypeEnum::NO_LIMIT, Collection|array $redeemers = [], Collection|array $voucherAbles = []): ?Voucher
    {
        $data = [];
        $data['code'] = $this->makeCode($code);
        $data['amount'] = $amountType->sanitize($amount);
        $data['amount_type'] = $amountType;
        $data['usage_limit'] = $limitType->sanitize($usageLimit);
        $data['usage_limit_type'] = $limitType;
        $data['expired_at'] = $this->sanitizeExpiredAt($expiredAt);

        $voucher = Voucher::create($data);

        return $voucher;
    }

    private function sanitizeExpiredAt(Carbon $expiredAt): Carbon
    {
        if ($expiredAt->lessThan(now())) {
            return now();
        }
        return $expiredAt;
    }

    private function makeCode(?string $code = null): string
    {
        if ($code) {
            return $this->sanitizeCode($code);
        }
        return $this->sanitizeCode(Str::random(8));
    }

    private function sanitizeCode(string $code): string
    {
        return str_replace(" ", "-", $code);
    }
}
