<div class="flex flex-col gap-6 w-full max-w-6xl mx-auto" wire:poll.2s>
    <x-slot:title>Kelola Sesi - {{ $session->name }}</x-slot:title>

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" size="sm" icon="arrow-left" href="{{ route('teacher.classrooms.show', $session->classroom_id) }}" wire:navigate class="hidden sm:flex" />
            <div>
                <flux:heading size="xl" class="flex items-center gap-2">
                    <flux:button variant="ghost" size="sm" icon="arrow-left" href="{{ route('teacher.classrooms.show', $session->classroom_id) }}" wire:navigate class="sm:hidden -ml-2" />
                    {{ $session->name }}
                </flux:heading>
                <flux:subheading class="flex items-center gap-2 mt-1">
                    <span>Status:</span> 
                    <flux:badge size="sm" color="{{ match($session->status) {
                        'waiting' => 'zinc',
                        'diagnostic' => 'blue',
                        'grouping' => 'orange',
                        'playing' => 'emerald',
                        'finished' => 'red',
                    } }}">
                        {{ strtoupper($session->status) }}
                    </flux:badge>
                    <span class="text-xs text-zinc-400">|</span>
                    <span>Durasi: {{ $session->duration_minutes }} Menit</span>
                    @if($session->status === 'playing')
                        <span class="text-xs text-zinc-400">|</span>
                        <span class="text-emerald-600 font-bold flex items-center gap-1"
                              x-data="{ 
                                  remaining: {{ $this->remainingTime }},
                                  init() {
                                      setInterval(() => {
                                          if(this.remaining > 0) this.remaining--;
                                      }, 1000);
                                  },
                                  formatTime() {
                                      let m = Math.floor(this.remaining / 60);
                                      let s = this.remaining % 60;
                                      return (m < 10 ? '0'+m : m) + ':' + (s < 10 ? '0'+s : s);
                                  }
                              }">
                            <flux:icon.clock class="size-4" />
                            Sisa Waktu: <span x-text="formatTime()"></span>
                        </span>
                    @endif
                </flux:subheading>
            </div>
        </div>
        
        <div class="flex gap-2">
            @if($session->status === 'waiting')
                <flux:button variant="primary" icon="play" wire:click="startDiagnostic">Mulai Tes Diagnostik</flux:button>
            @elseif($session->status === 'diagnostic')
                <flux:modal.trigger name="generate-groups">
                    <flux:button variant="primary" icon="users">Bentuk Kelompok Otomatis</flux:button>
                </flux:modal.trigger>
            @elseif($session->status === 'grouping')
                <flux:button variant="primary" icon="play" wire:click="startGame">Kunci Kelompok & Mulai Game ({{ $session->duration_minutes }} Menit)</flux:button>
            @elseif($session->status === 'playing')
                <flux:button variant="danger" icon="stop" wire:click="stopSession">Hentikan Sesi</flux:button>
                <flux:button variant="primary" icon="film" wire:click="openAllGroupsReplay" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-black shadow-lg">
                    🎬 Putar Ulang Serentak
                </flux:button>
            @elseif($session->status === 'finished')
                <flux:button variant="primary" icon="film" wire:click="openAllGroupsReplay" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-black shadow-lg">
                    🎬 Putar Ulang Serentak
                </flux:button>
            @endif
        </div>
    </div>

    <!-- Tampilan berdasarkan status -->
    @if($session->status === 'waiting')
        <flux:card class="text-center py-12">
            <flux:icon.clock class="size-12 mx-auto text-zinc-300 mb-4" />
            <flux:heading size="lg">Sesi Belum Dimulai</flux:heading>
            <flux:subheading>Siswa belum dapat melihat sesi ini. Klik tombol di atas untuk memulai tes diagnostik.</flux:subheading>
        </flux:card>

    @elseif($session->status === 'diagnostic')
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($students as $student)
                @php
                    $result = $student->diagnosticResults->first();
                @endphp
                <flux:card class="flex items-center gap-4 {{ $result ? 'border-emerald-500 bg-emerald-50/10' : '' }}">
                    <flux:avatar :name="$student->name" size="sm" />
                    <div class="flex-1">
                        <div class="font-medium text-sm truncate">{{ $student->name }}</div>
                        <div class="text-xs text-zinc-500">
                            @if($result)
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">Level: {{ $result->level ?? 'Basic' }}</span>
                            @else
                                Sedang mengerjakan tes...
                            @endif
                        </div>
                    </div>
                    @if($result)
                        <div class="text-right">
                            <div class="font-bold text-lg text-emerald-600">{{ $result->score }}</div>
                            <flux:badge size="xs" color="{{ match($result->level ?? 'Basic') {
                                'Advanced' => 'emerald',
                                'Intermediate' => 'blue',
                                default => 'orange',
                            } }}">
                                {{ $result->level ?? 'Basic' }}
                            </flux:badge>
                        </div>
                    @else
                        <flux:icon.arrow-path class="size-4 animate-spin text-zinc-400" />
                    @endif
                </flux:card>
            @endforeach
        </div>

    @elseif(in_array($session->status, ['grouping', 'playing', 'finished']))
        @if(in_array($session->status, ['playing', 'finished']))
            <div x-data="{ activeTab: 'groups' }">
                <div class="flex overflow-x-auto space-x-4 border-b border-zinc-200 dark:border-zinc-700 mb-6">
                    <button @click="activeTab = 'groups'" :class="activeTab === 'groups' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold border-b-2' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'" class="px-4 py-3 text-sm font-medium focus:outline-none transition-colors whitespace-nowrap flex items-center gap-2">
                        <flux:icon.users class="size-4" /> Peringkat Kelompok
                    </button>
                    <button @click="activeTab = 'individuals'" :class="activeTab === 'individuals' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold border-b-2' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'" class="px-4 py-3 text-sm font-medium focus:outline-none transition-colors whitespace-nowrap flex items-center gap-2">
                        <flux:icon.user class="size-4" /> Peringkat Individu
                    </button>
                    <button @click="activeTab = 'reflections'" :class="activeTab === 'reflections' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold border-b-2' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'" class="px-4 py-3 text-sm font-medium focus:outline-none transition-colors whitespace-nowrap flex items-center gap-2">
                        <flux:icon.chart-bar class="size-4" /> Jurnal Refleksi
                    </button>
                </div>

                <div x-show="activeTab === 'groups'" x-transition>
                <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white rounded-3xl p-6 shadow-xl mb-6">
                    <div class="flex items-center justify-between mb-4 border-b border-white/20 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="bg-white/20 p-2 rounded-xl backdrop-blur-md">
                                <flux:icon.trophy class="size-6 text-yellow-300" />
                            </div>
                            <div>
                                <h3 class="text-lg font-black">Papan Peringkat Live Kelompok</h3>
                                <p class="text-xs text-emerald-100">Pantau skor perolehan kelompok siswa secara real-time.</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div class="bg-white/20 text-white font-black px-3 py-1 rounded-full text-xs border border-white/30">
                                {{ strtoupper($session->status) }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($groups->sortByDesc('score')->values() as $rank => $g)
                            <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-2xl border border-white/20 flex items-center justify-between transition-all">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="size-7 rounded-lg flex items-center justify-center font-black text-xs shrink-0 {{ match($rank) {
                                        0 => 'bg-amber-400 text-slate-900',
                                        1 => 'bg-slate-300 text-slate-900',
                                        2 => 'bg-amber-700 text-white',
                                        default => 'bg-white/20 text-white',
                                    } }}">
                                        #{{ $rank + 1 }}
                                    </span>
                                    <div class="truncate">
                                        <div class="font-bold text-sm truncate">{{ $g->name }}</div>
                                        <div class="text-[10px] text-emerald-100 flex items-center gap-1">
                                            <span>{{ $g->students->count() }} Siswa</span>
                                            <span>•</span>
                                            <span class="font-semibold">Lv {{ $g->highest_level ?? 1 }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="font-black text-sm text-yellow-300 shrink-0 flex flex-col items-end">
                                    <span>{{ number_format($g->score ?? 0) }} ⭐</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                </div>

                <div x-show="activeTab === 'individuals'" x-transition style="display: none;">
                <flux:card>
                    <div class="mb-4">
                        <flux:heading size="lg">Peringkat Perorangan (Real-time)</flux:heading>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-zinc-500 uppercase bg-zinc-50 dark:bg-zinc-800/50">
                                <tr>
                                    <th class="px-4 py-3 rounded-l-lg">Siswa</th>
                                    <th class="px-4 py-3">Kelompok</th>
                                    <th class="px-4 py-3 text-center">Diagnostik</th>
                                    <th class="px-4 py-3 text-center">Progres (Bintang)</th>
                                    <th class="px-4 py-3 text-center">Langkah</th>
                                    <th class="px-4 py-3 text-center">Percobaan</th>
                                    <th class="px-4 py-3 rounded-r-lg text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students->sortByDesc(fn($s) => $s->results->last()?->total_stars ?? 0) as $s)
                                    @php
                                        $lastRes = $s->results->last();
                                        $grp = $groups->first(fn($g) => $g->students->contains('id', $s->id));
                                    @endphp
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                                        <td class="px-4 py-3 font-semibold flex items-center gap-2">
                                            <flux:avatar :name="$s->name" size="xs" />
                                            {{ $s->name }}
                                        </td>
                                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                            {{ $grp->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <flux:badge size="sm" color="{{ match($s->diagnosticResults->first()?->level) { 'Advanced' => 'emerald', 'Intermediate' => 'blue', default => 'orange' } }}">
                                                {{ $s->diagnosticResults->first()?->level ?? 'Basic' }}
                                            </flux:badge>
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-amber-500">
                                            {{ $lastRes?->total_stars ?? 0 }} ⭐
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-blue-500">
                                            {{ $lastRes?->total_steps ?? 0 }} 👣
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-zinc-500">
                                            {{ $lastRes?->total_attempts ?? 0 }}x
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($lastRes && $lastRes->status === 'finished')
                                                <flux:badge size="sm" color="emerald">Selesai</flux:badge>
                                            @elseif($lastRes && $lastRes->status === 'timeout')
                                                <flux:badge size="sm" color="red">Habis Waktu</flux:badge>
                                            @else
                                                <flux:badge size="sm" color="zinc">Belum/Sedang Main</flux:badge>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </flux:card>
                </div>

                <div x-show="activeTab === 'reflections'" x-transition style="display: none;">
                <flux:card>
                    <div class="flex items-center justify-between mb-6">
                        <flux:heading size="lg">Rekapitulasi Jurnal Refleksi PSE & CT</flux:heading>
                        <flux:button size="sm" variant="outline" icon="document-arrow-down" onclick="window.print()">Ekspor Data</flux:button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Emosi -->
                        <div class="bg-zinc-50 dark:bg-zinc-800/40 p-5 rounded-2xl border border-zinc-100 dark:border-zinc-700">
                            <flux:heading size="md" class="mb-4">Distribusi Emosi Siswa (PSE)</flux:heading>
                            <div class="space-y-4">
                                @foreach($emotionsData as $emo => $count)
                                    @php $percent = $students->count() > 0 ? ($count / $students->count()) * 100 : 0; @endphp
                                    <div>
                                        <div class="flex justify-between text-sm font-medium mb-1">
                                            <span>
                                                {{ match($emo) {
                                                    'Senang' => '😊 Senang',
                                                    'Penasaran' => '🤔 Penasaran',
                                                    'Sempat Kesal' => '😤 Sempat Kesal',
                                                    default => '😐 Biasa Saja'
                                                } }}
                                            </span>
                                            <span class="text-zinc-500">{{ $count }} Siswa</span>
                                        </div>
                                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2.5">
                                            <div class="bg-emerald-500 h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Pilar CT -->
                        <div class="bg-zinc-50 dark:bg-zinc-800/40 p-5 rounded-2xl border border-zinc-100 dark:border-zinc-700">
                            <flux:heading size="md" class="mb-4">Pilar CT yang Paling Membantu</flux:heading>
                            <div class="space-y-4">
                                @foreach($pillarsData as $pil => $count)
                                    @php $percent = $students->count() > 0 ? ($count / $students->count()) * 100 : 0; @endphp
                                    <div>
                                        <div class="flex justify-between text-sm font-medium mb-1">
                                            <span>{{ $pil }}</span>
                                            <span class="text-zinc-500">{{ $count }} Siswa</span>
                                        </div>
                                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2.5">
                                            <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </flux:card>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($groups as $group)
                <flux:card>
                    <div class="flex justify-between items-center mb-4">
                        <flux:heading size="lg">{{ $group->name }}</flux:heading>
                        <flux:badge size="sm" color="emerald" icon="sparkles">{{ number_format($group->score ?? 0) }} PTS</flux:badge>
                    </div>
                    <div class="space-y-2 min-h-[50px] p-2 -m-2 rounded-lg transition-colors border-2 border-transparent"
                         x-data="{
                            init() {
                                @if(!in_array($session->status, ['playing', 'finished']))
                                if (typeof Sortable !== 'undefined') {
                                    Sortable.create(this.$el, {
                                        group: 'students',
                                        animation: 150,
                                        ghostClass: 'opacity-50',
                                        onEnd: (evt) => {
                                            if (evt.from !== evt.to) {
                                                $wire.moveStudent(
                                                    evt.item.dataset.id,
                                                    evt.from.dataset.group,
                                                    evt.to.dataset.group
                                                );
                                            }
                                        }
                                    });
                                }
                                @endif
                            }
                         }"
                         data-group="{{ $group->id }}">
                        @foreach($group->students as $student)
                            @php
                                $diag = $student->diagnosticResults->first();
                                $diagScore = $diag?->score ?? 0;
                                $level = $diag?->level ?? 'Basic';

                                $studentResults = $student->results;
                                $gameRes = $studentResults->last(); // Percobaan terbaru
                                $attemptsCount = $gameRes?->total_attempts ?? 0;
                                
                                $gameStars = $gameRes?->total_stars ?? 0;
                                $gameSteps = $gameRes?->total_steps ?? 0;
                                $reflections = $gameRes?->reflections ?? [];
                                
                                $duration = $reflections['duration_seconds'] ?? null;
                                $speedBonus = $reflections['speed_bonus'] ?? 0;
                                $totalPts = $reflections['total_points'] ?? ($gameStars * 100);
                                $emotion = $reflections['emotion'] ?? null;
                                $pillar = $reflections['pillar'] ?? null;
                            @endphp

                            <div data-id="{{ $student->id }}" class="bg-zinc-50 dark:bg-zinc-800/80 p-3 rounded-2xl cursor-grab active:cursor-grabbing border border-zinc-200 dark:border-zinc-700 shadow-sm space-y-2 text-left">
                                <!-- Top Row: Avatar, Student Name, Level Badge & Individual Game Score -->
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <flux:avatar :name="$student->name" size="xs" />
                                        <div class="min-w-0">
                                            <div class="text-xs font-bold text-zinc-800 dark:text-white truncate">
                                                {{ $student->name }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <!-- Diagnostic Level -->
                                        <flux:badge size="xs" color="{{ match($level) {
                                            'Advanced' => 'emerald',
                                            'Intermediate' => 'blue',
                                            default => 'orange',
                                        } }}">
                                            {{ $level }} ({{ $diagScore }})
                                        </flux:badge>

                                        @if($attemptsCount > 0)
                                            <!-- Total Poin Siswa -->
                                            <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-950/60 px-2 py-0.5 rounded-full">
                                                +{{ number_format($totalPts) }} PTS ⭐
                                            </span>
                                        @else
                                            <span class="text-[10px] font-semibold text-zinc-400 bg-zinc-100 dark:bg-zinc-700/50 px-2 py-0.5 rounded-full">
                                                Belum Main ⏳
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Riwayat Gameplay & Refleksi PSE (Jika sudah bermain) -->
                                @if($attemptsCount > 0)
                                    <div class="bg-white dark:bg-zinc-900/70 p-2.5 rounded-xl border border-zinc-100 dark:border-zinc-700/60 text-[11px] space-y-1.5 font-medium">
                                        <div class="flex items-center justify-between text-zinc-600 dark:text-zinc-300">
                                            <span class="flex items-center gap-1">
                                                <span>⭐ Bintang:</span>
                                                <strong class="text-amber-500 font-bold">{{ str_repeat('⭐', $gameStars) }} ({{ $gameStars }} Star)</strong>
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <span>⏱️ Waktu:</span>
                                                <strong class="text-blue-600 dark:text-blue-400 font-bold">{{ $duration }}s</strong>
                                                @if($speedBonus > 0)
                                                    <span class="text-[10px] text-amber-500 font-black">(+{{ $speedBonus }}⚡)</span>
                                                @endif
                                            </span>
                                        </div>

                                        <div class="flex items-center justify-between text-zinc-500 text-[10px] pt-1.5 border-t border-zinc-100 dark:border-zinc-800">
                                            <span class="flex items-center gap-1">
                                                <span>😊 Emosi:</span>
                                                <strong class="text-zinc-700 dark:text-zinc-200">{{ $emotion ?? '-' }}</strong>
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <span>🎮 Total Main:</span>
                                                <strong class="text-purple-600 dark:text-purple-400 font-bold">{{ $attemptsCount }}x Percobaan</strong>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Replay Button -->
                                    <button type="button" wire:click="openReplay({{ $student->id }})" class="w-full mt-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/80 text-emerald-700 dark:text-emerald-300 font-extrabold text-[11px] py-1.5 px-3 rounded-xl border border-emerald-200 dark:border-emerald-800 transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm active:scale-95">
                                        <span>🎬 Putar Ulang</span>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- MODAL REPLAY PERMAINAN SISWA (TEACHER VIEW)               -->
    <!-- ========================================================= -->
    <div x-data="santriBotReplayEngine()" x-show="$wire.showReplayModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 text-left">
        <div class="bg-white dark:bg-zinc-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-emerald-100 dark:border-zinc-700 space-y-5 animate-scaleUp">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-700 pb-3">
                <div>
                    <h3 class="text-base font-black text-zinc-800 dark:text-white flex items-center gap-2">
                        <span>🎬 Replay Algoritma Siswa</span>
                    </h3>
                    <p class="text-xs text-zinc-500 font-medium" x-text="$wire.replayStudent ? 'Siswa: ' + $wire.replayStudent.name : ''"></p>
                </div>
                <button type="button" wire:click="closeReplay" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-white text-xl font-bold p-1 cursor-pointer">&times;</button>
            </div>

            <!-- Multi-Attempt Selection Bar -->
            @if(!empty($replayAttempts) && count($replayAttempts) > 1)
                <div class="space-y-1.5">
                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-wider">Pilih Riwayat Percobaan Permainan:</span>
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 max-w-full">
                        @foreach($replayAttempts as $idx => $att)
                            <button type="button" wire:click="selectAttempt({{ $idx }})"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer border shadow-sm {{ $selectedAttemptIndex === $idx ? 'bg-emerald-600 text-white border-emerald-500 ring-2 ring-emerald-400/50' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-200' }}">
                                🎮 Percobaan #{{ $att['attempt_number'] }} ({{ $att['duration'] }}s, {{ $att['stars'] }}⭐)
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Student Performance Summary Card -->
            @if(!empty($replayAttempts) && isset($replayAttempts[$selectedAttemptIndex]))
                @php $currentAtt = $replayAttempts[$selectedAttemptIndex]; @endphp
                <div class="bg-zinc-50 dark:bg-zinc-900/70 p-3 rounded-2xl border border-zinc-200 dark:border-zinc-700 grid grid-cols-4 gap-1 text-center text-xs font-semibold">
                    <div>
                        <span class="text-zinc-400 block text-[9px] uppercase font-bold">🎮 Percobaan</span>
                        <span class="font-black text-emerald-600 dark:text-emerald-400 text-xs">#{{ $currentAtt['attempt_number'] }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-400 block text-[9px] uppercase font-bold">⭐ Skor</span>
                        <span class="font-black text-emerald-600 dark:text-emerald-400 text-xs">+{{ $currentAtt['points'] }} PTS</span>
                    </div>
                    <div>
                        <span class="text-zinc-400 block text-[9px] uppercase font-bold">⏱️ Durasi</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400 text-xs">{{ $currentAtt['duration'] }}s</span>
                    </div>
                    <div>
                        <span class="text-zinc-400 block text-[9px] uppercase font-bold">🧩 Pilar CT</span>
                        <span class="font-bold text-amber-500 text-xs truncate block">{{ $currentAtt['pillar'] }}</span>
                    </div>
                </div>
            @endif

            <!-- Replay Canvas Screen -->
            <div class="space-y-4">
                <div class="relative mx-auto flex justify-center items-center bg-zinc-950 rounded-2xl p-2 shadow-inner overflow-hidden border-2 border-emerald-500/30">
                    <canvas id="replayCanvas" width="340" height="340" class="rounded-xl shadow-md max-w-full h-auto"></canvas>
                </div>

                <!-- Sequence Chips with Active Highlighting -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-[11px] font-black text-zinc-500 uppercase tracking-wider">
                        <span>Urutan Perintah Siswa:</span>
                        <span x-text="workspace.length + ' Langkah'"></span>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900/60 rounded-2xl border-2 border-dashed border-zinc-200 dark:border-zinc-700 flex flex-wrap gap-1.5 items-center min-h-[55px] max-h-[110px] overflow-y-auto">
                        <template x-if="workspace.length === 0">
                            <div class="w-full text-center text-zinc-400 text-xs italic py-2">
                                Belum ada data rekaman permainan untuk siswa ini.
                            </div>
                        </template>

                        <template x-for="(step, idx) in workspace" :key="idx">
                            <div class="px-2.5 py-1 rounded-xl font-extrabold text-[11px] flex items-center gap-1 transition-all shadow-sm"
                                 :class="currentStepIndex === idx ? 'bg-amber-400 text-black ring-2 ring-amber-300 scale-110 shadow-lg font-black' : 'bg-emerald-600 text-white opacity-80'">
                                <span x-text="(idx + 1) + '.'"></span>
                                <span x-text="getStepLabel(step)"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Playback Action Buttons -->
                <div class="flex items-center gap-2 pt-2">
                    <button type="button" @click="startReplay()" :disabled="isPlaying || workspace.length === 0" class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold py-3 rounded-2xl shadow-md transition-all flex items-center justify-center gap-2 text-xs disabled:opacity-40 cursor-pointer">
                        <span>▶️ Putar Ulang</span>
                    </button>

                    <button type="button" @click="resetReplay()" :disabled="isPlaying" class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 text-zinc-800 dark:text-white font-extrabold px-4 py-3 rounded-2xl transition-all flex items-center justify-center gap-1.5 text-xs disabled:opacity-40 cursor-pointer">
                        <span>🔄 Reset</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Santri Bot Replay Engine Script -->
    <script>
        function santriBotReplayEngine() {
            return {
                level: 'Basic',
                gridSize: 5,
                targetSteps: 10,
                workspace: [],
                isPlaying: false,
                currentStepIndex: -1,
                botX: 0,
                botY: 0,
                botDir: 3,
                hasWudu: false,
                hasKitab: false,
                startTile: { x: 0, y: 0 },
                wuduTile: { x: 0, y: 4 },
                kitabTile: { x: 4, y: 0 },
                masjidTile: { x: 4, y: 4 },
                obstacles: [],

                init() {
                    this.$watch('$wire.showReplayModal', (show) => {
                        if (show) {
                            setTimeout(() => {
                                if (this.$wire.replayData) {
                                    this.loadData(this.$wire.replayData);
                                }
                            }, 100);
                        }
                    });

                    Livewire.on('init-replay-canvas', (event) => {
                        let data = event.data || event[0]?.data;
                        setTimeout(() => {
                            if (data) {
                                this.loadData(data);
                            } else if (this.$wire.replayData) {
                                this.loadData(this.$wire.replayData);
                            }
                        }, 100);
                    });
                },

                loadData(data) {
                    if (!data) return;
                    this.level = data.level || 'Basic';
                    this.gridSize = data.gridSize || 5;
                    this.targetSteps = data.targetSteps || 10;
                    this.startTile = data.startTile || { x: 0, y: 0 };
                    this.wuduTile = data.wuduTile || { x: 0, y: 4 };
                    this.kitabTile = data.kitabTile || { x: 4, y: 0 };
                    this.masjidTile = data.masjidTile || { x: 4, y: 4 };
                    this.obstacles = data.obstacles || [];
                    this.workspace = data.workspace || [];
                    this.resetReplay();
                },

                resetReplay() {
                    this.botX = this.startTile.x;
                    this.botY = this.startTile.y;
                    this.botDir = 3;
                    this.hasWudu = false;
                    this.hasKitab = false;
                    this.isPlaying = false;
                    this.currentStepIndex = -1;
                    this.drawCanvas();
                },

                getStepLabel(cmd) {
                    return {
                        'FORWARD': '⬆️ Maju',
                        'TURN_LEFT': '↩️ Kiri',
                        'TURN_RIGHT': '↪️ Kanan',
                        'LOOP_2': '🔁 Loop',
                    }[cmd] || cmd;
                },

                async startReplay() {
                    if (this.workspace.length === 0) return;
                    this.resetReplay();
                    this.isPlaying = true;

                    // Expand loops
                    let expandedSteps = [];
                    let stepSourceIndices = [];
                    for (let i = 0; i < this.workspace.length; i++) {
                        let cmd = this.workspace[i];
                        if (cmd === 'LOOP_2') {
                            let prevAction = null;
                            for (let p = i - 1; p >= 0; p--) {
                                if (this.workspace[p] !== 'LOOP_2') {
                                    prevAction = this.workspace[p];
                                    break;
                                }
                            }
                            let nextAction = null;
                            if (!prevAction) {
                                for (let n = i + 1; n < this.workspace.length; n++) {
                                    if (this.workspace[n] !== 'LOOP_2') {
                                        nextAction = this.workspace[n];
                                        break;
                                    }
                                }
                            }
                            let targetCmd = prevAction || nextAction;
                            if (targetCmd) {
                                expandedSteps.push(targetCmd);
                                stepSourceIndices.push(i);
                            }
                        } else {
                            expandedSteps.push(cmd);
                            stepSourceIndices.push(i);
                        }
                    }

                    for (let stepIndex = 0; stepIndex < expandedSteps.length; stepIndex++) {
                        if (!this.isPlaying) break;
                        this.currentStepIndex = stepSourceIndices[stepIndex];
                        let cmd = expandedSteps[stepIndex];

                        if (cmd === 'FORWARD') {
                            let dx = [1, 0, -1, 0][this.botDir];
                            let dy = [0, 1, 0, -1][this.botDir];
                            let nextX = this.botX + dx;
                            let nextY = this.botY + dy;

                            if (nextX >= 0 && nextX < this.gridSize && nextY >= 0 && nextY < this.gridSize) {
                                if (!this.obstacles.some(o => o.x === nextX && o.y === nextY)) {
                                    this.botX = nextX;
                                    this.botY = nextY;
                                }
                            }
                            if (this.botX === this.wuduTile.x && this.botY === this.wuduTile.y) this.hasWudu = true;
                            if (this.botX === this.kitabTile.x && this.botY === this.kitabTile.y) this.hasKitab = true;
                        } else if (cmd === 'TURN_LEFT') {
                            this.botDir = (this.botDir + 3) % 4;
                        } else if (cmd === 'TURN_RIGHT') {
                            this.botDir = (this.botDir + 1) % 4;
                        }

                        this.drawCanvas();
                        await new Promise(r => setTimeout(r, 450));
                    }

                    this.isPlaying = false;
                },

                drawCanvas() {
                    const canvas = document.getElementById('replayCanvas');
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    const size = canvas.width;
                    const cellSize = size / this.gridSize;

                    ctx.fillStyle = '#09090b';
                    ctx.fillRect(0, 0, size, size);

                    for (let r = 0; r < this.gridSize; r++) {
                        for (let c = 0; c < this.gridSize; c++) {
                            ctx.strokeStyle = '#10b981';
                            ctx.lineWidth = 2;
                            ctx.strokeRect(c * cellSize, r * cellSize, cellSize, cellSize);
                            ctx.fillStyle = '#18181b';
                            ctx.fillRect(c * cellSize + 2, r * cellSize + 2, cellSize - 4, cellSize - 4);
                        }
                    }

                    this.drawTile(ctx, this.startTile.x, this.startTile.y, cellSize, '#064e3b', '🏠', 'Asrama');
                    this.drawTile(ctx, this.wuduTile.x, this.wuduTile.y, cellSize, this.hasWudu ? '#0284c7' : '#0369a1', '💧', 'Wudu');
                    this.drawTile(ctx, this.kitabTile.x, this.kitabTile.y, cellSize, this.hasKitab ? '#d97706' : '#b45309', '📖', 'Kitab');
                    this.obstacles.forEach(o => this.drawTile(ctx, o.x, o.y, cellSize, '#451a03', '🌧️', 'Becek'));
                    this.drawTile(ctx, this.masjidTile.x, this.masjidTile.y, cellSize, '#047857', '🕌', 'Masjid');

                    const bx = this.botX * cellSize + cellSize / 2;
                    const by = this.botY * cellSize + cellSize / 2;

                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(bx, by, cellSize / 2.8, 0, Math.PI * 2);
                    ctx.fillStyle = '#10b981';
                    ctx.fill();
                    ctx.lineWidth = 3;
                    ctx.strokeStyle = '#ffffff';
                    ctx.stroke();

                    ctx.font = `${cellSize * 0.4}px sans-serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('👳', bx, by);

                    const angles = [0, Math.PI / 2, Math.PI, -Math.PI / 2];
                    const arrowX = bx + Math.cos(angles[this.botDir]) * (cellSize / 2.4);
                    const arrowY = by + Math.sin(angles[this.botDir]) * (cellSize / 2.4);
                    ctx.beginPath();
                    ctx.arc(arrowX, arrowY, 5, 0, Math.PI * 2);
                    ctx.fillStyle = '#f59e0b';
                    ctx.fill();
                    ctx.strokeStyle = '#ffffff';
                    ctx.lineWidth = 1.5;
                    ctx.stroke();
                    ctx.restore();
                },

                drawTile(ctx, x, y, cellSize, color, emoji, label) {
                    const px = x * cellSize;
                    const py = y * cellSize;
                    ctx.fillStyle = color;
                    ctx.fillRect(px + 3, py + 3, cellSize - 6, cellSize - 6);
                    ctx.strokeStyle = '#ffffff';
                    ctx.lineWidth = 1.5;
                    ctx.strokeRect(px + 3, py + 3, cellSize - 6, cellSize - 6);
                    ctx.font = `${cellSize * 0.35}px sans-serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(emoji, px + cellSize / 2, py + cellSize / 2.4);
                    ctx.font = `bold ${Math.max(9, cellSize * 0.16)}px sans-serif`;
                    ctx.fillStyle = '#ffffff';
                    ctx.fillText(label, px + cellSize / 2, py + cellSize * 0.8);
                }
            };
        }
    </script>

    <flux:modal name="generate-groups" class="min-w-[450px]">
        <form wire:submit="generateGroups">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Pembagian Kelompok Otomatis</flux:heading>
                    <flux:subheading>Pilih strategi pengelompokan berdasarkan hasil tes diagnostik siswa.</flux:subheading>
                </div>
                
                <flux:input wire:model="groupCount" type="number" min="1" max="20" label="Jumlah Kelompok" placeholder="Misal: 4" required />

                <div class="space-y-3">
                    <flux:label>Strategi Pembagian</flux:label>
                    
                    <label class="flex items-start gap-3 p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <input type="radio" wire:model="strategy" value="heterogeneous" class="mt-1 text-emerald-600 focus:ring-emerald-500" />
                        <div>
                            <div class="font-medium text-sm">Heterogen (Peer Tutoring)</div>
                            <div class="text-xs text-zinc-500">Membagi siswa Advanced, Intermediate, dan Basic secara merata ke tiap kelompok.</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <input type="radio" wire:model="strategy" value="homogeneous" class="mt-1 text-emerald-600 focus:ring-emerald-500" />
                        <div>
                            <div class="font-medium text-sm">Homogen (Kemampuan Sejenis)</div>
                            <div class="text-xs text-zinc-500">Mengelompokkan siswa dengan tingkat kemampuan yang setara.</div>
                        </div>
                    </label>
                </div>

                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Bagi Kelompok</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    <!-- ========================================================= -->
    <!-- MODAL REPLAY BERSAMAAN SEMUA KELOMPOK (TEACHER GRAND VIEW) -->
    <!-- ========================================================= -->
    <div x-data="allGroupsReplayEngine()" x-show="$wire.showAllGroupsReplayModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4 sm:p-6 text-left">
        <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 max-w-6xl w-full max-h-[92vh] flex flex-col justify-between shadow-2xl border border-emerald-500/30 space-y-4 animate-scaleUp overflow-hidden">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3 shrink-0">
                <div>
                    <h2 class="text-lg font-black text-zinc-800 dark:text-white flex items-center gap-2">
                        <span>🎬 Replay Permainan Bersamaan Semua Kelompok</span>
                    </h2>
                    <p class="text-xs text-zinc-500 font-medium">Memutar ulang simulasi pergerakan Bot Santri dari solusi algoritma setiap kelompok secara serentak.</p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="startAllReplays()" :disabled="isPlaying" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold text-xs px-4 py-2 rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer disabled:opacity-40">
                        <span>▶️ Putar Serentak</span>
                    </button>
                    <button type="button" @click="resetAllReplays()" :disabled="isPlaying" class="bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 text-zinc-800 dark:text-white font-extrabold text-xs px-3 py-2 rounded-xl transition-all flex items-center gap-1 cursor-pointer disabled:opacity-40">
                        <span>🔄 Reset</span>
                    </button>
                    <button type="button" wire:click="closeAllGroupsReplay" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-white text-2xl font-bold p-1 cursor-pointer ml-2">&times;</button>
                </div>
            </div>

            <!-- Multi-Canvas Grid Area -->
            <div class="flex-1 overflow-y-auto pr-1 space-y-6">
                <template x-if="groups.length === 0">
                    <div class="py-16 text-center text-zinc-400 text-sm italic">
                        Belum ada data rekaman hasil permainan kelompok yang tersimpan.
                    </div>
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <template x-for="(g, gIdx) in groups" :key="gIdx">
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 space-y-3 shadow-md flex flex-col justify-between">
                            <!-- Group Title Header -->
                            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                                <span class="font-black text-sm text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                    <span>👥</span>
                                    <span x-text="g.group_name"></span>
                                </span>
                                <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 px-2 py-0.5 rounded-full" x-text="g.students.length + ' Siswa'"></span>
                            </div>

                            <!-- Student Replay Cards inside Group -->
                            <div class="space-y-4">
                                <template x-for="(st, sIdx) in g.students" :key="sIdx">
                                    <div class="bg-white dark:bg-zinc-900 p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 space-y-2">
                                        <div class="flex items-center justify-between text-xs font-bold">
                                            <span class="text-zinc-800 dark:text-white flex items-center gap-1">
                                                <span>👳</span>
                                                <span x-text="st.student_name"></span>
                                            </span>
                                            <span class="text-amber-500 font-extrabold text-[11px]" x-text="'+' + st.points + ' PTS ⭐'"></span>
                                        </div>

                                        <!-- Canvas element per student -->
                                        <div class="relative mx-auto flex justify-center items-center bg-zinc-950 rounded-xl p-1 shadow-inner border border-emerald-500/20">
                                            <canvas :id="'allGroupCanvas_' + gIdx + '_' + sIdx" width="260" height="260" class="rounded-lg max-w-full h-auto"></canvas>
                                        </div>

                                        <!-- Active Step Display -->
                                        <div class="text-[10px] font-semibold text-zinc-400 flex items-center justify-between">
                                            <span>Algoritma:</span>
                                            <span class="font-bold text-emerald-500" x-text="st.gameplay_data && st.gameplay_data.workspace ? st.gameplay_data.workspace.length + ' langkah' : '0'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js All Groups Replay Engine Script -->
    <script>
        function allGroupsReplayEngine() {
            return {
                groups: [],
                isPlaying: false,

                init() {
                    this.$watch('$wire.showAllGroupsReplayModal', (show) => {
                        if (show) {
                            setTimeout(() => {
                                if (this.$wire.allGroupsReplayData) {
                                    this.loadData(this.$wire.allGroupsReplayData);
                                }
                            }, 150);
                        }
                    });

                    Livewire.on('init-all-groups-canvas', (event) => {
                        let data = event.data || event[0]?.data;
                        setTimeout(() => {
                            if (data) {
                                this.loadData(data);
                            } else if (this.$wire.allGroupsReplayData) {
                                this.loadData(this.$wire.allGroupsReplayData);
                            }
                        }, 150);
                    });
                },

                loadData(data) {
                    if (!data) return;
                    this.groups = JSON.parse(JSON.stringify(data));
                    this.resetAllReplays();
                },

                resetAllReplays() {
                    this.isPlaying = false;
                    this.groups.forEach((g, gIdx) => {
                        g.students.forEach((st, sIdx) => {
                            let gData = st.gameplay_data || {};
                            st.gridSize = gData.gridSize || 5;
                            st.startTile = gData.startTile || { x: 0, y: 4 };
                            st.wuduTile = gData.wuduTile || { x: 2, y: 3 };
                            st.kitabTile = gData.kitabTile || { x: 3, y: 1 };
                            st.masjidTile = gData.masjidTile || { x: 4, y: 0 };
                            st.obstacles = gData.obstacles || [];
                            st.workspace = gData.workspace || [];
                            st.botX = st.startTile.x;
                            st.botY = st.startTile.y;
                            st.botDir = 3;
                            st.hasWudu = false;
                            st.hasKitab = false;

                            this.drawSingleCanvas(gIdx, sIdx, st);
                        });
                    });
                },

                async startAllReplays() {
                    this.resetAllReplays();
                    this.isPlaying = true;

                    let maxSteps = 0;
                    this.groups.forEach(g => {
                        g.students.forEach(st => {
                            let expanded = this.expandWorkspace(st.workspace);
                            st.expandedSteps = expanded.steps;
                            if (expanded.steps.length > maxSteps) {
                                maxSteps = expanded.steps.length;
                            }
                        });
                    });

                    for (let step = 0; step < maxSteps; step++) {
                        if (!this.isPlaying) break;

                        this.groups.forEach((g, gIdx) => {
                            g.students.forEach((st, sIdx) => {
                                if (step < st.expandedSteps.length) {
                                    let cmd = st.expandedSteps[step];
                                    if (cmd === 'FORWARD') {
                                        let dx = [1, 0, -1, 0][st.botDir];
                                        let dy = [0, 1, 0, -1][st.botDir];
                                        let nextX = st.botX + dx;
                                        let nextY = st.botY + dy;
                                        if (nextX >= 0 && nextX < st.gridSize && nextY >= 0 && nextY < st.gridSize) {
                                            if (!st.obstacles.some(o => o.x === nextX && o.y === nextY)) {
                                                st.botX = nextX;
                                                st.botY = nextY;
                                            }
                                        }
                                        if (st.botX === st.wuduTile.x && st.botY === st.wuduTile.y) st.hasWudu = true;
                                        if (st.botX === st.kitabTile.x && st.botY === st.kitabTile.y) st.hasKitab = true;
                                    } else if (cmd === 'TURN_LEFT') {
                                        st.botDir = (st.botDir + 3) % 4;
                                    } else if (cmd === 'TURN_RIGHT') {
                                        st.botDir = (st.botDir + 1) % 4;
                                    }
                                    this.drawSingleCanvas(gIdx, sIdx, st);
                                }
                            });
                        });

                        await new Promise(r => setTimeout(r, 450));
                    }

                    this.isPlaying = false;
                },

                expandWorkspace(workspace) {
                    let expandedSteps = [];
                    for (let i = 0; i < workspace.length; i++) {
                        let cmd = workspace[i];
                        if (cmd === 'LOOP_2') {
                            let prevAction = null;
                            for (let p = i - 1; p >= 0; p--) {
                                if (workspace[p] !== 'LOOP_2') {
                                    prevAction = workspace[p];
                                    break;
                                }
                            }
                            let nextAction = null;
                            if (!prevAction) {
                                for (let n = i + 1; n < workspace.length; n++) {
                                    if (workspace[n] !== 'LOOP_2') {
                                        nextAction = workspace[n];
                                        break;
                                    }
                                }
                            }
                            let targetCmd = prevAction || nextAction;
                            if (targetCmd) expandedSteps.push(targetCmd);
                        } else {
                            expandedSteps.push(cmd);
                        }
                    }
                    return { steps: expandedSteps };
                },

                drawSingleCanvas(gIdx, sIdx, st) {
                    const canvas = document.getElementById('allGroupCanvas_' + gIdx + '_' + sIdx);
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    const size = canvas.width;
                    const cellSize = size / st.gridSize;

                    ctx.fillStyle = '#09090b';
                    ctx.fillRect(0, 0, size, size);

                    for (let r = 0; r < st.gridSize; r++) {
                        for (let c = 0; c < st.gridSize; c++) {
                            ctx.strokeStyle = '#10b981';
                            ctx.lineWidth = 1.5;
                            ctx.strokeRect(c * cellSize, r * cellSize, cellSize, cellSize);
                            ctx.fillStyle = '#18181b';
                            ctx.fillRect(c * cellSize + 1.5, r * cellSize + 1.5, cellSize - 3, cellSize - 3);
                        }
                    }

                    this.drawTile(ctx, st.startTile.x, st.startTile.y, cellSize, '#064e3b', '🏠', 'Asrama');
                    this.drawTile(ctx, st.wuduTile.x, st.wuduTile.y, cellSize, st.hasWudu ? '#0284c7' : '#0369a1', '💧', 'Wudu');
                    this.drawTile(ctx, st.kitabTile.x, st.kitabTile.y, cellSize, st.hasKitab ? '#d97706' : '#b45309', '📖', 'Kitab');
                    st.obstacles.forEach(o => this.drawTile(ctx, o.x, o.y, cellSize, '#451a03', '🌧️', 'Becek'));
                    this.drawTile(ctx, st.masjidTile.x, st.masjidTile.y, cellSize, '#047857', '🕌', 'Masjid');

                    const bx = st.botX * cellSize + cellSize / 2;
                    const by = st.botY * cellSize + cellSize / 2;

                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(bx, by, cellSize / 2.8, 0, Math.PI * 2);
                    ctx.fillStyle = '#10b981';
                    ctx.fill();
                    ctx.lineWidth = 2;
                    ctx.strokeStyle = '#ffffff';
                    ctx.stroke();

                    ctx.font = `${cellSize * 0.4}px sans-serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('👳', bx, by);

                    const angles = [0, Math.PI / 2, Math.PI, -Math.PI / 2];
                    const arrowX = bx + Math.cos(angles[st.botDir]) * (cellSize / 2.4);
                    const arrowY = by + Math.sin(angles[st.botDir]) * (cellSize / 2.4);
                    ctx.beginPath();
                    ctx.arc(arrowX, arrowY, 4, 0, Math.PI * 2);
                    ctx.fillStyle = '#f59e0b';
                    ctx.fill();
                    ctx.restore();
                },

                drawTile(ctx, x, y, cellSize, color, emoji, label) {
                    const px = x * cellSize;
                    const py = y * cellSize;
                    ctx.fillStyle = color;
                    ctx.fillRect(px + 2, py + 2, cellSize - 4, cellSize - 4);
                    ctx.font = `${cellSize * 0.35}px sans-serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(emoji, px + cellSize / 2, py + cellSize / 2.4);
                }
            };
        }
    </script>

    <!-- ========================================================= -->
    <!-- CETAK PDF JURNAL REFLEKSI (Hanya terlihat saat Print)     -->
    <!-- ========================================================= -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #print-section, #print-section * {
                visibility: visible;
            }
            #print-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 10px;
            }
            .print-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            .print-card {
                border: 2px solid #e4e4e7;
                border-radius: 12px;
                padding: 16px;
                page-break-inside: avoid;
                background-color: #fafafa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    <div id="print-section" class="hidden print:block w-full bg-white">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black text-emerald-800">Jurnal Refleksi Siswa (PSE & CT)</h1>
            <p class="text-lg text-zinc-600 font-bold mt-1">
                Sesi: {{ $session->name }} | Kelas: {{ $session->classroom->name }}
            </p>
        </div>

        <div class="print-grid">
            @foreach($students->sortBy('name') as $student)
                @php
                    $lastRes = $student->results->last();
                    $reflections = $lastRes?->reflections ?? [];
                    $emotion = $reflections['emotion'] ?? '-';
                    $pillar = $reflections['pillar'] ?? '-';
                    $duration = $reflections['duration_seconds'] ?? '-';
                    $points = $reflections['total_points'] ?? ($lastRes ? $lastRes->total_stars * 100 : 0);
                    $groupName = $groups->first(fn($g) => $g->students->contains('id', $student->id))?->name ?? '-';
                @endphp
                <div class="print-card">
                    <!-- Card Header -->
                    <div class="flex items-start justify-between border-b border-zinc-200 pb-3 mb-4">
                        <div>
                            <h3 class="font-black text-xl text-zinc-800">{{ $student->name }}</h3>
                            <div class="text-sm font-bold text-zinc-500 mt-0.5">Kelompok: {{ $groupName }}</div>
                        </div>
                        <span class="text-[11px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg border border-emerald-200">
                            {{ $student->diagnosticResults->first()?->level ?? 'Basic' }}
                        </span>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-white p-2.5 rounded-lg border border-zinc-200">
                            <span class="block text-[10px] text-zinc-400 uppercase font-black tracking-wider mb-1">Poin Akhir</span>
                            <strong class="text-base text-emerald-600">{{ number_format($points) }} PTS ⭐</strong>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-zinc-200">
                            <span class="block text-[10px] text-zinc-400 uppercase font-black tracking-wider mb-1">Durasi</span>
                            <strong class="text-base text-blue-600">{{ $duration }} Detik</strong>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-zinc-200">
                            <span class="block text-[10px] text-zinc-400 uppercase font-black tracking-wider mb-1">Emosi (PSE)</span>
                            <strong class="text-base text-zinc-800">{{ $emotion }}</strong>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-zinc-200">
                            <span class="block text-[10px] text-zinc-400 uppercase font-black tracking-wider mb-1">Pilar CT</span>
                            <strong class="text-base text-amber-600">{{ $pillar }}</strong>
                        </div>
                    </div>
                    
                    <!-- Card Footer -->
                    <div class="mt-4 pt-3 border-t border-zinc-200 text-xs font-bold text-zinc-500 flex justify-between items-center">
                        <span>Percobaan: {{ $lastRes?->total_attempts ?? 0 }}x main</span>
                        <span class="text-zinc-400">{{ $lastRes && $lastRes->created_at ? $lastRes->created_at->format('d/m/Y H:i') : 'Belum bermain' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
