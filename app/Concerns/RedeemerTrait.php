<?php

namespace App\Concerns;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait RedeemerTrait
{
    public function vouchers(): MorphToMany
    {
        return $this->morphToMany(Voucher::class, "redeemer", 'redeemers');
    }
}
