<?php

namespace App\Enums;

enum VoucherAmountTypeEnum: string
{
    case PERCENTAGE = 'percentage';
    case ABSOLUTE = 'absolute';
}
