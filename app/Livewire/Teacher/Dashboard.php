<?php

namespace App\Livewire\Teacher;

use App\Models\Classroom;
use App\Models\GameSession;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        $totalClassrooms = $user->classrooms()->count();
        
        $totalStudents = User::whereHas('studentClassrooms', function($q) use ($user) {
            $q->where('teacher_id', $user->id);
        })->count();

        $totalSessions = GameSession::where('teacher_id', $user->id)->count();

        $recentSessions = GameSession::where('teacher_id', $user->id)
            ->with('classroom')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.teacher.dashboard', [
            'totalClassrooms' => $totalClassrooms,
            'totalStudents' => $totalStudents,
            'totalSessions' => $totalSessions,
            'recentSessions' => $recentSessions,
        ]);
    }
}
