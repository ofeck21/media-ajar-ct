<?php

namespace App\Livewire\Student;

use App\Models\GameSession;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $classroom = $user->getClassroom();
        $activeSession = $classroom?->gameSessions()->whereNotIn('status', ['finished'])->latest()->first();

        $group = null;
        if ($activeSession) {
            $group = $user->groups()
                ->where('game_session_id', $activeSession->id)
                ->with(['students' => function($q) use ($activeSession) {
                    $q->with(['diagnosticResults' => function($r) use ($activeSession) {
                        $r->where('game_session_id', $activeSession->id);
                    }]);
                }])
                ->first();

            if ($activeSession->status === 'diagnostic') {
                $this->redirectRoute('student.diagnostic', navigate: true);
            } elseif (in_array($activeSession->status, ['grouping', 'playing'])) {
                $this->redirectRoute('student.game', navigate: true);
            }
        }

        $myResults = $user->results()
            ->with(['gameSession', 'group'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.student.dashboard', [
            'classroom' => $classroom,
            'activeSession' => $activeSession,
            'group' => $group,
            'myResults' => $myResults,
        ]);
    }

    public $showReplayModal = false;
    public $replayStudent = null;
    public $replayData = null;

    public function openReplay($resultId)
    {
        $result = \App\Models\StudentResult::with('gameSession')->find($resultId);
        if ($result && $result->student_id === auth()->id()) {
            $reflections = $result->reflections ?? [];
            $this->replayStudent = [
                'name' => auth()->user()->name,
                'session_name' => $result->gameSession?->name ?? 'Sesi Permainan',
                'stars' => $result->total_stars,
                'steps' => $result->total_steps,
                'points' => $reflections['total_points'] ?? ($result->total_stars * 100),
                'duration' => $reflections['duration_seconds'] ?? 0,
                'emotion' => $reflections['emotion'] ?? '-',
                'pillar' => $reflections['pillar'] ?? '-',
            ];

            $this->replayData = $reflections['gameplay_data'] ?? [
                'level' => $result->diagnostic_level ?? 'Basic',
                'gridSize' => 5,
                'targetSteps' => 10,
                'startTile' => ['x' => 0, 'y' => 4],
                'wuduTile' => ['x' => 2, 'y' => 3],
                'kitabTile' => ['x' => 3, 'y' => 1],
                'masjidTile' => ['x' => 4, 'y' => 0],
                'obstacles' => [['x' => 1, 'y' => 2]],
                'workspace' => ['FORWARD', 'TURN_LEFT', 'FORWARD', 'LOOP_2', 'FORWARD'],
            ];

            $this->showReplayModal = true;
            $this->dispatch('init-replay-canvas', data: $this->replayData);
        }
    }

    public function closeReplay()
    {
        $this->showReplayModal = false;
        $this->replayStudent = null;
        $this->replayData = null;
    }
}
