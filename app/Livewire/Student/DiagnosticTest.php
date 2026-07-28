<?php

namespace App\Livewire\Student;

use App\Models\DiagnosticQuestion;
use App\Models\GameSession;
use Livewire\Component;

class DiagnosticTest extends Component
{
    public GameSession $session;
    public $questions;
    public $answers = [];
    public $isSubmitted = false;

    public function mount()
    {
        $classroom = auth()->user()->getClassroom();
        $activeSession = $classroom?->gameSessions()->where('status', 'diagnostic')->latest()->first();

        if (!$activeSession) {
            $this->redirectRoute('student.dashboard', navigate: true);
            return;
        }

        $this->session = $activeSession;
        
        // Cek apakah sudah pernah submit
        $existingResult = auth()->user()->diagnosticResults()->where('game_session_id', $this->session->id)->first();
        if ($existingResult) {
            $this->isSubmitted = true;
        }

        // Ambil 3 Soal Logika Singkat
        $this->questions = DiagnosticQuestion::inRandomOrder()->take(3)->get();
        foreach ($this->questions as $q) {
            $this->answers[$q->id] = null;
        }
    }

    public function submit()
    {
        $this->validate([
            'answers.*' => 'required',
        ], [
            'answers.*.required' => 'Semua soal wajib dijawab!',
        ]);

        $score = 0;
        $total = count($this->questions);
        
        if ($total > 0) {
            foreach ($this->questions as $q) {
                if (isset($this->answers[$q->id]) && (int)$this->answers[$q->id] === (int)$q->correct_answer_index) {
                    $score++;
                }
            }
            $finalScore = round(($score / $total) * 100);
        } else {
            $finalScore = 0;
        }

        // Tentukan level: Basic (<20), Intermediate (20-80), Advanced (>80)
        if ($finalScore < 20) {
            $level = 'Basic';
        } elseif ($finalScore <= 80) {
            $level = 'Intermediate';
        } else {
            $level = 'Advanced';
        }

        auth()->user()->diagnosticResults()->create([
            'game_session_id' => $this->session->id,
            'score' => $finalScore,
            'level' => $level,
        ]);

        $this->isSubmitted = true;
    }

    public function render()
    {
        // Polling to check for session status changes
        if (isset($this->session)) {
            $user = auth()->user();
            $classroom = $user->getClassroom();
            $latestSession = $classroom?->gameSessions()->whereNotIn('status', ['finished'])->latest()->first();

            // Jika ada sesi yang lebih baru atau sesi saat ini sudah dihapus/selesai
            if ($latestSession && $latestSession->id !== $this->session->id) {
                $this->redirectRoute('student.dashboard', navigate: true);
                return view('livewire.student.diagnostic-test');
            }

            $this->session = GameSession::find($this->session->id);
            
            if (!$this->session || in_array($this->session->status, ['waiting', 'finished'])) {
                $this->redirectRoute('student.dashboard', navigate: true);
                return view('livewire.student.diagnostic-test');
            }

            if (in_array($this->session->status, ['grouping', 'playing'])) {
                $this->redirectRoute('student.game', navigate: true);
                return view('livewire.student.diagnostic-test');
            }
        }

        return view('livewire.student.diagnostic-test');
    }
}
