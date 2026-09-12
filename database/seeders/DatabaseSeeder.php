<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Student;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        Program::firstOrCreate(['code' => 'BSIT'], ['name' => 'BS Information Technology', 'status' => 'ACTIVE']);
        Program::firstOrCreate(['code' => 'BSCS'], ['name' => 'BS Computer Science', 'status' => 'ACTIVE']);
        Program::firstOrCreate(['code' => 'BSIS'], ['name' => 'BS Information Systems', 'status' => 'ACTIVE']);

        Student::factory(100)->create();
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
