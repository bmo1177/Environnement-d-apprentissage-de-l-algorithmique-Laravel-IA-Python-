<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Database\PDOWrapper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Service class for handling user-related business logic
 */
class UserService
{
    /**
     * @var PDOWrapper
     */
    protected $db;

    /**
     * UserService constructor
     */
    public function __construct()
    {
        $this->db = new PDOWrapper();
    }

    /**
     * Get user by ID
     *
     * @param int $userId
     * @return User|null
     */
    public function getUserById(int $userId)
    {
        try {
            return User::find($userId);
        } catch (\Exception $e) {
            Log::error('Error retrieving user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get users by role
     *
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersByRole(string $role)
    {
        try {
            return User::where('role', $role)->get();
        } catch (\Exception $e) {
            Log::error('Error retrieving users by role: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Create a new user
     *
     * @param array $userData
     * @return User|null
     */
    public function createUser(array $userData)
    {
        try {
            // Ensure password is hashed
            if (isset($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            }

            return User::create($userData);
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update user information
     *
     * @param int $userId
     * @param array $userData
     * @return bool
     */
    public function updateUser(int $userId, array $userData)
    {
        try {
            $user = User::find($userId);
            
            if (!$user) {
                return false;
            }

            // Hash password if it's being updated
            if (isset($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            }

            return $user->update($userData);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a user
     *
     * @param int $userId
     * @return bool
     */
    public function deleteUser(int $userId)
    {
        try {
            $user = User::find($userId);
            
            if (!$user) {
                return false;
            }

            return $user->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Authenticate user with secure password verification
     *
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function authenticateUser(string $email, string $password)
    {
        try {
            $user = User::where('email', $email)->first();
            
            if (!$user || !Hash::check($password, $user->password)) {
                return null;
            }

            return $user;
        } catch (\Exception $e) {
            Log::error('Authentication error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get students assigned to a teacher
     *
     * @param int $teacherId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeacherStudents(int $teacherId)
    {
        try {
            return User::where('role', 'student')
                ->where('teacher_id', $teacherId)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error retrieving teacher students: ' . $e->getMessage());
            return collect([]);
        }
    }
}