<?php

namespace App\Models;

use App\Enums\VoucherAmountTypeEnum;
use App\Enums\VoucherUsageLimitTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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

    public function scopeNotExpiredByCode(Builder $query, string $code): Builder
    {
        return $query->byCode($code)->where(function ($q) {
            $q->whereNull('expired_at')->orWhere('expired_at', ">=", now());
        });
    }
}
