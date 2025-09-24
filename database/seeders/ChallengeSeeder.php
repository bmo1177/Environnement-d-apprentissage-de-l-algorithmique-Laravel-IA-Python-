<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Challenge;
use App\Models\Competency;

class ChallengeSeeder extends Seeder
{
    public function run()
    {
        $challenges = [
            [
                'title' => 'Two Sum',
                'description' => 'Find two numbers in an array that add up to a target value',
                'problem_statement' => 'Given an array of integers and a target sum, return the indices of two numbers that add up to the target.',
                'competency_id' => 1,
                'difficulty' => 'easy',
                'starter_code' => "def two_sum(nums, target):\n    # Your code here\n    pass",
                'test_cases' => [
                ['input' => ['nums' => [2, 7, 11, 15], 'target' => 9], 'output' => [0, 1]],
                ['input' => ['nums' => [3, 2, 4], 'target' => 6], 'output' => [1, 2]],
                ['input' => ['nums' => [3, 3], 'target' => 6], 'output' => [0, 1]],
                ],

                'hints' => [
                    'Try using a hash map to store seen numbers',
                    'For each number, check if target - number exists',
                ],
                'points' => 50,
            ],
            [
                'title' => 'Palindrome Check',
                'description' => 'Check if a string is a palindrome',
                'problem_statement' => 'Write a function to determine if a given string is a palindrome (reads the same forwards and backwards).',
                'competency_id' => 1,
                'difficulty' => 'easy',
                'starter_code' => "def is_palindrome(s):\n    # Your code here\n    pass",
                'test_cases' => [
                    ['input' => ['s' => 'racecar'], 'output' => true],
                    ['input' => ['s' => 'hello'], 'output' => false],
                    ['input' => ['s' => 'A man a plan a canal Panama'], 'output' => true],
                ],
                'hints' => [
                    'Consider removing spaces and converting to lowercase',
                    'Compare the string with its reverse',
                ],
                'points' => 40,
            ],
            [
                'title' => 'Bubble Sort',
                'description' => 'Implement the bubble sort algorithm',
                'problem_statement' => 'Sort an array of integers in ascending order using bubble sort.',
                'competency_id' => 4,
                'difficulty' => 'easy',
                'starter_code' => "def bubble_sort(arr):\n    # Your code here\n    return arr",
                'test_cases' => [
                    ['input' => ['arr' => [64, 34, 25, 12, 22, 11, 90]], 'output' => [11, 12, 22, 25, 34, 64, 90]],
                    ['input' => ['arr' => [5, 2, 8, 1, 9]], 'output' => [1, 2, 5, 8, 9]],
                    ['input' => ['arr' => [1]], 'output' => [1]],
                ],
                'hints' => [
                    'Compare adjacent elements and swap if needed',
                    'Repeat until no more swaps are needed',
                ],
                'points' => 60,
            ],
            [
                'title' => 'Binary Search',
                'description' => 'Implement binary search on a sorted array',
                'problem_statement' => 'Find the index of a target element in a sorted array using binary search. Return -1 if not found.',
                'competency_id' => 5,
                'difficulty' => 'medium',
                'starter_code' => "def binary_search(arr, target):\n    # Your code here\n    return -1",
                'test_cases' => [
                    ['input' => ['arr' => [1, 3, 5, 7, 9, 11], 'target' => 7], 'output' => 3],
                    ['input' => ['arr' => [1, 2, 3, 4, 5], 'target' => 6], 'output' => -1],
                    ['input' => ['arr' => [10, 20, 30, 40, 50], 'target' => 10], 'output' => 0],
                ],
                'hints' => [
                    'Start with left and right pointers',
                    'Calculate mid and compare with target',
                    'Adjust search space based on comparison',
                ],
                'points' => 80,
            ],
            [
                'title' => 'Fibonacci Sequence',
                'description' => 'Generate the nth Fibonacci number',
                'problem_statement' => 'Write a function to return the nth number in the Fibonacci sequence using recursion.',
                'competency_id' => 6,
                'difficulty' => 'medium',
                'starter_code' => "def fibonacci(n):\n    # Your code here\n    pass",
                'test_cases' => [
                    ['input' => ['n' => 5], 'output' => 5],
                    ['input' => ['n' => 10], 'output' => 55],
                    ['input' => ['n' => 1], 'output' => 1],
                ],
                'hints' => [
                    'Base cases: F(0) = 0, F(1) = 1',
                    'Recursive case: F(n) = F(n-1) + F(n-2)',
                ],
                'points' => 70,
            ],
            [
                'title' => 'Stack Implementation',
                'description' => 'Implement a stack data structure',
                'problem_statement' => 'Create a Stack class with push, pop, peek, and is_empty methods.',
                'competency_id' => 2,
                'difficulty' => 'medium',
                'starter_code' => "class Stack:\n    def __init__(self):\n        pass\n    \n    def push(self, item):\n        pass\n    \n    def pop(self):\n        pass\n    \n    def peek(self):\n        pass\n    \n    def is_empty(self):\n        pass",
                'test_cases' => [
                    ['input' => ['operations' => ['push(1)', 'push(2)', 'pop()', 'peek()']], 'output' => [null, null, 2, 1]],
                    ['input' => ['operations' => ['is_empty()', 'push(5)', 'is_empty()']], 'output' => [true, null, false]],
                ],
                'hints' => [
                    'Use a list to store stack elements',
                    'Push adds to the end, pop removes from the end',
                ],
                'points' => 90,
            ],
        ];

        foreach ($challenges as $challengeData) {
            Challenge::create($challengeData);
        }
    }
}