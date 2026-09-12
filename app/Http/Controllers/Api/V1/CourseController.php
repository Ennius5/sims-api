<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseResource;
use App\Http\Traits\ApiResponse;
use App\Models\Course;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CourseController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $courses = QueryBuilder::for(Course::class)
            ->allowedFilters(['status', AllowedFilter::partial('search', 'course_title')])
            ->allowedSorts(['course_title', 'course_code', 'created_at'])
            ->paginate(request('per_page', 20));

        return $this->success(CourseResource::collection($courses)->response()->getData(true), 'Courses retrieved successfully.');
    }

    public function store(CourseRequest $request)
    {
        $course = Course::create($request->validated());

        return $this->success(new CourseResource($course), 'Course created successfully.', 201);
    }

    public function show(Course $course)
    {
        return $this->success(new CourseResource($course), 'Course retrieved successfully.');
    }

    public function update(CourseRequest $request, Course $course)
    {
        $course->update($request->validated());

        return $this->success(new CourseResource($course), 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return $this->success(null, 'Course deleted successfully.', 200);
    }
}
