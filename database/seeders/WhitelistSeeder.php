<?php

namespace Database\Seeders;

use App\Models\Whitelist;
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
        Whitelist::factory()->create([
            'phone' => '0775623646',
            'customer_name' => 'Kalema Edgar',
            'created_at' => now(),
        ]);

        Whitelist::factory()->create([
            'phone' => '0758102030',
            'customer_name' => 'Kimuli Flower',
            'created_at' => now(),
        ]);

        Whitelist::factory()->count(3)->create();
    }
}
