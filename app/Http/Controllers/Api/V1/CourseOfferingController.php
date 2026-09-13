<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseOfferingRequest;
use App\Http\Resources\CourseOfferingResource;
use App\Http\Traits\ApiResponse;
use App\Models\CourseOffering;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CourseOfferingController extends Controller
{
    use ApiResponse;

public function index()
{
    $perPage = min((int) request('per_page', 20), 100);

    $offerings = QueryBuilder::for(CourseOffering::class)
        ->with(['course', 'academicTerm', 'instructor'])
        ->allowedFilters([
            'course_id',
            'academic_term_id',
            'instructor_id',
            'status',
            AllowedFilter::callback('search', function ($query, $value) {
                $query->where('section', 'like', "%{$value}%")
                    ->orWhereHas('course', function ($q) use ($value) {
                        $q->where('course_code', 'like', "%{$value}%")
                          ->orWhere('course_title', 'like', "%{$value}%");
                    });
            }),
        ])
        ->allowedSorts(['section', 'created_at'])
        ->paginate($perPage);

    return $this->success(CourseOfferingResource::collection($offerings)->response()->getData(true), 'Course offerings retrieved successfully.');
}

    public function store(CourseOfferingRequest $request)
    {
        $offering = CourseOffering::create($request->validated());

        return $this->success(new CourseOfferingResource($offering->load(['course', 'academicTerm', 'instructor'])), 'Course offering created successfully.', 201);
    }

    public function show(CourseOffering $courseOffering)
    {
        return $this->success(new CourseOfferingResource($courseOffering->load(['course', 'academicTerm', 'instructor'])), 'Course offering retrieved successfully.');
    }

    public function update(CourseOfferingRequest $request, CourseOffering $courseOffering)
    {
        $courseOffering->update($request->validated());

        return $this->success(new CourseOfferingResource($courseOffering->load(['course', 'academicTerm', 'instructor'])), 'Course offering updated successfully.');
    }

    public function destroy(CourseOffering $courseOffering)
    {
        $courseOffering->delete();

        return $this->success(null, 'Course offering deleted successfully.');
    }

    public function students(CourseOffering $courseOffering)
    {
        $perPage = min(request('per_page', 20), 100); // Limit the maximum per page to 100
        $students = $courseOffering->enrollments()->with('student.program')->paginate($perPage);

        return $this->success($students, 'Enrolled students retrieved successfully.');
    }
}
