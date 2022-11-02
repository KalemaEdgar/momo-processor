<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'client_name' => 'dfcuapp',
            'description' => 'DFCU Web Mobile Payments Platform',
            'email' => 'admin@dfcuapp.com',
            'client_id' => 123456,
            'phone' => '0775623646',
            'ova' => 'OVA123456'
        ]);

        User::factory()->count(2)->create();
        User::factory()->blocked()->count(2)->create();
    }
}
