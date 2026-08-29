<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama_akun' => fake()->randomElement(['BCA Utama', 'Mandiri Tabungan', 'GoPay', 'OVO', 'Dompet Tunai']),
            'tipe' => fake()->randomElement(['bank', 'e-wallet', 'kas', 'kartu_kredit']),
            'saldo' => fake()->randomFloat(2, 50000, 5000000),
            'warna' => fake()->hexColor(),
            'catatan' => fake()->sentence(),
        ];
    }
}
