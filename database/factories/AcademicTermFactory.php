<?php

namespace Database\Factories;

use App\Models\AcademicTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicTerm>
 */
class AcademicTermFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_year' => '2026-2027',
            'semester' => $this->faker->randomElement(['1st', '2nd']),
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-15',
            'status' => 'ACTIVE',
        ];
    }
}
