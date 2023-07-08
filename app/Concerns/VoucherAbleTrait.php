<?php

namespace App\Concerns;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait VoucherAbleTrait
{
    public function vouchers(): MorphToMany
    {
        return $this->morphToMany(Voucher::class, "voucher_able", 'voucher_ables');
    }
}
