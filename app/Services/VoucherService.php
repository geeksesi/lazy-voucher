<?php

namespace App\Services;

use App\Enums\VoucherAmountTypeEnum;
use App\Enums\VoucherStatusEnum;
use App\Enums\VoucherUsageLimitTypeEnum;
use App\Exceptions\VoucherIsInvalidException;
use App\Models\UsedVoucher;
use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

        DB::transaction(function () use ($voucher, $redeemers, $voucherAbles) {
            $this->storeRedeemers($voucher, $redeemers);
            $this->storeVoucherAbles($voucher, $voucherAbles);
        });

        return $voucher;
    }

    public function status(string $code, User $user, ?Model $redeemer = null, ?Model $voucherAble = null): VoucherStatusEnum
    {
        try {
            $voucherQuery = Voucher::byCode($code)->notExpired();

            if ($this->isValidRedeemer($redeemer)) {
                $voucherQuery->byRedeemers($redeemer);
            }
            if ($this->isValidVoucherAble($voucherAble)) {
                $voucherQuery->byVoucherAbles($voucherAble);
            }
            $voucher = $voucherQuery->firstOrFail();

            if (!$this->isUsableVoucher($voucher, $user)) {
                throw 'usage limit';
            }
            return VoucherStatusEnum::ACTIVE;
        } catch (\Throwable $th) {
            return VoucherStatusEnum::INVALID;
        }
    }

    public function use(string $code, User $user): UsedVoucher
    {
        $voucher = Voucher::byCode($code)->notExpired()->firstOrFail();
        if (!$this->isUsableVoucher($voucher, $user)) {
            throw new VoucherIsInvalidException();
        }

        return UsedVoucher::create(['user_id' => $user->id, 'voucher_id' => $voucher->id]);
    }

    private function isUsableVoucher(Voucher $voucher, $user): bool
    {
        if (!$voucher->usage_limit_type->hasLimit()) {
            return true;
        }

        $query = UsedVoucher::query();
        $query = match ($voucher->usage_limit_type) {
            VoucherUsageLimitTypeEnum::PER_REDEEMER => $query->voucherId($voucher->id)->userId($user->id),
            default => $query->voucherId($voucher->id),
        };

        return $query->count() < $voucher->usage_limit;
    }

    private function isValidRedeemer(?Model $redeemer): bool
    {
        if (is_null($redeemer)) {
            return false;
        }
        return in_array(\App\Concerns\RedeemerTrait::class, class_uses_recursive($redeemer));
    }

    private function isValidVoucherAble(?Model $voucherAble): bool
    {
        if (is_null($voucherAble)) {
            return false;
        }
        return in_array(\App\Concerns\VoucherAbleTrait::class, class_uses_recursive($voucherAble));
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

    private function storeRedeemers(Voucher $voucher, Collection|array $redeemers = [])
    {
        foreach ($redeemers as $redeemer) {
            if (!$this->isValidRedeemer($redeemer)) {
                abort(400, 'voucherAble should be used VoucherAbleTrait');
            }
            $redeemer->vouchers()->save($voucher);
        }
    }

    private function storeVoucherAbles(Voucher $voucher, Collection|array $voucherAbles = [])
    {
        foreach ($voucherAbles as $voucherAble) {
            if (!$this->isValidVoucherAble($voucherAble)) {
                abort(400, 'voucherAble should be used VoucherAbleTrait');
            }
            $voucherAble->vouchers()->save($voucher);
        }
    }
}
