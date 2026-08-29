<?php

namespace Database\Factories;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Goal>
 */
class GoalFactory extends Factory
{
    protected $model = Goal::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama_goal' => fake()->words(3, true),
            'target' => 10000000.00,
            'progres' => 0.00,
            'deadline' => fake()->dateTimeBetween('+1 month', '+1 year')->format('Y-m-d'),
            'catatan' => fake()->sentence(),
        ];
    }
}
