<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $clients = DB::select('SELECT client_id, ova FROM users u WHERE u.blocked = ? AND u.approved = ?', [false, true]);
        $data = fake()->randomElement($clients);
        //$accounts = User::where('blocked', 'false')->where('account_id', '<>', $data->account_id)->pluck('account_id')->toArray();
        return [
            'reference' => 'ref' . fake()->randomNumber(6, true),
            'client_id' => $data->client_id,
            'debit_account' => $data->ova,
            'credit_account' => fake()->randomElement(['0775623646','0701356712','0781126013','0752003900','0772356712','0750117542','0701099098','0774843714']),
            'transaction_type' => fake()->randomElement(['MTN MoMo','Airtel Pay']),
            'amount' => fake()->randomNumber(5, true),
            'created_by' => 'app',
            'status' => 'SUCCESS',
            'reason' => 'Transaction processed successfully',
            'client_ip' => '172.217.22.14',
        ];
    }
}
