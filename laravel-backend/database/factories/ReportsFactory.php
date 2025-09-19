<?php

namespace Database\Factories;

use App\Models\Reports;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportsFactory extends Factory
{
    protected $model = Reports::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Generates a related user
            'type' => $this->faker->randomElement(['bug', 'feature', 'complaint']),
            'description' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
