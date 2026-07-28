<?php

namespace App\Livewire\Teacher;

use App\Models\GameSession;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SessionShow extends Component
{
    public GameSession $session;

    public $groupCount = 3;
    public $strategy = 'heterogeneous';

    public function mount(GameSession $session)
    {
        if ($session->teacher_id !== auth()->id()) {
            abort(403);
        }

        $this->session = $session;
    }

    public function startDiagnostic()
    {
        $this->session->update(['status' => 'diagnostic']);
    }

    public function generateGroups()
    {
        $this->validate([
            'groupCount' => 'required|integer|min:1|max:20',
            'strategy' => 'required|in:heterogeneous,homogeneous',
        ]);

        DB::transaction(function () {
            // Hapus grup lama jika ada
            $this->session->studentGroups()->delete();

            // Ambil semua siswa beserta nilainya, urutkan dari tertinggi ke terendah
            $students = $this->session->classroom->students()
                ->with(['diagnosticResults' => function($q) {
                    $q->where('game_session_id', $this->session->id);
                }])
                ->get()
                ->sortByDesc(function ($student) {
                    return $student->diagnosticResults->first()->score ?? 0;
                })
                ->values();

            // Buat grup kosong
            $groups = [];
            for ($i = 1; $i <= $this->groupCount; $i++) {
                $groups[] = $this->session->studentGroups()->create([
                    'name' => 'Kelompok ' . $i,
                ]);
            }

            if ($students->isNotEmpty()) {
                if ($this->strategy === 'heterogeneous') {
                    // Heterogen (Peer Tutoring): Distribusi Ular (Snake Draft)
                    $direction = 1;
                    $groupIndex = 0;
                    
                    foreach ($students as $student) {
                        $groups[$groupIndex]->students()->attach($student->id);
                        
                        $groupIndex += $direction;
                        if ($groupIndex >= $this->groupCount) {
                            $groupIndex = $this->groupCount - 1;
                            $direction = -1;
                        } elseif ($groupIndex < 0) {
                            $groupIndex = 0;
                            $direction = 1;
                        }
                    }
                } else {
                    // Homogen: Kemampuan sejenis (Advanced dengan Advanced, dsb)
                    $chunkSize = (int) ceil($students->count() / $this->groupCount);
                    $chunks = $students->chunk($chunkSize);

                    foreach ($chunks as $index => $chunk) {
                        if (isset($groups[$index])) {
                            foreach ($chunk as $student) {
                                $groups[$index]->students()->attach($student->id);
                            }
                        }
                    }
                }
            }

            $this->session->update(['status' => 'grouping']);
        });

        \Flux\Flux::modal('generate-groups')->close();
        $this->dispatch('close-modal', 'generate-groups');
    }

    public function startGame()
    {
        $this->session->update([
            'status' => 'playing',
            'started_at' => now(),
        ]);
    }
    
    public function finishGame()
    {
        $this->session->update(['status' => 'finished']);
    }

    public function stopSession()
    {
        $this->finishGame();
    }

    public function getRemainingTimeProperty()
    {
        if ($this->session->status !== 'playing' || !$this->session->started_at) {
            return 0;
        }

        $elapsedSeconds = now()->timestamp - $this->session->started_at->timestamp;
        $totalSeconds = $this->session->duration_minutes * 60;
        $remaining = $totalSeconds - $elapsedSeconds;

        return $remaining > 0 ? $remaining : 0;
    }

    public $replayStudent = null;
    public $replayAttempts = [];
    public $selectedAttemptIndex = 0;
    public $replayData = null;
    public $showReplayModal = false;

    public function openReplay($studentId)
    {
        $student = User::find($studentId);
        $results = \App\Models\StudentResult::where('game_session_id', $this->session->id)
            ->where('student_id', $studentId)
            ->orderBy('id', 'asc')
            ->get();

        if ($results->isNotEmpty() && $student) {
            $this->replayStudent = [
                'id' => $student->id,
                'name' => $student->name,
            ];

            $attempts = [];
            foreach ($results as $index => $result) {
                $reflections = $result->reflections ?? [];
                
                $gameplayData = $reflections['gameplay_data'] ?? [
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

                $attempts[] = [
                    'id' => $result->id,
                    'attempt_number' => $index + 1,
                    'stars' => $result->total_stars,
                    'steps' => $result->total_steps,
                    'points' => $reflections['total_points'] ?? ($result->total_stars * 100),
                    'duration' => $reflections['duration_seconds'] ?? 0,
                    'emotion' => $reflections['emotion'] ?? '-',
                    'pillar' => $reflections['pillar'] ?? '-',
                    'created_at' => $result->created_at ? $result->created_at->format('H:i:s') : '-',
                    'gameplay_data' => $gameplayData,
                ];
            }

            $this->replayAttempts = $attempts;
            $this->selectedAttemptIndex = count($attempts) - 1; // Default: Percobaan Terbaru
            $this->replayData = $attempts[$this->selectedAttemptIndex]['gameplay_data'];
            $this->showReplayModal = true;
            $this->dispatch('init-replay-canvas', data: $this->replayData);
        }
    }

    public function selectAttempt($index)
    {
        if (isset($this->replayAttempts[$index])) {
            $this->selectedAttemptIndex = $index;
            $this->replayData = $this->replayAttempts[$index]['gameplay_data'];
            $this->dispatch('init-replay-canvas', data: $this->replayData);
        }
    }

    public function closeReplay()
    {
        $this->showReplayModal = false;
        $this->replayStudent = null;
        $this->replayAttempts = [];
        $this->replayData = null;
    }

    public $showAllGroupsReplayModal = false;
    public $allGroupsReplayData = [];

    public function openAllGroupsReplay()
    {
        $groups = $this->session->studentGroups()
            ->with(['students' => function($q) {
                $q->with(['results' => function($r) {
                    $r->where('game_session_id', $this->session->id)->orderBy('id', 'desc');
                }]);
            }])
            ->get();

        $allData = [];
        foreach ($groups as $group) {
            $studentReplays = [];
            foreach ($group->students as $student) {
                $res = $student->results->first();
                if ($res) {
                    $reflections = $res->reflections ?? [];
                    $studentReplays[] = [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                        'stars' => $res->total_stars,
                        'points' => $reflections['total_points'] ?? ($res->total_stars * 100),
                        'duration' => $reflections['duration_seconds'] ?? 0,
                        'emotion' => $reflections['emotion'] ?? '-',
                        'pillar' => $reflections['pillar'] ?? '-',
                        'gameplay_data' => $reflections['gameplay_data'] ?? [
                            'level' => $res->diagnostic_level ?? 'Basic',
                            'gridSize' => 5,
                            'targetSteps' => 10,
                            'startTile' => ['x' => 0, 'y' => 4],
                            'wuduTile' => ['x' => 2, 'y' => 3],
                            'kitabTile' => ['x' => 3, 'y' => 1],
                            'masjidTile' => ['x' => 4, 'y' => 0],
                            'obstacles' => [['x' => 1, 'y' => 2]],
                            'workspace' => ['FORWARD', 'TURN_LEFT', 'FORWARD', 'LOOP_2', 'FORWARD'],
                        ],
                    ];
                }
            }

            if (!empty($studentReplays)) {
                $allData[] = [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'students' => $studentReplays,
                ];
            }
        }

        $this->allGroupsReplayData = $allData;
        $this->showAllGroupsReplayModal = true;
        $this->dispatch('init-all-groups-canvas', data: $allData);
    }

    public function closeAllGroupsReplay()
    {
        $this->showAllGroupsReplayModal = false;
        $this->allGroupsReplayData = [];
    }

    public function moveStudent($studentId, $fromGroupId, $toGroupId)
    {
        DB::transaction(function () use ($studentId, $fromGroupId, $toGroupId) {
            if ($fromGroupId) {
                $from = StudentGroup::find($fromGroupId);
                if ($from && $from->game_session_id === $this->session->id) {
                    $from->students()->detach($studentId);
                }
            }
            if ($toGroupId) {
                $to = StudentGroup::find($toGroupId);
                if ($to && $to->game_session_id === $this->session->id) {
                    $to->students()->attach($studentId);
                }
            }
        });
    }

    public function render()
    {
        $students = $this->session->classroom->students()
            ->with(['diagnosticResults' => function($q) {
                $q->where('game_session_id', $this->session->id);
            }, 'results' => function($q) {
                $q->where('game_session_id', $this->session->id);
            }])
            ->get();

        $groups = $this->session->studentGroups()
            ->with(['students' => function($q) {
                $q->with(['diagnosticResults' => function($sq) {
                    $sq->where('game_session_id', $this->session->id);
                }, 'results' => function($sq) {
                    $sq->where('game_session_id', $this->session->id);
                }]);
            }])
            ->get();

        // Process reflections for Tab 3 (Rekap Jurnal Refleksi)
        $emotionsData = [
            'Senang' => 0,
            'Penasaran' => 0,
            'Sempat Kesal' => 0,
            'Biasa Saja' => 0,
        ];
        $pillarsData = [
            'Dekomposisi' => 0,
            'Pengenalan Pola' => 0,
            'Abstraksi' => 0,
            'Algoritma' => 0,
        ];

        foreach ($students as $student) {
            $lastResult = $student->results->last();
            if ($lastResult && is_array($lastResult->reflections)) {
                $emotion = $lastResult->reflections['emotion'] ?? null;
                $pillar = $lastResult->reflections['pillar'] ?? null;

                if ($emotion && isset($emotionsData[$emotion])) {
                    $emotionsData[$emotion]++;
                } elseif ($emotion) {
                    $emotionsData[$emotion] = 1;
                }

                if ($pillar && isset($pillarsData[$pillar])) {
                    $pillarsData[$pillar]++;
                } elseif ($pillar) {
                    $pillarsData[$pillar] = 1;
                }
            }
        }

        return view('livewire.teacher.session-show', [
            'students' => $students,
            'groups' => $groups->map(function ($group) {
                $totalStars = 0;
                $highestLevel = 0;
                foreach ($group->students as $student) {
                    $lastResult = $student->results->last();
                    if ($lastResult) {
                        $totalStars += $lastResult->total_stars;
                        // For level mapping, we might not have 'level_number', but we have diagnostic_level
                        $levelNum = $lastResult->diagnostic_level == 'Advanced' ? 3 : ($lastResult->diagnostic_level == 'Intermediate' ? 2 : 1);
                        if ($levelNum > $highestLevel) {
                            $highestLevel = $levelNum;
                        }
                    }
                }
                $group->total_stars = $totalStars;
                $group->highest_level = $highestLevel;
                return $group;
            })->sortByDesc('total_stars'),
            'emotionsData' => $emotionsData,
            'pillarsData' => $pillarsData,
        ]);
    }
}
