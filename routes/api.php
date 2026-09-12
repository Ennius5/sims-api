<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Middleware\Authenticate;
use Spatie\Permission\Middleware\RoleMiddleware;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProgramController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\AcademicTermController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\AcademicRecordController;
use App\Http\Controllers\Api\V1\CourseOfferingController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\GradeController;


Route::prefix('v1')->group(function () {


    Route::middleware(Authenticate::class . ':sanctum')->group(function () {

        Route::middleware(RoleMiddleware::class . ':administrator|registrar')->group(function () {
            Route::apiResource('programs', ProgramController::class);
            Route::apiResource('courses', CourseController::class);
            Route::apiResource('academic-terms', AcademicTermController::class);
            });

        Route::apiResource('students', StudentController::class);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::apiResource('course-offerings', CourseOfferingController::class);
        Route::get('/course-offerings/{courseOffering}/students', [CourseOfferingController::class, 'students']);

        Route::apiResource('enrollments', EnrollmentController::class)->except(['update']); // spec allows GET/PATCH/DELETE — using apiResource->only if you prefer
        Route::patch('/enrollments/{enrollment}', [EnrollmentController::class, 'update']);
        Route::get('/students/{student}/enrollments', [EnrollmentController::class, 'indexForStudent']);

        Route::post('/grades', [GradeController::class, 'store']);
        Route::get('/grades/{grade}', [GradeController::class, 'show']);
        Route::match(['put', 'patch'], '/grades/{grade}', [GradeController::class, 'update']);
        Route::get('/students/{student}/grades', [GradeController::class, 'indexForStudent']);


        Route::get('/students/{student}/academic-record', [AcademicRecordController::class, 'show']);

    });

    Route::post('/auth/login', [AuthController::class, 'login']);




});
