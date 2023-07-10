<?php

namespace App\Enums;

enum VoucherUsageLimitTypeEnum: string
{
    case PER_REDEEMER = 'per_redeemer';
    case ALL = 'all';
    case NO_LIMIT = 'no_limit';

    public function sanitize(?int $limit)
    {
        return match ($this) {
            self::NO_LIMIT => null,
            default => $this->sanitizeLimit($limit)
        };
    }

    public function hasLimit()
    {
        return match ($this) {
            self::NO_LIMIT => false,
            default => true
        };
    }

    private function sanitizeLimit(?int $limit): int
    {
        if (is_null($limit)) {
            return 0;
        }
        if ($limit < 0) {
            return 0;
        }
        return $limit;
    }
}
