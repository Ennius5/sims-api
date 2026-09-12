<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AcademicRecordController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;
    public function show(Student $student)
    {
        $this->authorize('view', $student); // reuses StudentPolicy — same access rule

        $enrollments = $student->enrollments()
            ->with(['courseOffering.course', 'courseOffering.academicTerm', 'grade'])
            ->get()
            ->groupBy(fn ($e) => $e->courseOffering->academicTerm->academic_year . ' - ' . $e->courseOffering->academicTerm->semester);

        $record = $enrollments->map(function ($termEnrollments) {
            return $termEnrollments->map(fn ($e) => [
                'course_code' => $e->courseOffering->course->course_code,
                'course_title' => $e->courseOffering->course->course_title,
                'units' => $e->courseOffering->course->units,
                'midterm_grade' => $e->grade?->midterm_grade,
                'final_grade' => $e->grade?->final_grade,
                'remarks' => $e->grade?->remarks,
            ]);
        });

        return $this->success([
            'student' => [
                'id' => $student->id,
                'student_number' => $student->student_number,
                'name' => $student->fullName(),
            ],
            'academic_record' => $record,
        ], 'Academic record retrieved successfully.');
    }
}
