<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramRequest;
use App\Http\Resources\ProgramResource;
use App\Http\Traits\ApiResponse;
use App\Models\Program;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProgramController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $programs = QueryBuilder::for(Program::class)
            ->allowedFilters(['status', AllowedFilter::partial('search', 'name')])
            ->allowedSorts(['name', 'code', 'created_at'])
            ->paginate(request('per_page', 20));

        return $this->success(ProgramResource::collection($programs)->response()->getData(true), 'Programs retrieved successfully.');
    }

    public function store(ProgramRequest $request)
    {
        $program = Program::create($request->validated());

        return $this->success(new ProgramResource($program), 'Program created successfully.', 201);
    }

    public function show(Program $program)
    {
        return $this->success(new ProgramResource($program), 'Program retrieved successfully.');
    }

    public function update(ProgramRequest $request, Program $program)
    {
        $program->update($request->validated());

        return $this->success(new ProgramResource($program), 'Program updated successfully.');
    }

    public function destroy(Program $program)
    {
        $program->delete();

        return $this->success(null, 'Program deleted successfully.', 200);
    }
}
