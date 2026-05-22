<?php

namespace Database\Factories;

use App\Modules\Class\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

class KelasFactory extends Factory
{
    protected $model = Kelas::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['X A', 'X B', 'XI A', 'XI B', 'XII A', 'XII B']),
            'grade_level' => fake()->numberBetween(10, 12),
            'capacity' => fake()->numberBetween(25, 40),
        ];
    }
}
