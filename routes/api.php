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

Route::prefix('v1')->group(function () {


    Route::middleware(Authenticate::class . ':sanctum')->group(function () {

        Route::middleware(RoleMiddleware::class . ':administrator|registrar')->group(function () {
            Route::apiResource('programs', ProgramController::class);
            Route::apiResource('courses', CourseController::class);
            Route::apiResource('academic-terms', AcademicTermController::class);
            });

        Route::apiResource('students', StudentController::class);
        Route::post('/auth/login', [AuthController::class, 'login']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
            });



});
