<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\User;

class InstructorController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $instructors = User::role('instructor')
            ->where('status', 'ACTIVE')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return $this->success($instructors, 'Instructors retrieved successfully.');
    }
}
