<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlacklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('blacklist')->insert([
            'phone' => '0772123456',
            'customer_name' => 'Arms Dealer',
            'created_at' => now(),
        ]);

        DB::table('blacklist')->insert([
            'phone' => '0754098765',
            'customer_name' => 'Not honest person',
            'created_at' => now(),
        ]);

        for ($i=0; $i < 3; $i++) {
            DB::table('blacklist')->insert([
                'phone' => fake()->phoneNumber(),
                'customer_name' => fake()->name(),
                'created_at' => now(),
            ]);
        }
    }
}
