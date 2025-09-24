<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LearnerProfile;
use App\Models\Challenge;
use App\Models\Attempt;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Number of students to create
        $studentCount = 10;

        // Get all challenges
        $challenges = Challenge::all();

        User::factory()->count($studentCount)->create([
            'role' => 'student',
        ])->each(function ($user) use ($challenges) {

            // Create learner profile for each student
            LearnerProfile::factory()->create([
                'user_id' => $user->id,
            ]);

            // Create random attempts for each student
            foreach ($challenges as $challenge) {
                // Each student attempts each challenge randomly
                $attemptCount = rand(0, 3); // 0 to 3 attempts per challenge
                for ($i = 0; $i < $attemptCount; $i++) {
                    Attempt::create([
                        'user_id' => $user->id,
                        'challenge_id' => $challenge->id,
                        'score' => rand(10, $challenge->points),
                        'is_successful' => (bool)rand(0, 1),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }
}
