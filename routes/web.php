<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    StudentController,
    TeacherController,
    AdminController,
    ChallengeController,
    HeatmapController
};

// Public routes
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Redirect based on role
    Route::get('/home', function () {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    })->name('home');

    // Student routes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/challenges', [StudentController::class, 'challenges'])->name('challenges');
        Route::get('/challenge/{challenge}', [StudentController::class, 'showChallenge'])->name('challenge');
        Route::post('/challenge/{challenge}/submit', [StudentController::class, 'submitSolution'])->name('submit');
        Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    });

    // Teacher routes
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
        Route::get('/students', [TeacherController::class, 'students'])->name('students');
        Route::get('/student/{id}', [TeacherController::class, 'studentDetail'])->name('student.detail');
        Route::get('/challenges', [TeacherController::class, 'challenges'])->name('challenges');
        Route::get('/challenge/create', [TeacherController::class, 'createChallenge'])->name('challenge.create');
        Route::post('/challenge/store', [TeacherController::class, 'storeChallenge'])->name('challenge.store');
        Route::get('/challenge/{challenge}/edit', [TeacherController::class, 'editChallenge'])->name('challenge.edit');
        Route::put('/challenge/{challenge}', [TeacherController::class, 'updateChallenge'])->name('challenge.update');
        Route::get('/clusters', [TeacherController::class, 'clusters'])->name('clusters');
        Route::get('/analytics', [TeacherController::class, 'analytics'])->name('analytics');
    });

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/user/create', [AdminController::class, 'createUser'])->name('user.create');
        Route::post('/user/store', [AdminController::class, 'storeUser'])->name('user.store');
        Route::get('/user/{user}/edit', [AdminController::class, 'editUser'])->name('user.edit');
        Route::put('/user/{user}', [AdminController::class, 'updateUser'])->name('user.update');
        Route::delete('/user/{user}', [AdminController::class, 'deleteUser'])->name('user.delete');
        Route::get('/competencies', [AdminController::class, 'competencies'])->name('competencies');
        Route::get('/competency/create', [AdminController::class, 'createCompetency'])->name('competency.create');
        Route::post('/competency/store', [AdminController::class, 'storeCompetency'])->name('competency.store');
        Route::get('/competency/{competency}/edit', [AdminController::class, 'editCompetency'])->name('competency.edit');
        Route::put('/competency/{competency}', [AdminController::class, 'updateCompetency'])->name('competency.update');
        Route::get('/settings', [AdminController::class, 'systemSettings'])->name('settings');
    });

    // Heatmap routes (for all authenticated users)
    Route::prefix('heatmap')->name('heatmap.')->group(function () {
        Route::post('/generate/{attempt}', [HeatmapController::class, 'generateHeatmap'])->name('generate');
        Route::get('/data', [HeatmapController::class, 'getHeatmapData'])->name('data');
    });

    // General challenge routes
    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
    Route::get('/challenge/{challenge}', [ChallengeController::class, 'show'])->name('challenges.show');
});