<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'account_id' => Account::factory(),
            'destination_account_id' => null,
            'category_id' => Category::factory(),
            'jumlah' => fake()->randomFloat(2, 10000, 500000),
            'tipe' => 'expense',
            'tanggal' => fake()->date(),
            'catatan' => fake()->sentence(),
            'attachment_url' => null,
        ];
    }
}
