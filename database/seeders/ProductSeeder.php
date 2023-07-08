<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(5, 15) as $counter) {
            Product::factory()->count(rand(1, 5))->hasAttached(Voucher::factory()->count(rand(1, 5)))->create();
        }
    }
}
