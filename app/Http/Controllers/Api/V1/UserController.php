<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 20), 100);

        $query = User::query();

        if ($request->filled('role')) {
            $query->role($request->string('role'));
        }

        if ($request->filled('search')) {
            $value = $request->string('search');
            $query->where(function ($q) use ($value) {
                $q->where('name', 'like', "%{$value}%")
                  ->orWhere('email', 'like', "%{$value}%");
            });
        }

        $users = $query->orderBy('name')->paginate($perPage);

        $users->getCollection()->transform(fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'status' => $user->status,
            'student_id' => $user->student?->id, // lets the frontend show current linkage, if any
        ]);

        return $this->success($users, 'Users retrieved successfully.');
    }
}
