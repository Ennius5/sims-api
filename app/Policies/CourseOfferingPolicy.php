<?php

namespace App\Policies;

use App\Models\CourseOffering;
use App\Models\User;

class CourseOfferingPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // any authenticated user can browse offerings
    }

    public function view(User $user, CourseOffering $courseOffering): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'registrar']);
    }

    public function update(User $user, CourseOffering $courseOffering): bool
    {
        return $user->hasAnyRole(['administrator', 'registrar']);
    }

    public function delete(User $user, CourseOffering $courseOffering): bool
    {
        return $user->hasAnyRole(['administrator', 'registrar']);
    }
}
