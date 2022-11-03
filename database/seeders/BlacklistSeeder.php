<?php

namespace Database\Seeders;

use App\Models\Blacklist;
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
        Blacklist::factory()->create([
            'phone' => '0772123456',
            'customer_name' => 'Arms Dealer',
            'created_at' => now(),
        ]);

        Blacklist::factory()->create([
            'phone' => '0754098765',
            'customer_name' => 'Not honest person',
            'created_at' => now(),
        ]);

        Blacklist::factory()->count(3)->create();
    }
}
