<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsedVoucher extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'voucher_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function scopeVoucherId(Builder $query, int $voucherId): Builder
    {
        return $query->where('voucher_id', $voucherId);
    }

    public function scopeUserId(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
