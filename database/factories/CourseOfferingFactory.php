<?php

namespace Database\Factories;

use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseOfferingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::inRandomOrder()->first()?->id ?? Course::factory(),
            'academic_term_id' => AcademicTerm::inRandomOrder()->first()?->id ?? AcademicTerm::factory(),
            'instructor_id' => User::role('instructor')->inRandomOrder()->first()?->id,
            'section' => $this->faker->randomElement(['A', 'B', 'C']),
            'schedule' => $this->faker->randomElement(['MWF 8:00-9:00', 'TTH 9:30-11:00', 'MWF 1:00-2:00']),
            'room' => $this->faker->randomElement(['Rm 101', 'Rm 202', 'Lab 1', 'Lab 2']),
            'capacity' => 40,
            'status' => 'OPEN',
        ];
    }
}
