<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Competency;

class CompetencySeeder extends Seeder
{
    public function run()
    {
        $competencies = [
            [
                'name' => 'Basic Algorithms',
                'description' => 'Understanding and implementing fundamental algorithms',
                'domain' => 'algorithms',
                'level' => 1,
                'max_score' => 100,
            ],
            [
                'name' => 'Data Structures',
                'description' => 'Working with arrays, lists, stacks, queues, and trees',
                'domain' => 'data_structures',
                'level' => 1,
                'max_score' => 100,
            ],
            [
                'name' => 'Problem Decomposition',
                'description' => 'Breaking complex problems into smaller, manageable parts',
                'domain' => 'problem_solving',
                'level' => 2,
                'max_score' => 100,
            ],
            [
                'name' => 'Sorting Algorithms',
                'description' => 'Implementing and optimizing various sorting techniques',
                'domain' => 'algorithms',
                'level' => 2,
                'max_score' => 100,
            ],
            [
                'name' => 'Searching Algorithms',
                'description' => 'Linear search, binary search, and advanced searching techniques',
                'domain' => 'algorithms',
                'level' => 2,
                'max_score' => 100,
            ],
            [
                'name' => 'Recursion',
                'description' => 'Understanding and implementing recursive solutions',
                'domain' => 'problem_solving',
                'level' => 3,
                'max_score' => 100,
            ],
            [
                'name' => 'Dynamic Programming',
                'description' => 'Solving optimization problems using dynamic programming',
                'domain' => 'algorithms',
                'level' => 4,
                'max_score' => 100,
            ],
            [
                'name' => 'Graph Algorithms',
                'description' => 'Working with graphs, traversals, and shortest path algorithms',
                'domain' => 'algorithms',
                'level' => 4,
                'max_score' => 100,
            ],
        ];

        foreach ($competencies as $competency) {
            Competency::create($competency);
        }
    }
}