<?php

namespace App\Exceptions;

use Exception;

class VoucherIsInvalidException extends Exception
{
    protected $message = 'The voucher is invalid.';
}
