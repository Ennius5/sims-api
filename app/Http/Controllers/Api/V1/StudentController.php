<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentResource;
use App\Http\Traits\ApiResponse;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class StudentController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Student::class);

        $students = QueryBuilder::for(Student::class)
            ->with('program')
            ->allowedFilters([
                'program_id',
                'year_level',
                'status',
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->where('first_name', 'like', "%{$value}%")
                          ->orWhere('last_name', 'like', "%{$value}%")
                          ->orWhere('student_number', 'like', "%{$value}%");
                    });
                }),
            ])
            ->allowedSorts(['last_name', 'first_name', 'student_number', 'created_at'])
            ->paginate(request('per_page', 20));

        return $this->success(StudentResource::collection($students)->response()->getData(true), 'Students retrieved successfully.');
    }

    public function store(StudentRequest $request)
    {
        $this->authorize('create', Student::class);

        $student = Student::create($request->validated());

        return $this->success(new StudentResource($student->load('program')), 'Student created successfully.', 201);
    }

    public function show(Student $student)
    {
        $this->authorize('view', $student);

        return $this->success(new StudentResource($student->load('program')), 'Student retrieved successfully.');
    }

    public function update(StudentRequest $request, Student $student)
    {
        $this->authorize('update', $student);

        $student->update($request->validated());

        return $this->success(new StudentResource($student->load('program')), 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $this->authorize('delete', $student);

        $student->delete();

        return $this->success(null, 'Student deleted successfully.');
    }
}
