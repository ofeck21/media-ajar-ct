<?php

namespace App\Livewire\Student;

use App\Models\GameSession;
use App\Models\StudentGroup;
use App\Models\StudentResult;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Livewire\Component;

class Game extends Component
{
    public $session;
    public $group;
    public $studentLevel = 'Basic';

    // State Refleksi PSE
    public $showReflectionModal = false;
    public $completedStars = 0;
    public $completedSteps = 0;
    public $emotion = '';
    public $ctPillar = '';

    public function mount()
    {
        $this->ensureResultsTableExists();

        $user = auth()->user();
        $classroom = $user->getClassroom();
        $activeSession = $classroom?->gameSessions()->whereNotIn('status', ['finished'])->latest()->first();

        if (!$activeSession || in_array($activeSession->status, ['waiting', 'finished'])) {
            $this->redirectRoute('student.dashboard', navigate: true);
            return;
        }

        if ($activeSession->status === 'diagnostic') {
            $this->redirectRoute('student.diagnostic', navigate: true);
            return;
        }

        $this->session = $activeSession;
        if ($this->session->status === 'playing' && $this->session->started_at) {
            $elapsed = now()->timestamp - $this->session->started_at->timestamp;
            $this->initialElapsed = $elapsed > 0 ? $elapsed : 0;
        } else {
            $this->initialElapsed = 0;
        }

        $this->loadStudentData();
    }

    protected function ensureResultsTableExists()
    {
        if (!Schema::hasColumn('student_groups', 'score')) {
            Schema::table('student_groups', function (Blueprint $table) {
                $table->integer('score')->default(0)->after('name');
            });
        }

        if (!Schema::hasTable('student_results')) {
            Schema::create('student_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('game_session_id')->constrained()->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('student_group_id')->nullable()->constrained('student_groups')->nullOnDelete();
                $table->integer('diagnostic_score')->nullable();
                $table->string('diagnostic_level')->nullable();
                $table->integer('total_stars')->default(0);
                $table->integer('total_steps')->default(0);
                $table->json('reflections')->nullable();
                $table->timestamps();
            });
        }
    }

    public $hasFinished = false;
    public $existingResult = null;
    public $initialElapsed = 0;

    public function loadStudentData()
    {
        $user = auth()->user();

        // Load group
        $this->group = $user->groups()
            ->where('game_session_id', $this->session->id)
            ->with(['students' => function($q) {
                $q->with(['diagnosticResults' => function($r) {
                    $r->where('game_session_id', $this->session->id);
                }]);
            }])
            ->first();

        // Load diagnostic level
        $diagResult = $user->diagnosticResults()->where('game_session_id', $this->session->id)->first();
        $this->studentLevel = $diagResult->level ?? 'Basic';

        // Check if student has already finished the game
        $this->existingResult = StudentResult::where('game_session_id', $this->session->id)
            ->where('student_id', $user->id)
            ->first();
        $this->hasFinished = !is_null($this->existingResult);
        
        \Log::info("GameProp: hasFinished={$this->hasFinished}, session_id={$this->session->id}, student_id={$user->id}, existingResult=".($this->existingResult ? 'YES' : 'NO'));
    }

    public function saveGameResult($stars, $steps, $reflectionsData, $attempts = 1, $status = 'finished')
    {
        $user = auth()->user();

        // Enforce 1 time finish per student per session
        $alreadyFinished = StudentResult::where('game_session_id', $this->session->id)
            ->where('student_id', $user->id)
            ->exists();

        if ($alreadyFinished) {
            $this->showReflectionModal = false;
            session()->flash('error_message', "Kamu sudah menyelesaikan permainan pada sesi ini.");
            return;
        }

        // Ambil data total poin (Efisiensi Langkah + Bonus Kecepatan Waktu)
        $earnedPoints = (int) ($reflectionsData['total_points'] ?? ($stars * 100));
        $durationSec = (int) ($reflectionsData['duration_seconds'] ?? 0);

        // Simpan hasil permainan
        StudentResult::create([
            'game_session_id' => $this->session->id,
            'student_id' => $user->id,
            'student_group_id' => $this->group?->id,
            'diagnostic_level' => $this->studentLevel,
            'total_stars' => $stars,
            'total_steps' => $steps,
            'total_attempts' => $attempts,
            'status' => $status,
            'reflections' => $reflectionsData,
        ]);

        // Tambahkan Poin Kelompok di DB (+ Total Poin)
        if ($this->group) {
            $this->group->increment('score', $earnedPoints);
        }

        $this->hasFinished = true;
        $this->showReflectionModal = false;
        session()->flash('success_message', "Hebat! Misi diselesaikan dalam {$durationSec} detik dengan {$stars} Bintang ⭐ (+{$earnedPoints} Poin Kelompok!)");
    }

    public function getElapsedSecondsProperty()
    {
        if ($this->session->status !== 'playing' || !$this->session->started_at) {
            return 0;
        }
        $elapsed = now()->timestamp - $this->session->started_at->timestamp;
        return $elapsed > 0 ? $elapsed : 0;
    }

    public function getRemainingTimeProperty()
    {
        if ($this->session->status !== 'playing' || !$this->session->started_at) {
            \Log::info("TimeProp: status not playing or started_at null");
            return 0;
        }

        $elapsedSeconds = now()->timestamp - $this->session->started_at->timestamp;
        $totalSeconds = $this->session->duration_minutes * 60;
        $remaining = $totalSeconds - $elapsedSeconds;
        
        \Log::info("TimeProp: elapsed={$elapsedSeconds}, total={$totalSeconds}, remaining={$remaining}, now=".now()->timestamp.", started=".$this->session->started_at->timestamp);

        return $remaining > 0 ? $remaining : 0;
    }

    public function render()
    {
        $user = auth()->user();
        $classroom = $user->getClassroom();
        $latestSession = $classroom?->gameSessions()->whereNotIn('status', ['finished'])->latest()->first();

        // Jika ada sesi yang lebih baru atau sesi saat ini sudah dihapus/selesai
        if ($latestSession && $latestSession->id !== $this->session->id) {
            // Sesi baru telah dimulai oleh guru, redirect ke dashboard agar diarahkan dengan benar
            $this->redirectRoute('student.dashboard', navigate: true);
            return view('livewire.student.game', [
                'activeSession' => $this->session,
                'leaderboard' => collect(),
            ]);
        }

        $this->session = GameSession::find($this->session->id);
        
        if (!$this->session || in_array($this->session->status, ['waiting', 'finished'])) {
            $this->redirectRoute('student.dashboard', navigate: true);
            return view('livewire.student.game', [
                'activeSession' => $this->session ?? new GameSession(),
                'leaderboard' => collect(),
            ]);
        }

        if ($this->session->status === 'diagnostic') {
            $this->redirectRoute('student.diagnostic', navigate: true);
            return view('livewire.student.game', [
                'activeSession' => $this->session,
                'leaderboard' => collect(),
            ]);
        }

        $this->loadStudentData();

        $leaderboard = StudentGroup::where('game_session_id', $this->session->id)
            ->with('students')
            ->get()
            ->sortByDesc(function ($g) {
                return $g->score ?? 0;
            })
            ->values();

        return view('livewire.student.game', [
            'activeSession' => $this->session,
            'leaderboard' => $leaderboard,
        ]);
    }
}
