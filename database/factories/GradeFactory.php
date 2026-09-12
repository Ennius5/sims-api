<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'midterm_grade' => $this->faker->randomFloat(2, 1, 3),
            'final_grade' => $this->faker->randomFloat(2, 1, 3),
            'remarks' => $this->faker->randomElement(['PASSED', 'PASSED', 'PASSED', 'FAILED']),
        ];
    }
}
