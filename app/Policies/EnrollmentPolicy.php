<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // scoped per-role inside the controller query itself
    }

    public function view(User $user, Enrollment $enrollment): bool
    {
        if ($user->hasAnyRole(['administrator', 'registrar'])) {
            return true;
        }

        if ($user->hasRole('instructor')) {
            return $enrollment->courseOffering->instructor_id === $user->id;
        }

        return $user->hasRole('student') && $user->student?->id === $enrollment->student_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'registrar']);
    }

    public function update(User $user, Enrollment $enrollment): bool
    {
        return $user->hasAnyRole(['administrator', 'registrar']);
    }

    public function delete(User $user, Enrollment $enrollment): bool
    {
        return $user->hasAnyRole(['administrator', 'registrar']);
    }
}
