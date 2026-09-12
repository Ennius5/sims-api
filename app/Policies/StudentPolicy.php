<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'registrar', 'instructor']) || $user->hasRole('student');
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->hasAnyRole(['administrator', 'registrar'])) {
            return true;
        }

        return $user->hasRole('student') && $user->student?->id === $student->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'registrar']);
    }

    public function update(User $user, Student $student): bool
    {
        return $user->hasAnyRole(['administrator', 'registrar']);
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasRole('administrator');
    }
}
