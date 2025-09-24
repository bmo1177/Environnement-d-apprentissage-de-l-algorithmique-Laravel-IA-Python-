<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Challenge;
use App\Models\Competency;
use App\Models\Attempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'students' => User::where('role', 'student')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'challenges' => Challenge::count(),
            'competencies' => Competency::count(),
            'total_attempts' => Attempt::count(),
        ];
        
        $recentUsers = User::latest()->take(5)->get();
        
        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }

    public function users()
    {
        $users = User::paginate(20);
        return view('admin.users', compact('users'));
    }

    public function createUser()
    {
        return view('admin.user-create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,teacher,student',
            'student_id' => 'nullable|string|unique:users',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        
        if ($user->role === 'student') {
            LearnerProfile::create(['user_id' => $user->id]);
        }
        
        return redirect()->route('admin.users')
            ->with('success', 'User created successfully!');
    }

    public function editUser(User $user)
    {
        return view('admin.user-edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,teacher,student',
            'student_id' => 'nullable|string|unique:users,student_id,' . $user->id,
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        
        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully!');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully!');
    }

    public function competencies()
    {
        $competencies = Competency::withCount('challenges')->paginate(20);
        return view('admin.competencies', compact('competencies'));
    }

    public function createCompetency()
    {
        return view('admin.competency-create');
    }

    public function storeCompetency(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'domain' => 'required|string|max:255',
            'level' => 'required|integer|min:1|max:10',
            'max_score' => 'required|integer|min:10|max:1000',
        ]);

        Competency::create($validated);
        
        return redirect()->route('admin.competencies')
            ->with('success', 'Competency created successfully!');
    }

    public function editCompetency(Competency $competency)
    {
        return view('admin.competency-edit', compact('competency'));
    }

    public function updateCompetency(Request $request, Competency $competency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'domain' => 'required|string|max:255',
            'level' => 'required|integer|min:1|max:10',
            'max_score' => 'required|integer|min:10|max:1000',
        ]);

        $competency->update($validated);
        
        return redirect()->route('admin.competencies')
            ->with('success', 'Competency updated successfully!');
    }

    public function systemSettings()
    {
        return view('admin.settings');
    }
}