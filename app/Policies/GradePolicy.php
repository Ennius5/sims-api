<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;

class GradePolicy
{
    public function view(User $user, Grade $grade): bool
    {
        if ($user->hasAnyRole(['administrator', 'registrar'])) {
            return true;
        }

        if ($user->hasRole('instructor')) {
            return $grade->enrollment->courseOffering->instructor_id === $user->id;
        }

        return $user->hasRole('student') && $user->student?->id === $grade->enrollment->student_id;
    }

    public function create(User $user, ?int $courseOfferingInstructorId = null): bool
    {
        if ($user->hasAnyRole(['administrator', 'registrar'])) {
            return true;
        }

        return $user->hasRole('instructor') && $courseOfferingInstructorId === $user->id;
    }

    public function update(User $user, Grade $grade): bool
    {
        if ($user->hasAnyRole(['administrator', 'registrar'])) {
            return true;
        }

        return $user->hasRole('instructor') && $grade->enrollment->courseOffering->instructor_id === $user->id;
    }
}
