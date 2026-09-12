<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_code' => strtoupper($this->faker->unique()->bothify('??-###')),
            'course_title' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'units' => $this->faker->numberBetween(1, 5),
            'status' => 'ACTIVE',
        ];
    }
}
