<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LearnerProfile;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@learner.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Teachers
        $teacher1 = User::create([
            'name' => 'Teacher One',
            'email' => 'teacher1@learner.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $teacher2 = User::create([
            'name' => 'Teacher Two',
            'email' => 'teacher2@learner.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // Create Students
        $students = [
            ['name' => 'Alice Johnson', 'email' => 'alice@learner.com', 'student_id' => 'STD001'],
            ['name' => 'Bob Smith', 'email' => 'bob@learner.com', 'student_id' => 'STD002'],
            ['name' => 'Charlie Brown', 'email' => 'charlie@learner.com', 'student_id' => 'STD003'],
            ['name' => 'Diana Prince', 'email' => 'diana@learner.com', 'student_id' => 'STD004'],
            ['name' => 'Eve Wilson', 'email' => 'eve@learner.com', 'student_id' => 'STD005'],
        ];

        foreach ($students as $studentData) {
            $student = User::create([
                'name' => $studentData['name'],
                'email' => $studentData['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
                'student_id' => $studentData['student_id'],
                'level' => rand(1, 3),
            ]);

            // Create learner profile
            LearnerProfile::create([
                'user_id' => $student->id,
                'problem_solving_score' => rand(40, 90),
                'logical_reasoning_score' => rand(40, 90),
                'pattern_recognition_score' => rand(40, 90),
                'abstraction_score' => rand(40, 90),
                'total_attempts' => rand(5, 50),
                'successful_attempts' => rand(2, 30),
                'average_time_per_challenge' => rand(10, 60),
                'hints_used' => rand(0, 20),
                'engagement_level' => rand(30, 90),
                'persistence_score' => rand(30, 90),
                'streak_days' => rand(0, 15),
                'last_active_date' => now()->subDays(rand(0, 7)),
                'learning_style' => ['visual', 'verbal', 'sequential', 'global'][rand(0, 3)],
                'pace' => ['slow', 'moderate', 'fast'][rand(0, 2)],
                'overall_performance' => rand(40, 95),
            ]);
        }
    }
}
