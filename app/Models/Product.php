<?php

namespace App\Models;

use App\Concerns\VoucherAbleTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use VoucherAbleTrait;
}
