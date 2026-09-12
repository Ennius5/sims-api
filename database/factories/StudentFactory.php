<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_number' => $this->faker->unique()->numerify('2026-#####'),
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->lastName(),
            'last_name' => $this->faker->lastName(),
            'suffix' => null,
            'birth_date' => $this->faker->dateTimeBetween('-25 years', '-18 years'),
            'email' => $this->faker->unique()->safeEmail(),
            'contact_number' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'program_id' => Program::inRandomOrder()->first()?->id ?? Program::factory(),
            'year_level' => $this->faker->numberBetween(1, 4),
            'status' => 'ACTIVE',
        ];
    }
}
