<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(5, 15) as $counter) {
            User::factory()->count(rand(1, 5))->hasAttached(Voucher::factory()->count(rand(1, 5)))->create();
        }
    }
}
