<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama' => fake()->randomElement(['Makanan & Minuman', 'Transportasi', 'Belanja', 'Gaji', 'Investasi', 'Tagihan']),
            'tipe' => fake()->randomElement(['income', 'expense']),
            'warna' => fake()->hexColor(),
            'icon' => fake()->randomElement(['wallet', 'shopping-cart', 'utensils', 'car', 'bolt', 'briefcase']),
        ];
    }
}
