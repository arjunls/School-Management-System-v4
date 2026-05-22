<?php

namespace Database\Factories;

use App\Modules\Subject\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word() . ' ' . fake()->randomLetter(),
            'code' => strtoupper(fake()->unique()->lexify('???') . fake()->randomNumber(3)),
            'description' => fake()->sentence(),
            'credits' => fake()->numberBetween(1, 4),
        ];
    }
}
