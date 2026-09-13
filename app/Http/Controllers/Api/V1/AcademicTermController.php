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
        $academicTerms = QueryBuilder::for(AcademicTerm::class)
            ->allowedFilters(['status', AllowedFilter::partial('search', 'name')])
            ->allowedSorts(['academic_year', 'semester','status', 'start_date', 'end_date'])
            ->paginate(request('per_page', 20));

        return $this->success(AcademicTermResource::collection($academicTerms)->response()->getData(true), 'Academic terms retrieved successfully.');
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
