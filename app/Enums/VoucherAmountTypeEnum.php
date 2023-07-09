<?php

namespace App\Enums;

enum VoucherAmountTypeEnum: string
{
    case PERCENTAGE = 'percentage';
    case ABSOLUTE = 'absolute';

    public function sanitize(int $amount)
    {
        return match ($this) {
            self::ABSOLUTE => $amount,
            self::PERCENTAGE => $this->sanitizePercentage($amount)
        };
    }

    private function sanitizePercentage(int $amount): int
    {
        if ($amount > 100) {
            return 100;
        }
        if ($amount < 0) {
            return 0;
        }
        return $amount;
    }
}
