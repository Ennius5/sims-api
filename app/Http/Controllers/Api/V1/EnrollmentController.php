<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollmentRequest;
use App\Http\Resources\EnrollmentResource;
use App\Http\Traits\ApiResponse;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EnrollmentController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = min((int) request('per_page', 20), 100);

        $query = QueryBuilder::for(Enrollment::class)
            ->with(['student', 'courseOffering.course', 'grade'])
            ->allowedFilters(['student_id', 'course_offering_id', 'status'])
            ->allowedSorts(['enrollment_date', 'created_at']);

        if ($user->hasRole('student')) {
            $query->where('student_id', $user->student?->id);
        } elseif ($user->hasRole('instructor')) {
            $query->whereHas('courseOffering', fn ($q) => $q->where('instructor_id', $user->id));
        }

        $enrollments = $query->paginate($perPage);

        return $this->success(EnrollmentResource::collection($enrollments)->response()->getData(true), 'Enrollments retrieved successfully.');
    }

    public function store(EnrollmentRequest $request)
    {
        $this->authorize('create', Enrollment::class);

        $enrollment = Enrollment::create($request->validated());

        return $this->success(new EnrollmentResource($enrollment->load(['student', 'courseOffering'])), 'Enrollment created successfully.', 201);
    }

    public function show(Enrollment $enrollment)
    {
        $this->authorize('view', $enrollment);

        return $this->success(new EnrollmentResource($enrollment->load(['student', 'courseOffering', 'grade'])), 'Enrollment retrieved successfully.');
    }

    public function update(EnrollmentRequest $request, Enrollment $enrollment)
    {
        $this->authorize('update', $enrollment);

        $enrollment->update($request->validated());

        return $this->success(new EnrollmentResource($enrollment->load(['student', 'courseOffering'])), 'Enrollment updated successfully.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $this->authorize('delete', $enrollment);

        $enrollment->delete();

        return $this->success(null, 'Enrollment deleted successfully.');
    }

    public function indexForStudent(\App\Models\Student $student)
    {
        $this->authorize('view', $student);

        $perPage = min((int) request('per_page', 20), 100);

        $enrollments = $student->enrollments()
            ->with(['courseOffering.course', 'courseOffering.academicTerm', 'grade'])
            ->paginate($perPage);

        return $this->success(EnrollmentResource::collection($enrollments)->response()->getData(true), 'Student enrollments retrieved successfully.');
    }
}
