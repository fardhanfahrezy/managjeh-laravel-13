<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringRule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecurringRule>
 */
class RecurringRuleFactory extends Factory
{
    protected $model = RecurringRule::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'account_id' => Account::factory(),
            'category_id' => Category::factory(),
            'jumlah' => 150000.00,
            'frekuensi' => 'monthly',
            'tanggal_berikutnya' => now()->toDateString(),
            'catatan' => fake()->words(2, true),
            'is_active' => true,
        ];
    }
}
