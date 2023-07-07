<?php

namespace App\Enums;

enum VoucherUsageLimitTypeEnum: string
{
    case PER_REDEEMER = 'per_redeemer';
    case ALL = 'all';
    case NO_LIMIT = 'no_limit';
}
