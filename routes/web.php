<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        $role = auth()->user()->role;
        return match ($role) {
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => abort(403, 'Unknown role'),
        };
    })->name('dashboard');

    Route::middleware(['role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::livewire('/schools', App\Livewire\Superadmin\SchoolIndex::class)->name('schools.index');
        Route::livewire('/users', App\Livewire\Superadmin\UserIndex::class)->name('users.index');
        Route::livewire('/questions', App\Livewire\Superadmin\QuestionIndex::class)->name('questions.index');
        Route::get('/', function () {
            return redirect()->route('superadmin.schools.index');
        })->name('dashboard');
    });

    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::livewire('/classrooms', App\Livewire\Teacher\ClassroomIndex::class)->name('classrooms.index');
        Route::livewire('/classrooms/{classroom}', App\Livewire\Teacher\ClassroomShow::class)->name('classrooms.show');
        Route::livewire('/students', App\Livewire\Teacher\StudentIndex::class)->name('students.index');
        Route::livewire('/sessions/{session}', App\Livewire\Teacher\SessionShow::class)->name('sessions.show');
        Route::get('/', function () {
            return redirect()->route('teacher.classrooms.index');
        })->name('dashboard');
    });

    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::livewire('/', App\Livewire\Student\Dashboard::class)->name('dashboard');
        Route::livewire('/diagnostic', App\Livewire\Student\DiagnosticTest::class)->name('diagnostic');
        Route::livewire('/game', App\Livewire\Student\Game::class)->name('game');
    });
});

require __DIR__.'/settings.php';
