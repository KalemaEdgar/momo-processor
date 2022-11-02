<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WhitelistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('whitelist')->insert([
            'phone' => '0775623646',
            'customer_name' => 'Kalema Edgar',
            'created_at' => now(),
        ]);

        DB::table('whitelist')->insert([
            'phone' => '0758102030',
            'customer_name' => 'Kimuli Flower',
            'created_at' => now(),
        ]);

        for ($i=0; $i < 3; $i++) {
            DB::table('whitelist')->insert([
                'phone' => fake()->phoneNumber(),
                'customer_name' => fake()->name(),
                'created_at' => now(),
            ]);
        }
    }
}
