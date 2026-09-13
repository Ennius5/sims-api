<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicTermRequest;
use App\Http\Resources\AcademicTermResource;
use App\Http\Traits\ApiResponse;
use App\Models\AcademicTerm;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AcademicTermController extends Controller
{
    use ApiResponse;

public function index()
{
    $perPage = min((int) request('per_page', 20), 100);

    $terms = QueryBuilder::for(AcademicTerm::class)
        ->allowedFilters([
            'status',
            AllowedFilter::callback('search', function ($query, $value) {
                $query->where(function ($q) use ($value) {
                    $q->where('academic_year', 'like', "%{$value}%")
                      ->orWhere('semester', 'like', "%{$value}%");
                });
            }),
        ])
        ->allowedSorts(['academic_year', 'start_date', 'created_at'])
        ->paginate($perPage);

    return $this->success(AcademicTermResource::collection($terms)->response()->getData(true), 'Academic terms retrieved successfully.');
}

    public function store(AcademicTermRequest $request)
    {
        $academicTerm = AcademicTerm::create($request->validated());

        return $this->success(new AcademicTermResource($academicTerm), 'Academic term created successfully.', 201);
    }

    public function show(AcademicTerm $academicTerm)
    {
        return $this->success(new AcademicTermResource($academicTerm), 'Academic term retrieved successfully.');
    }

    public function update(AcademicTermRequest $request, AcademicTerm $academicTerm)
    {
        $academicTerm->update($request->validated());

        return $this->success(new AcademicTermResource($academicTerm), 'Academic term updated successfully.');
    }

    public function destroy(AcademicTerm $academicTerm)
    {
        $academicTerm->delete();

        return $this->success(null, 'Academic term deleted successfully.', 200);
    }
}
