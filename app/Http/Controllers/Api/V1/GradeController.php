<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeRequest;
use App\Http\Resources\GradeResource;
use App\Http\Traits\ApiResponse;
use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GradeController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function store(GradeRequest $request)
    {
        $enrollment = Enrollment::with('courseOffering')->findOrFail($request->enrollment_id);

        $this->authorize('create', [Grade::class, $enrollment->courseOffering->instructor_id]);

        $grade = Grade::create($request->validated());

        return $this->success(new GradeResource($grade), 'Grade recorded successfully.', 201);
    }

    public function show(Grade $grade)
    {
        $this->authorize('view', $grade);

        return $this->success(new GradeResource($grade), 'Grade retrieved successfully.');
    }

    public function update(GradeRequest $request, Grade $grade)
    {
        $this->authorize('update', $grade);

        $grade->update($request->validated());

        return $this->success(new GradeResource($grade), 'Grade updated successfully.');
    }

    public function indexForStudent(\App\Models\Student $student)
    {
        $this->authorize('view', $student); // same rule — student sees own, admin/registrar see all, instructor blocked unless extended

        $grades = \App\Models\Grade::whereHas('enrollment', fn ($q) => $q->where('student_id', $student->id))
            ->with('enrollment.courseOffering.course')
            ->paginate(request('per_page', 20));

        return $this->success(GradeResource::collection($grades)->response()->getData(true), 'Student grades retrieved successfully.');
    }
}
