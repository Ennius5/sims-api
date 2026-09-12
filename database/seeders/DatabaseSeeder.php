<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Student;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Grade;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        Program::firstOrCreate(['code' => 'BSIT'], ['name' => 'BS Information Technology', 'status' => 'ACTIVE']);
        Program::firstOrCreate(['code' => 'BSCS'], ['name' => 'BS Computer Science', 'status' => 'ACTIVE']);
        Program::firstOrCreate(['code' => 'BSIS'], ['name' => 'BS Information Systems', 'status' => 'ACTIVE']);

        Student::factory(100)->create();
        $studentUser = User::where('email', 'student1@sims.test')->first();
        Student::inRandomOrder()->first()?->update(['user_id' => $studentUser->id]);

        \App\Models\Course::factory(20)->create();
        \App\Models\AcademicTerm::factory(2)->create();

        $courseIds = \App\Models\Course::pluck('id');
        $termIds = \App\Models\AcademicTerm::pluck('id');
        $sections = ['A', 'B', 'C'];

        $offeringCombos = collect();
        while ($offeringCombos->count() < 20) {
            $combo = [$courseIds->random(), $termIds->random(), collect($sections)->random()];
            $key = implode('-', $combo);
            if (! $offeringCombos->has($key)) {
                $offeringCombos->put($key, $combo);
            }
        }

        foreach ($offeringCombos as [$courseId, $termId, $section]) {
            CourseOffering::factory()->create([
                'course_id' => $courseId,
                'academic_term_id' => $termId,
                'section' => $section,
            ]);
        }

        $students = Student::pluck('id');
        $offerings = CourseOffering::pluck('id');

        $pairs = collect();
        while ($pairs->count() < 200) {
            $pair = [$students->random(), $offerings->random()];
            $key = implode('-', $pair);
            if (! $pairs->has($key)) {
                $pairs->put($key, $pair);
            }
        }

        foreach ($pairs as [$studentId, $offeringId]) {
            Enrollment::factory()->create([
                'student_id' => $studentId,
                'course_offering_id' => $offeringId,
            ]);
        }

        Enrollment::inRandomOrder()->limit(100)->get()->each(function ($enrollment) {
            Grade::factory()->create(['enrollment_id' => $enrollment->id]);
        });
    }
}
