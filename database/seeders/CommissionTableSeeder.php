<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('commissions')->insert([
            [
                'commission' => '10',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
