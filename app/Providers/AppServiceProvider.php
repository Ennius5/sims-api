<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Student;
use App\Policies\StudentPolicy;
use Illuminate\Support\Facades\Gate;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(\App\Models\Enrollment::class, \App\Policies\EnrollmentPolicy::class);
        Gate::policy(\App\Models\Grade::class, \App\Policies\GradePolicy::class);
    }
}
