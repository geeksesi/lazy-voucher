<?php

namespace App\Models;

use App\Enums\VoucherAmountTypeEnum;
use App\Enums\VoucherUsageLimitTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'amount',
        'amount_type',
        'usage_limit',
        'usage_limit_type',
        'expired_at'
    ];

    protected $casts = [
        'amount_type' => VoucherAmountTypeEnum::class,
        'usage_limit_type' => VoucherUsageLimitTypeEnum::class,
        'expired_at' => 'datetime'
    ];

    // VoucherAble relations

    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'voucher_able', 'voucher_ables');
    }

    // Redeemer relations

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'redeemer', 'redeemers');
    }


    // normal relations

    public function usedVouchers(): HasMany
    {
        return $this->hasMany(UsedVoucher::class);
    }

    // scopes

    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expired_at')->orWhere('expired_at', ">=", now());
        });
    }

    public function scopeByRedeemers(Builder $query, Model $redeemer): Builder
    {
        return $query->where('id', function ($q) use ($redeemer) {
            $q->select('voucher_id')->from('redeemers')->where(function ($q) use ($redeemer) {
                $q->where('redeemer_id', $redeemer->id)->where("redeemer_type", get_class($redeemer));
            });
        });
    }

    public function scopeByVoucherAbles(Builder $query, Model $voucherAble): Builder
    {
        return $query->where('id', function ($q) use ($voucherAble) {
            $q->select('voucher_id')->from('voucher_ables')->where(function ($q) use ($voucherAble) {
                $q->where("voucher_able_id", $voucherAble->id)->where("voucher_able_type", get_class($voucherAble));
            });
        });
    }
}
