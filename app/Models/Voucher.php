<?php

namespace App\Models;

use App\Enums\VoucherAmountTypeEnum;
use App\Enums\VoucherUsageLimitTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'usage_limit_type' => VoucherUsageLimitTypeEnum::class
    ];


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
