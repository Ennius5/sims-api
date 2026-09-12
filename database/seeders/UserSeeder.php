<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@sims.test'],
            ['name' => 'System Administrator', 'password' => Hash::make('password')]
        );
        $admin->assignRole('administrator');

        $registrar = User::firstOrCreate(
            ['email' => 'registrar@sims.test'],
            ['name' => 'Registrar Staff', 'password' => Hash::make('password')]
        );
        $registrar->assignRole('registrar');

        $instructor1 = User::firstOrCreate(
            ['email' => 'instructor1@sims.test'],
            ['name' => 'Juan Dela Cruz', 'password' => Hash::make('password')]
        );
        $instructor1->assignRole('instructor');

        $instructor2 = User::firstOrCreate(
            ['email' => 'instructor2@sims.test'],
            ['name' => 'Maria Santos', 'password' => Hash::make('password')]
        );
        $instructor2->assignRole('instructor');

        $studentUser = User::firstOrCreate(
            ['email' => 'student1@sims.test'],
            ['name' => 'Pedro Reyes', 'password' => Hash::make('password')]
        );
        $studentUser->assignRole('student');
    }
}
