<div class="min-h-[calc(100vh-4rem)] bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800 p-3 sm:p-6" wire:poll.2s>
    <x-slot:title>Permainan CT Santri - {{ $group->name ?? 'Kelompok' }}</x-slot:title>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="max-w-7xl mx-auto space-y-6">
        @if(!$group)
            <!-- Belum Memiliki Kelompok -->
            <div class="bg-white dark:bg-zinc-800 rounded-3xl p-10 shadow-xl border border-emerald-100 dark:border-zinc-700 text-center space-y-6">
                <div class="size-20 mx-auto bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center">
                    <flux:icon.user-group class="size-10" />
                </div>
                <div class="space-y-2">
                    <h2 class="text-2xl font-black text-zinc-800 dark:text-white">Menunggu Pembagian Kelompok</h2>
                    <p class="text-zinc-500 text-sm max-w-md mx-auto">Gurumu belum membagikan kelompok untuk sesi ini. Halaman ini akan otomatis diperbarui begitu kelompok dibuat!</p>
                </div>
            </div>

        @elseif($activeSession->status === 'finished')
            <!-- Selesai / Podium Juara -->
            <div class="bg-white dark:bg-zinc-800 rounded-3xl p-8 sm:p-10 shadow-xl border border-emerald-100 dark:border-zinc-700 text-center space-y-8">
                <div class="size-24 mx-auto bg-gradient-to-br from-amber-400 to-yellow-500 text-white rounded-full flex items-center justify-center shadow-lg animate-bounce">
                    <flux:icon.trophy class="size-14" />
                </div>
                <div>
                    <h2 class="text-3xl font-black text-zinc-800 dark:text-white">Sesi Permainan Selesai! 🎉</h2>
                    <p class="text-zinc-500 text-base max-w-md mx-auto mt-1">Selamat untuk semua kelompok yang telah menyelesaikan tantangan Computational Thinking!</p>
                </div>

                <div class="max-w-xl mx-auto space-y-3 text-left">
                    <h3 class="text-sm font-bold text-zinc-400 uppercase tracking-wider text-center mb-4">🏆 Papan Peringkat Akhir Kelas</h3>
                    @foreach($leaderboard as $rank => $g)
                        <div class="p-4 rounded-2xl border flex items-center justify-between {{ $g->id === $group->id ? 'bg-emerald-50 dark:bg-emerald-950/50 border-emerald-500 shadow-md ring-2 ring-emerald-500/20' : 'bg-zinc-50 dark:bg-zinc-800/60 border-zinc-200 dark:border-zinc-700' }}">
                            <div class="flex items-center gap-4">
                                <span class="size-9 rounded-xl flex items-center justify-center font-black text-lg {{ match($rank) {
                                    0 => 'bg-amber-400 text-white shadow-md',
                                    1 => 'bg-slate-300 text-slate-800',
                                    2 => 'bg-amber-700 text-white',
                                    default => 'bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300',
                                } }}">
                                    {{ $rank + 1 }}
                                </span>
                                <div>
                                    <div class="font-black text-base text-zinc-800 dark:text-white flex items-center gap-2">
                                        <span>{{ $g->name }}</span>
                                        @if($g->id === $group->id)
                                            <span class="bg-emerald-600 text-white text-[10px] px-2 py-0.5 rounded-full font-black">Kelompokmu</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-zinc-400">{{ $g->students->count() }} Anggota</div>
                                </div>
                            </div>
                            <div class="font-black text-lg text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <span>{{ number_format($g->score) }}</span>
                                <span class="text-xs">PTS ⭐</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        @elseif($activeSession->status === 'grouping')
            <!-- Ruang Tunggu (Grouping Phase) -->
            <div class="bg-white dark:bg-zinc-800 rounded-3xl p-8 shadow-xl border border-emerald-100 dark:border-zinc-700 space-y-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-zinc-100 dark:border-zinc-700 pb-6">
                    <div class="flex items-center gap-4 text-center sm:text-left">
                        <div class="size-16 bg-gradient-to-br from-emerald-400 to-teal-600 text-white rounded-2xl flex items-center justify-center shadow-lg shrink-0">
                            <flux:icon.user-group class="size-9" />
                        </div>
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-xs font-black text-emerald-600 bg-emerald-100 dark:bg-emerald-950/60 px-3 py-1 rounded-full uppercase tracking-wider mb-1">
                                🎮 Kelompok Belajar Kamu
                            </div>
                            <h2 class="text-3xl font-black text-zinc-800 dark:text-white">{{ $group->name }}</h2>
                        </div>
                    </div>

                    <div class="bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 px-4 py-2 rounded-2xl text-xs font-bold flex items-center gap-2">
                        <span class="size-2 rounded-full bg-amber-500 animate-ping"></span>
                        Menunggu Guru Memulai Game...
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-zinc-500 uppercase tracking-wider text-left">Teman Sekelompok ({{ $group->students->count() }} Siswa)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($group->students as $st)
                            @php
                                $diagResult = $st->diagnosticResults->first();
                                $level = $diagResult->level ?? 'Basic';
                                $isCurrentUser = $st->id === auth()->id();
                            @endphp
                            <div class="p-4 rounded-2xl border transition-all flex items-center justify-between {{ $isCurrentUser ? 'bg-emerald-50/80 dark:bg-emerald-950/50 border-emerald-500 shadow-md ring-2 ring-emerald-500/20' : 'bg-zinc-50 dark:bg-zinc-800/60 border-zinc-200 dark:border-zinc-700' }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <flux:avatar :name="$st->name" size="md" class="{{ $isCurrentUser ? 'ring-2 ring-emerald-500' : '' }}" />
                                    <div class="min-w-0 text-left">
                                        <div class="font-bold text-sm text-zinc-800 dark:text-white truncate flex items-center gap-1.5">
                                            <span>{{ $st->name }}</span>
                                            @if($isCurrentUser)
                                                <span class="bg-emerald-600 text-white text-[10px] px-2 py-0.5 rounded-full font-black">Kamu</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-zinc-400 font-mono">NISN: {{ $st->nisn }}</div>
                                    </div>
                                </div>

                                <flux:badge size="sm" color="{{ match($level) {
                                    'Basic' => 'blue',
                                    'Intermediate' => 'orange',
                                    'Advanced' => 'emerald',
                                    default => 'zinc',
                                } }}">
                                    {{ $level }}
                                </flux:badge>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-700 text-center">
                    <div class="inline-flex items-center gap-2 text-xs text-zinc-500 font-medium">
                        <flux:icon.rocket-launch class="size-4 text-emerald-500 animate-bounce" />
                        <span>Bersiaplah! Bekerja samalah dengan teman kelompokmu begitu game dimulai!</span>
                    </div>
                </div>
            </div>

        @else
            <div wire:ignore.self x-data="santriBotEngine('{{ $studentLevel }}', {{ $initialElapsed }})" x-init="initEngine()" x-on:session-timeout.window="handleTimeout()" class="space-y-6">

                <!-- Header Status & Top Score -->
                <div class="bg-white dark:bg-zinc-800 rounded-3xl p-5 shadow-xl border border-emerald-100 dark:border-zinc-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4 text-center sm:text-left">
                        <div class="size-14 bg-gradient-to-br from-emerald-500 to-teal-600 text-white rounded-2xl flex items-center justify-center shadow-md shrink-0">
                            <flux:icon.user-group class="size-8" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2 justify-center sm:justify-start">
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">{{ $group->name }}</span>
                                <span class="bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase">Level TaRL: {{ $studentLevel }}</span>
                            </div>
                            <h2 class="text-2xl font-black text-zinc-800 dark:text-white">Misi Bot Santri ke Masjid 🕌</h2>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="bg-blue-600 text-white px-4 py-2 rounded-2xl shadow-lg flex items-center gap-2"
                             wire:ignore
                             x-data="{ 
                                 remaining: {{ $this->remainingTime }},
                                 init() {
                                     // Prevent immediate timeout on page load to debug timezone issues
                                     // if (this.remaining === 0) {
                                     //     this.$dispatch('session-timeout');
                                     // }
                                     this.timer = setInterval(() => {
                                         if(this.remaining > 0) {
                                             this.remaining--;
                                             if (this.remaining === 0) {
                                                 this.$dispatch('session-timeout');
                                             }
                                         }
                                     }, 1000);
                                 },
                                 formatTime() {
                                     let m = Math.floor(this.remaining / 60);
                                     let s = this.remaining % 60;
                                     return (m < 10 ? '0'+m : m) + ':' + (s < 10 ? '0'+s : s);
                                 }
                             }">
                            <flux:icon.clock class="size-5 animate-pulse" />
                            <div>
                                <div class="text-[9px] uppercase font-bold text-blue-200">Sisa Waktu</div>
                                <div class="text-lg font-black leading-none" x-text="formatTime()"></div>
                            </div>
                        </div>

                        <div class="bg-emerald-500 text-white px-5 py-2 rounded-2xl shadow-lg flex items-center gap-3">
                            <flux:icon.sparkles class="size-6 text-yellow-300 animate-spin" />
                            <div>
                                <div class="text-[10px] uppercase font-bold text-emerald-100">Poin Kelompok</div>
                                <div class="text-xl font-black leading-tight">{{ number_format($group->score ?? 0) }} <span class="text-xs font-normal">PTS</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flash Notification Messages -->
                @if(session('success_message'))
                    <div class="bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 p-4 rounded-2xl text-sm font-bold flex items-center gap-3 animate-pulse">
                        <flux:icon.check-circle class="size-6 shrink-0" />
                        <span>{{ session('success_message') }}</span>
                    </div>
                @endif

                <!-- Main Game Layout: 3 Columns Side-by-Side in 1 Row -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                    
                    <!-- Column 1: HTML5 Canvas Screen (4 Cols) -->
                    <div class="lg:col-span-4 space-y-4">
                        <div class="bg-white dark:bg-zinc-800 rounded-3xl p-5 shadow-xl border border-emerald-100 dark:border-zinc-700 space-y-4 text-center">
                            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-700 pb-3">
                                <span class="text-xs font-black text-zinc-500 uppercase tracking-wider flex items-center gap-1.5">
                                    <flux:icon.map class="size-4 text-emerald-500" />
                                    Peta Grid 2D (<span x-text="gridSize + 'x' + gridSize"></span>)
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <span x-show="hasWudu" class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">💧 Wudu ✅</span>
                                    <span x-show="hasKitab" class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full">📖 Kitab ✅</span>
                                </div>
                            </div>

                            <!-- Canvas Container -->
                            <div class="relative mx-auto flex justify-center items-center bg-zinc-950 rounded-2xl p-2 shadow-inner overflow-hidden border-2 border-emerald-500/30">
                                <canvas id="gameCanvas" width="360" height="360" class="rounded-xl shadow-md max-w-full h-auto"></canvas>
                            </div>

                            <!-- Status & Mission Info Badges -->
                            <div class="grid grid-cols-3 gap-2 text-xs font-semibold">
                                <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 p-2 rounded-xl text-emerald-700 dark:text-emerald-300 flex items-center justify-center gap-1">
                                    <span>🏠 &rarr; 🕌</span>
                                </div>
                                <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 p-2 rounded-xl text-amber-700 dark:text-amber-300 flex items-center justify-center gap-1">
                                    <span>Target &le;<span x-text="targetSteps"></span></span>
                                </div>
                                <div class="bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 p-2 rounded-xl text-blue-700 dark:text-blue-300 font-mono font-bold flex items-center justify-center gap-1">
                                    <flux:icon.clock class="size-3.5 text-blue-500 animate-pulse" />
                                    <span x-text="formatTime(elapsedSeconds)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- System Debugging Toast Notification -->
                        <div x-show="debugMessage" x-transition class="bg-red-50 dark:bg-red-950/80 border-2 border-red-400 text-red-700 dark:text-red-200 p-4 rounded-2xl text-xs font-bold space-y-1 shadow-lg text-left">
                            <div class="flex items-center gap-2 text-red-600 dark:text-red-300 font-black">
                                <flux:icon.exclamation-triangle class="size-5 shrink-0" />
                                <span>PETUNJUK DEBUGGING:</span>
                            </div>
                            <p class="leading-relaxed pl-7" x-text="debugMessage"></p>
                        </div>
                    </div>

                    <!-- Column 2: Workspace Algoritma (Middle - 5 Cols) -->
                    <div class="lg:col-span-5 bg-white dark:bg-zinc-800 rounded-3xl p-5 shadow-xl border border-emerald-100 dark:border-zinc-700 space-y-4 flex flex-col justify-between text-left">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-700 pb-2">
                                <div class="flex items-center gap-1.5">
                                    <flux:icon.code-bracket class="size-4 text-emerald-500" />
                                    <h3 class="font-black text-xs text-zinc-800 dark:text-white">Workspace Algoritma (<span x-text="workspace.length"></span>)</h3>
                                </div>

                                <button @click="clearWorkspace()" :disabled="isExecuting || workspace.length === 0 || hasFinished" class="text-[11px] font-bold text-red-500 hover:text-red-700 flex items-center gap-1 disabled:opacity-40">
                                    <flux:icon.trash class="size-3" />
                                    <span>Kosongkan</span>
                                </button>
                            </div>

                            <!-- Workspace Sequence Chips -->
                            <div wire:ignore class="min-h-[170px] max-h-[220px] overflow-y-auto p-3 bg-zinc-50 dark:bg-zinc-900/60 rounded-2xl border-2 border-dashed border-zinc-200 dark:border-zinc-700 flex flex-wrap gap-1.5 items-start">
                                <template x-if="workspace.length === 0">
                                    <div class="w-full text-center py-12 text-zinc-400 text-[11px] italic">
                                        Klik icon perintah di Palette Perintah sebelah kanan untuk menyusun urutan rute santri bot.
                                    </div>
                                </template>

                                <template x-for="(step, idx) in workspace" :key="idx">
                                    <div class="relative size-14 aspect-square rounded-2xl font-black text-xs flex flex-col items-center justify-center p-1 shadow-md animate-fadeIn transition-all hover:scale-105 border shrink-0"
                                         :class="{
                                            'bg-emerald-500 text-white border-emerald-400': step === 'FORWARD',
                                            'bg-blue-500 text-white border-blue-400': step === 'TURN_LEFT',
                                            'bg-purple-500 text-white border-purple-400': step === 'TURN_RIGHT',
                                            'bg-orange-500 text-white border-orange-400': step.startsWith('LOOP_')
                                         }">
                                        <!-- Step Number Badge -->
                                        <span class="absolute top-1 left-1.5 text-[9px] font-black opacity-80" x-text="(idx + 1) + '.'"></span>
                                        
                                        <!-- Delete Step Button (Hidden when finished) -->
                                        <button type="button" x-show="!hasFinished" @click="removeStep(idx)" :disabled="isExecuting" class="absolute top-0.5 right-1 size-4 rounded-full bg-black/25 hover:bg-black/50 text-white flex items-center justify-center text-[10px] leading-none transition-all cursor-pointer">
                                            &times;
                                        </button>

                                        <!-- Emoji Icon -->
                                        <span class="text-xl mt-1.5" x-text="{
                                            'FORWARD': '⬆️',
                                            'TURN_LEFT': '↩️',
                                            'TURN_RIGHT': '↪️',
                                            'LOOP_2': '🔁'
                                        }[step] || '❓'"></span>

                                        <!-- Short Label -->
                                        <span class="text-[9px] font-extrabold truncate w-full text-center tracking-tighter" x-text="{
                                            'FORWARD': 'Maju',
                                            'TURN_LEFT': 'Kiri',
                                            'TURN_RIGHT': 'Kanan',
                                            'LOOP_2': 'Loop 2x'
                                        }[step] || step"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                    <!-- Banner Jika Sudah Finished -->
                    <template x-if="hasFinished">
                        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white rounded-3xl p-6 shadow-xl border border-white/20 mb-6 space-y-4 text-left">
                            <div class="flex items-center justify-between gap-4 border-b border-white/20 pb-3">
                                <div class="flex items-center gap-3">
                                    <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-md text-3xl">🏆</div>
                                    <div>
                                        <h3 class="text-lg font-black flex items-center gap-2">
                                            <span>Misi Telah Diselesaikan!</span>
                                            <span class="text-amber-300 text-base" x-text="'⭐'.repeat(earnedStars)"></span>
                                        </h3>
                                        <p class="text-xs text-emerald-100 font-medium">Algoritma solusi terakhirmu telah tersimpan dan poin kelompok berhasil diklaim.</p>
                                    </div>
                                </div>
                                <div class="bg-white/20 backdrop-blur-md px-3.5 py-1.5 rounded-full font-black text-xs border border-white/30 shrink-0">
                                    ✅ SELESAI
                                </div>
                            </div>

                            <!-- Summary Grid -->
                            <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-2xl border border-white/20 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs font-semibold">
                                <div>
                                    <span class="text-emerald-200 block text-[10px] uppercase font-bold">⭐ Total Skor Klaim</span>
                                    <span class="font-black text-yellow-300 text-sm" x-text="'+' + totalPoints + ' PTS'"></span>
                                </div>
                                <div>
                                    <span class="text-emerald-200 block text-[10px] uppercase font-bold">⏱️ Durasi Waktu</span>
                                    <span class="font-bold text-white text-sm" x-text="elapsedSeconds + 's'"></span>
                                </div>
                                <div>
                                    <span class="text-emerald-200 block text-[10px] uppercase font-bold">😊 Refleksi Emosi</span>
                                    <span class="font-bold text-white text-xs truncate block" x-text="selectedEmotion"></span>
                                </div>
                                <div>
                                    <span class="text-emerald-200 block text-[10px] uppercase font-bold">🧩 Pilar CT</span>
                                    <span class="font-bold text-yellow-300 text-xs truncate block" x-text="selectedPillar"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Action Buttons: Run & Reset -->
                    <div class="flex items-center gap-2 pt-2">
                        <button @click="runAlgorithm()" :disabled="isExecuting || workspace.length === 0 || hasFinished" class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold py-3 rounded-2xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 text-xs disabled:opacity-50 cursor-pointer">
                            <flux:icon.play class="size-4" />
                            <span x-text="hasFinished ? '✅ PERMAINAN SUDAH SELESAI' : (isExecuting ? 'MENJALANKAN BOT...' : '▶️ JALANKAN BOT SANTRI')"></span>
                        </button>

                        <button @click="resetBotPosition()" :disabled="isExecuting || hasFinished" class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-800 dark:text-white font-extrabold px-4 py-3 rounded-2xl transition-all flex items-center justify-center gap-1.5 text-xs disabled:opacity-50 cursor-pointer">
                            <flux:icon.arrow-path class="size-4" />
                            <span>Reset</span>
                        </button>
                    </div>
                </div>

                <!-- Column 3: Palette Perintah (Right - 3 Cols) -->
                <div class="lg:col-span-3 bg-white dark:bg-zinc-800 rounded-3xl p-5 shadow-xl border border-emerald-100 dark:border-zinc-700 space-y-4 text-left">
                    <div class="border-b border-zinc-100 dark:border-zinc-700 pb-2">
                        <h3 class="text-xs font-black text-zinc-500 uppercase tracking-wider flex items-center gap-1.5">
                            <flux:icon.command-line class="size-4 text-emerald-500" />
                            Palette Perintah
                        </h3>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <!-- Maju -->
                        <button @click="addInstruction('FORWARD')" :disabled="isExecuting || hasFinished" class="aspect-square p-2 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border-2 border-emerald-300 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-2xl flex flex-col items-center justify-center gap-1 transition-all hover:scale-105 shadow-sm disabled:opacity-50 active:scale-95 cursor-pointer">
                            <span class="text-3xl">⬆️</span>
                            <span class="text-[11px] font-black">Maju</span>
                        </button>

                        <!-- Belok Kiri -->
                        <button @click="addInstruction('TURN_LEFT')" :disabled="isExecuting || hasFinished" class="aspect-square p-2 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/60 border-2 border-blue-300 dark:border-blue-800 text-blue-700 dark:text-blue-300 rounded-2xl flex flex-col items-center justify-center gap-1 transition-all hover:scale-105 shadow-sm disabled:opacity-50 active:scale-95 cursor-pointer">
                            <span class="text-3xl">↩️</span>
                            <span class="text-[11px] font-black">Belok Kiri</span>
                        </button>

                        <!-- Belok Kanan -->
                        <button @click="addInstruction('TURN_RIGHT')" :disabled="isExecuting || hasFinished" class="aspect-square p-2 bg-purple-50 dark:bg-purple-950/40 hover:bg-purple-100 dark:hover:bg-purple-900/60 border-2 border-purple-300 dark:border-purple-800 text-purple-700 dark:text-purple-300 rounded-2xl flex flex-col items-center justify-center gap-1 transition-all hover:scale-105 shadow-sm disabled:opacity-50 active:scale-95 cursor-pointer">
                            <span class="text-3xl">↪️</span>
                            <span class="text-[11px] font-black">Belok Kanan</span>
                        </button>

                        <!-- Loop 2x -->
                        <button @click="addInstruction('LOOP_2')" :disabled="isExecuting || hasFinished" class="aspect-square p-2 bg-orange-50 dark:bg-orange-950/40 hover:bg-orange-100 dark:hover:bg-orange-900/60 border-2 border-orange-300 dark:border-orange-800 text-orange-700 dark:text-orange-300 rounded-2xl flex flex-col items-center justify-center gap-1 transition-all hover:scale-105 shadow-sm disabled:opacity-50 active:scale-95 cursor-pointer">
                            <span class="text-3xl">🔁</span>
                            <span class="text-[11px] font-black">Loop 2x</span>
                        </button>
                    </div>

                        <div class="text-[10px] text-zinc-400 italic text-center pt-2">
                            💡 Klik icon di atas untuk menambahkan instruksi ke workspace.
                        </div>
                    </div>

                </div>

                <!-- Misi Selesai Banner (Muncul setelah selesai) -->
                <template x-if="hasFinished">
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/40 dark:to-teal-950/40 border-2 border-emerald-500 rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col items-center justify-center text-center animate-fadeIn space-y-3">
                        <div class="size-16 bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-lg">
                            <flux:icon.check-badge class="size-10" />
                        </div>
                        <div>
                            <h3 class="font-black text-emerald-800 dark:text-emerald-400 text-2xl">Misi Telah Selesai! 🎉</h3>
                            <p class="text-sm text-emerald-700 dark:text-emerald-500 mt-1 max-w-lg mx-auto font-medium">Hebat! Kamu sudah mengirimkan hasil refleksi misi. Sambil menunggu sesi berakhir, kamu bisa memantau Papan Peringkat secara langsung di bawah ini.</p>
                        </div>
                    </div>
                </template>

                <!-- Live Leaderboard Section (Full Width Below 3 Columns Layout) -->
                <div class="bg-white dark:bg-zinc-800 rounded-3xl p-5 sm:p-6 shadow-xl border border-emerald-100 dark:border-zinc-700 space-y-4 text-left">
                    <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-700 pb-3">
                        <div class="flex items-center gap-2">
                            <flux:icon.trophy class="size-5 text-amber-500" />
                            <h3 class="font-black text-sm text-zinc-800 dark:text-white">Papan Peringkat Live Sesi Ini</h3>
                        </div>
                        <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 px-2.5 py-0.5 rounded-full uppercase">Realtime</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach($leaderboard as $rank => $g)
                            @php $isMyGroup = $g->id === $group->id; @endphp
                            <div class="p-3 rounded-2xl border flex items-center justify-between {{ $isMyGroup ? 'bg-emerald-50 dark:bg-emerald-950/50 border-emerald-500 ring-1 ring-emerald-500/20 shadow-md' : 'bg-zinc-50 dark:bg-zinc-800/60 border-zinc-200 dark:border-zinc-700' }}">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="size-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0 {{ match($rank) {
                                        0 => 'bg-amber-400 text-white shadow-md',
                                        1 => 'bg-slate-300 text-slate-800 shadow-sm',
                                        2 => 'bg-amber-700 text-white shadow-sm',
                                        default => 'bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300',
                                    } }}">
                                        #{{ $rank + 1 }}
                                    </span>
                                    <div class="min-w-0">
                                        <div class="font-bold text-xs text-zinc-800 dark:text-white truncate flex items-center gap-1">
                                            <span>{{ $g->name }}</span>
                                            @if($isMyGroup)
                                                <span class="bg-emerald-600 text-white text-[8px] px-1 py-0.2 rounded font-black">Kamu</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="font-black text-xs text-emerald-600 dark:text-emerald-400 shrink-0">
                                    {{ number_format($g->score) }} PTS ⭐
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- ========================================================= -->
                <!-- MODAL REFLEKSI METAKOGNITIF & PSE (POST-GAME)             -->
                <!-- ========================================================= -->
                <div x-show="showReflectionModal" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                    <div class="bg-white dark:bg-zinc-800 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-emerald-100 dark:border-zinc-700 space-y-6 text-center animate-scaleUp">
                        
                        <!-- Header Selebrasi Bintang -->
                        <div class="space-y-3">
                            <div class="text-4xl animate-bounce">🏆 🎉</div>
                            <h2 class="text-2xl font-black text-zinc-800 dark:text-white">Misi Berhasil Diselesaikan!</h2>
                            <div class="flex items-center justify-center gap-1.5 text-2xl py-1">
                                <template x-for="i in 3" :key="i">
                                    <span x-text="i <= earnedStars ? '⭐' : '⚪'"></span>
                                </template>
                            </div>

                            <!-- Point & Time Breakdown Card -->
                            <div class="bg-zinc-50 dark:bg-zinc-900/80 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-700 space-y-2 text-xs font-medium">
                                <div class="flex items-center justify-between">
                                    <span class="text-zinc-500">⭐ Efisiensi Blok Workspace (<span x-text="workspace.length"></span> / target <span x-text="targetSteps"></span>):</span>
                                    <span class="font-bold text-zinc-800 dark:text-white">+<span x-text="basePoints"></span> Poin</span>
                                </div>
                                <template x-if="loopCount > 0">
                                    <div class="flex items-center justify-between text-purple-600 dark:text-purple-400 font-bold">
                                        <span class="flex items-center gap-1">
                                            <span>🔁 Bonus Efisiensi Loop (<span x-text="loopCount"></span>x):</span>
                                            <span class="text-[9px] bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 px-1.5 py-0.5 rounded-full font-black">NILAI PLUS ➕</span>
                                        </span>
                                        <span>+<span x-text="loopBonus"></span> Poin 🔁</span>
                                    </div>
                                </template>
                                <div class="flex items-center justify-between">
                                    <span class="text-zinc-500">⏱️ Waktu Pengerjaan (<span x-text="elapsedSeconds"></span>s):</span>
                                    <span class="font-bold text-amber-500">+<span x-text="speedBonus"></span> Bonus Kecepatan ⚡</span>
                                </div>
                                <div class="pt-2 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-between text-sm font-black">
                                    <span class="text-emerald-600 dark:text-emerald-400">Total Skor Klaim:</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 text-lg">+<span x-text="totalPoints"></span> PTS</span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Refleksi Metakognitif & PSE -->
                        <div class="space-y-4 text-left">
                            <!-- Emotional Check-in -->
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-zinc-700 dark:text-zinc-300">1. Perasaanmu saat menyelesaikan permainan ini? (Emotional Check-in)</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" @click="selectedEmotion = 'Senang'" :class="selectedEmotion === 'Senang' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/60 ring-2 ring-emerald-500/30' : 'border-zinc-200 dark:border-zinc-700'" class="p-3 border rounded-2xl text-center text-xs font-bold transition-all hover:border-emerald-400">
                                        <div class="text-2xl mb-1">😊</div>
                                        <div>Senang & Bangga</div>
                                    </button>

                                    <button type="button" @click="selectedEmotion = 'Penasaran'" :class="selectedEmotion === 'Penasaran' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/60 ring-2 ring-emerald-500/30' : 'border-zinc-200 dark:border-zinc-700'" class="p-3 border rounded-2xl text-center text-xs font-bold transition-all hover:border-emerald-400">
                                        <div class="text-2xl mb-1">🤔</div>
                                        <div>Penasaran</div>
                                    </button>

                                    <button type="button" @click="selectedEmotion = 'Sempat Kesal'" :class="selectedEmotion === 'Sempat Kesal' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/60 ring-2 ring-emerald-500/30' : 'border-zinc-200 dark:border-zinc-700'" class="p-3 border rounded-2xl text-center text-xs font-bold transition-all hover:border-emerald-400">
                                        <div class="text-2xl mb-1">😤</div>
                                        <div>Sempat Kesal (Debugging)</div>
                                    </button>
                                </div>
                            </div>

                            <!-- Pilar CT Terbantu -->
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-zinc-700 dark:text-zinc-300">2. Pilar Computational Thinking mana yang paling membantumu?</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" @click="selectedPillar = 'Dekomposisi'" :class="selectedPillar === 'Dekomposisi' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/60' : 'border-zinc-200 dark:border-zinc-700'" class="p-2.5 border rounded-xl text-xs font-bold transition-all text-left">
                                        🧩 Dekomposisi (Memecah Langkah)
                                    </button>

                                    <button type="button" @click="selectedPillar = 'Pengenalan Pola'" :class="selectedPillar === 'Pengenalan Pola' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/60' : 'border-zinc-200 dark:border-zinc-700'" class="p-2.5 border rounded-xl text-xs font-bold transition-all text-left">
                                        🔍 Pengenalan Pola (Looping)
                                    </button>

                                    <button type="button" @click="selectedPillar = 'Abstraksi'" :class="selectedPillar === 'Abstraksi' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/60' : 'border-zinc-200 dark:border-zinc-700'" class="p-2.5 border rounded-xl text-xs font-bold transition-all text-left">
                                        🎯 Abstraksi (Fokus Rute Utama)
                                    </button>

                                    <button type="button" @click="selectedPillar = 'Algoritma'" :class="selectedPillar === 'Algoritma' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/60' : 'border-zinc-200 dark:border-zinc-700'" class="p-2.5 border rounded-xl text-xs font-bold transition-all text-left">
                                        🤖 Algoritma (Urutan Langkah)
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button Submit -->
                        <div class="pt-4">
                            <button type="button" @click="submitReflection()" class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold py-3.5 rounded-2xl shadow-lg transition-all text-sm cursor-pointer">
                                🚀 KLAIM SKOR & KIRIM REFLEKSI
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Alpine.js Canvas Game Engine Script -->
            <div wire:ignore>
                <script>
                    function santriBotEngine(level, initialElapsed = 0) {
                        return {
                        level: level || 'Basic',
                        hasFinished: @json($hasFinished),
                        existingResult: @json($existingResult?->reflections),
                        savedResultInfo: @json($existingResult),
                        gridSize: 5,
                        targetSteps: 10,
                        workspace: [],
                        isExecuting: false,
                        debugMessage: '',
                        showReflectionModal: false,
                        earnedStars: 3,
                        executedSteps: 0,
                        attempts: 0, // NEW: Track number of attempts
                        selectedEmotion: 'Senang',
                        selectedPillar: 'Algoritma',

                        // Time & Score State
                        elapsedSeconds: initialElapsed,
                        basePoints: 300,
                        speedBonus: 0,
                        loopCount: 0,
                        loopBonus: 0,
                        totalPoints: 300,
                        timerInterval: null,

                        // State Bot
                        botX: 0,
                        botY: 0,
                        botDir: 0, // 0: East, 1: South, 2: West, 3: North
                        hasWudu: false,
                        hasKitab: false,

                        // Locations
                        startTile: { x: 0, y: 0 },
                        wuduTile: { x: 0, y: 4 },
                        kitabTile: { x: 4, y: 0 },
                        masjidTile: { x: 4, y: 4 },
                        obstacles: [],

                        initEngine() {
                            if (this.hasFinished && this.existingResult) {
                                let gData = this.existingResult.gameplay_data || {};
                                this.level = gData.level || this.level;
                                this.gridSize = gData.gridSize || 5;
                                this.targetSteps = gData.targetSteps || 10;
                                this.startTile = gData.startTile || { x: 0, y: 4 };
                                this.wuduTile = gData.wuduTile || { x: 2, y: 3 };
                                this.kitabTile = gData.kitabTile || { x: 3, y: 1 };
                                this.masjidTile = gData.masjidTile || { x: 4, y: 0 };
                                this.obstacles = gData.obstacles || [];
                                this.workspace = gData.workspace || [];
                                this.hasWudu = true;
                                this.hasKitab = true;
                                this.botX = this.masjidTile.x;
                                this.botY = this.masjidTile.y;
                                
                                if (this.savedResultInfo) {
                                    this.earnedStars = this.savedResultInfo.total_stars || 3;
                                    this.executedSteps = this.savedResultInfo.total_steps || this.workspace.length;
                                }
                                this.basePoints = this.existingResult.base_points || 300;
                                this.speedBonus = this.existingResult.speed_bonus || 0;
                                this.loopCount = this.existingResult.loop_count || 0;
                                this.loopBonus = this.existingResult.loop_bonus || 0;
                                this.totalPoints = this.existingResult.total_points || 300;
                                this.elapsedSeconds = this.existingResult.duration_seconds || 0;
                                this.selectedEmotion = this.existingResult.emotion || '-';
                                this.selectedPillar = this.existingResult.pillar || '-';
                                this.attempts = this.savedResultInfo ? (this.savedResultInfo.total_attempts || 1) : 1;

                                this.$watch('workspace', () => this.drawCanvas());
                                this.drawCanvas();
                                return;
                            }

                            let obstacleCount = 0;
                            if (this.level === 'Intermediate') {
                                this.gridSize = 6;
                                obstacleCount = 2;
                            } else if (this.level === 'Advanced') {
                                this.gridSize = 8;
                                obstacleCount = 4;
                            } else {
                                this.gridSize = 5;
                                obstacleCount = 0;
                            }

                            // 1. Position Start (Asrama 🏠) at bottom row with random X
                            const startY = this.gridSize - 1;
                            const startX = Math.floor(Math.random() * this.gridSize);
                            this.startTile = { x: startX, y: startY };

                            // 2. Position Finish (Masjid 🕌) at top row with random X
                            const finishY = 0;
                            const finishX = Math.floor(Math.random() * this.gridSize);
                            this.masjidTile = { x: finishX, y: finishY };

                            this.wuduTile = null;
                            this.kitabTile = null;
                            this.obstacles = [];

                            const isOccupied = (x, y) => {
                                if (x === this.startTile.x && y === this.startTile.y) return true;
                                if (x === this.masjidTile.x && y === this.masjidTile.y) return true;
                                if (this.wuduTile && x === this.wuduTile.x && y === this.wuduTile.y) return true;
                                if (this.kitabTile && x === this.kitabTile.x && y === this.kitabTile.y) return true;
                                if (this.obstacles.some(o => o.x === x && o.y === y)) return true;
                                return false;
                            };

                            // 3. Position Wudu (💧) at random middle row
                            let wx, wy;
                            do {
                                wx = Math.floor(Math.random() * this.gridSize);
                                wy = Math.floor(Math.random() * (this.gridSize - 2)) + 1;
                            } while (isOccupied(wx, wy));
                            this.wuduTile = { x: wx, y: wy };

                            // 4. Position Kitab (📖) at random middle row
                            let kx, ky;
                            do {
                                kx = Math.floor(Math.random() * this.gridSize);
                                ky = Math.floor(Math.random() * (this.gridSize - 2)) + 1;
                            } while (isOccupied(kx, ky));
                            this.kitabTile = { x: kx, y: ky };

                            // 5. Position Obstacles (🌧️)
                            for (let i = 0; i < obstacleCount; i++) {
                                let ox, oy, tries = 0;
                                do {
                                    ox = Math.floor(Math.random() * this.gridSize);
                                    oy = Math.floor(Math.random() * this.gridSize);
                                    tries++;
                                } while (isOccupied(ox, oy) && tries < 50);
                                if (tries < 50) {
                                    this.obstacles.push({ x: ox, y: oy });
                                }
                            }

                            // 6. Calculate Fair Target Steps Metric (Manhattan Distance Route):
                            const distA = Math.abs(this.startTile.x - this.wuduTile.x) + Math.abs(this.startTile.y - this.wuduTile.y)
                                        + Math.abs(this.wuduTile.x - this.kitabTile.x) + Math.abs(this.wuduTile.y - this.kitabTile.y)
                                        + Math.abs(this.kitabTile.x - this.masjidTile.x) + Math.abs(this.kitabTile.y - this.masjidTile.y);

                            const distB = Math.abs(this.startTile.x - this.kitabTile.x) + Math.abs(this.startTile.y - this.kitabTile.y)
                                        + Math.abs(this.kitabTile.x - this.wuduTile.x) + Math.abs(this.kitabTile.y - this.wuduTile.y)
                                        + Math.abs(this.wuduTile.x - this.masjidTile.x) + Math.abs(this.wuduTile.y - this.masjidTile.y);

                            const minDistance = Math.min(distA, distB);
                            // Set fair target steps: Min distance + 4 (for turns & action commands)
                            this.targetSteps = minDistance + 4;

                            this.startTimer();
                            this.resetBotPosition();
                            this.$watch('workspace', () => this.drawCanvas());
                        },

                        startTimer() {
                            this.stopTimer();
                            this.timerInterval = setInterval(() => {
                                this.elapsedSeconds++;
                            }, 1000);
                        },

                        stopTimer() {
                            if (this.timerInterval) {
                                clearInterval(this.timerInterval);
                                this.timerInterval = null;
                            }
                        },

                        formatTime(sec) {
                            let m = Math.floor(sec / 60).toString().padStart(2, '0');
                            let s = (sec % 60).toString().padStart(2, '0');
                            return `${m}:${s}`;
                        },

                        resetBotPosition() {
                            this.botX = this.startTile.x;
                            this.botY = this.startTile.y;
                            this.botDir = 3; // Facing NORTH towards top
                            this.hasWudu = false;
                            this.hasKitab = false;
                            this.debugMessage = '';
                            this.drawCanvas();
                        },

                        addInstruction(cmd) {
                            if (!this.isExecuting && !this.hasFinished) {
                                this.workspace.push(cmd);
                            }
                        },

                        removeStep(idx) {
                            if (!this.isExecuting && !this.hasFinished) {
                                this.workspace.splice(idx, 1);
                            }
                        },

                        clearWorkspace() {
                            if (!this.isExecuting && !this.hasFinished) {
                                this.workspace = [];
                                this.resetBotPosition();
                            }
                        },

                        getStepLabel(cmd) {
                            return {
                                'FORWARD': '⬆️ Maju 1 Langkah',
                                'TURN_LEFT': '↩️ Belok Kiri',
                                'TURN_RIGHT': '↪️ Belok Kanan',
                                'TAKE_WUDU': '💧 Ambil Wudu',
                                'TAKE_KITAB': '📖 Ambil Kitab',
                                'LOOP_2': '🔁 Ulangi 2x',
                            }[cmd] || cmd;
                        },

                        async runAlgorithm() {
                            if (this.workspace.length === 0 || this.isExecuting || this.hasFinished) return;

                            this.isExecuting = true;
                            this.attempts++; // Increment attempts each time they run
                            this.resetBotPosition();

                            // Expand loops seamlessly (Supports multiple LOOP_2 blocks in sequence)
                            let expandedSteps = [];
                            for (let i = 0; i < this.workspace.length; i++) {
                                let cmd = this.workspace[i];
                                if (cmd === 'LOOP_2') {
                                    // 1. Cari perintah aksi sebelumnya
                                    let prevAction = null;
                                    for (let p = i - 1; p >= 0; p--) {
                                        if (this.workspace[p] !== 'LOOP_2') {
                                            prevAction = this.workspace[p];
                                            break;
                                        }
                                    }

                                    // 2. Cari perintah aksi sesudahnya jika tidak ada sebelumnya
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
                                    }
                                } else {
                                    expandedSteps.push(cmd);
                                }
                            }

                            this.executedSteps = expandedSteps.length;

                            for (let stepIndex = 0; stepIndex < expandedSteps.length; stepIndex++) {
                                let cmd = expandedSteps[stepIndex];

                                if (cmd === 'FORWARD') {
                                    let dx = [1, 0, -1, 0][this.botDir];
                                    let dy = [0, 1, 0, -1][this.botDir];
                                    let nextX = this.botX + dx;
                                    let nextY = this.botY + dy;

                                    // Bounds Check
                                    if (nextX < 0 || nextX >= this.gridSize || nextY < 0 || nextY >= this.gridSize) {
                                        this.debugMessage = 'Aduhai! Bot Santri melangkah keluar dari area pesantren. Periksa kembali arah algoritmamu.';
                                        this.isExecuting = false;
                                        return;
                                    }

                                    // Obstacle Check
                                    if (this.obstacles.some(o => o.x === nextX && o.y === nextY)) {
                                        this.debugMessage = 'Aduh! Bot Santri tersandung rintangan becek. Periksa kembali rute algoritmamu!';
                                        this.isExecuting = false;
                                        return;
                                    }

                                    this.botX = nextX;
                                    this.botY = nextY;

                                    // Otomatis Ambil Wudu saat melewatinya
                                    if (this.botX === this.wuduTile.x && this.botY === this.wuduTile.y) {
                                        this.hasWudu = true;
                                    }

                                    // Otomatis Ambil Kitab saat melewatinya
                                    if (this.botX === this.kitabTile.x && this.botY === this.kitabTile.y) {
                                        this.hasKitab = true;
                                    }

                                } else if (cmd === 'TURN_LEFT') {
                                    this.botDir = (this.botDir + 3) % 4;
                                } else if (cmd === 'TURN_RIGHT') {
                                    this.botDir = (this.botDir + 1) % 4;
                                }

                                this.drawCanvas();
                                await new Promise(r => setTimeout(r, 380));
                            }

                            // Final Verification on Finish Tile (Masjid)
                            if (this.botX === this.masjidTile.x && this.botY === this.masjidTile.y) {
                                if (!this.hasWudu) {
                                    this.debugMessage = 'Aduhai, Bot Santri belum mengambil Wudu sebelum ke Masjid! Periksa kembali algoritmamu.';
                                    this.isExecuting = false;
                                    return;
                                }

                                if (!this.hasKitab) {
                                    this.debugMessage = 'Aduhai, Bot Santri belum mengambil Kitab Suci sebelum ke Masjid! Ayo ambil kitab dulu.';
                                    this.isExecuting = false;
                                    return;
                                }

                                // SUCCESS MISSION!
                                this.isExecuting = false;
                                this.calculateStarsAndPoints();
                                this.showReflectionModal = true;
                            } else {
                                this.debugMessage = 'Bot Santri belum sampai di Masjid (Finish). Periksa urutan langkah algoritmamu!';
                                this.isExecuting = false;
                            }
                        },

                        calculateStarsAndPoints() {
                            this.stopTimer();

                            const workspaceBlocks = this.workspace.length;
                            this.loopCount = this.workspace.filter(c => c === 'LOOP_2').length;
                            this.loopBonus = this.loopCount * 50; // Bonus +50 PTS per Loop (Nilai Plus!)

                            // 1. Stars based on Workspace Block Efficiency (Loops make code compact!)
                            if (workspaceBlocks <= this.targetSteps) {
                                this.earnedStars = 3;
                                this.basePoints = 300;
                            } else if (workspaceBlocks <= this.targetSteps + 4) {
                                this.earnedStars = 2;
                                this.basePoints = 200;
                            } else {
                                this.earnedStars = 1;
                                this.basePoints = 100;
                            }

                            // 2. Speed Bonus (Duration)
                            if (this.elapsedSeconds <= 30) {
                                this.speedBonus = 150;
                            } else if (this.elapsedSeconds <= 60) {
                                this.speedBonus = 100;
                            } else if (this.elapsedSeconds <= 120) {
                                this.speedBonus = 50;
                            } else {
                                this.speedBonus = 20;
                            }

                            // Total score = Base Points + Speed Bonus + Loop Bonus (Nilai Plus!)
                            this.totalPoints = this.basePoints + this.speedBonus + this.loopBonus;
                        },

                        submitReflection() {
                            this.showReflectionModal = false;
                            this.hasFinished = true; // Langsung tampilkan banner Misi Selesai di klien
                            
                            Livewire.find(this.$root.closest('[wire\\:id]').getAttribute('wire:id')).call('saveGameResult', this.earnedStars, this.executedSteps, {
                                emotion: this.selectedEmotion,
                                pillar: this.selectedPillar,
                                duration_seconds: this.elapsedSeconds,
                                base_points: this.basePoints,
                                speed_bonus: this.speedBonus,
                                loop_count: this.loopCount,
                                loop_bonus: this.loopBonus,
                                total_points: this.totalPoints,
                                gameplay_data: {
                                    level: this.level,
                                    gridSize: this.gridSize,
                                    targetSteps: this.targetSteps,
                                    startTile: this.startTile,
                                    wuduTile: this.wuduTile,
                                    kitabTile: this.kitabTile,
                                    masjidTile: this.masjidTile,
                                    obstacles: this.obstacles,
                                    workspace: [...this.workspace]
                                }
                            }, this.attempts, 'finished');
                        },

                        handleTimeout() {
                            if (this.hasFinished) return;
                            this.hasFinished = true;
                            this.earnedStars = 0;
                            this.totalPoints = this.basePoints + this.loopBonus;
                            
                            Livewire.find(this.$root.closest('[wire\\:id]').getAttribute('wire:id')).call('saveGameResult', this.earnedStars, this.executedSteps, {
                                emotion: '-',
                                pillar: '-',
                                duration_seconds: this.elapsedSeconds,
                                base_points: this.basePoints,
                                speed_bonus: 0,
                                loop_count: this.loopCount,
                                loop_bonus: this.loopBonus,
                                total_points: this.totalPoints,
                                gameplay_data: {
                                    level: this.level,
                                    gridSize: this.gridSize,
                                    targetSteps: this.targetSteps,
                                    startTile: this.startTile,
                                    wuduTile: this.wuduTile,
                                    kitabTile: this.kitabTile,
                                    masjidTile: this.masjidTile,
                                    obstacles: this.obstacles,
                                    workspace: [...this.workspace]
                                }
                            }, this.attempts, 'timeout');
                        },

                        drawCanvas() {
                            const canvas = document.getElementById('gameCanvas');
                            if (!canvas) return;
                            const ctx = canvas.getContext('2d');
                            const size = canvas.width;
                            const cellSize = size / this.gridSize;

                            // Clear Canvas with sleek dark background
                            ctx.fillStyle = '#09090b'; // zinc-950
                            ctx.fillRect(0, 0, size, size);

                            // Draw Bold Grid Tiles & High-Contrast Lines
                            for (let r = 0; r < this.gridSize; r++) {
                                for (let c = 0; c < this.gridSize; c++) {
                                    // Bold prominent grid lines (emerald-700 / zinc-600)
                                    ctx.strokeStyle = '#10b981'; // emerald-500 line accent
                                    ctx.lineWidth = 2;
                                    ctx.strokeRect(c * cellSize, r * cellSize, cellSize, cellSize);

                                    ctx.fillStyle = '#18181b'; // zinc-900 tile fill
                                    ctx.fillRect(c * cellSize + 2, r * cellSize + 2, cellSize - 4, cellSize - 4);
                                }
                            }

                            // Draw Special Locations
                            // 1. Asrama (Start)
                            this.drawTile(ctx, this.startTile.x, this.startTile.y, cellSize, '#064e3b', '🏠', 'Asrama');
                            // 2. Wudu (💧)
                            this.drawTile(ctx, this.wuduTile.x, this.wuduTile.y, cellSize, this.hasWudu ? '#0284c7' : '#0369a1', '💧', 'Wudu');
                            // 3. Kitab (📖)
                            this.drawTile(ctx, this.kitabTile.x, this.kitabTile.y, cellSize, this.hasKitab ? '#d97706' : '#b45309', '📖', 'Kitab');
                            // 4. Obstacles (🌧️)
                            this.obstacles.forEach(o => {
                                this.drawTile(ctx, o.x, o.y, cellSize, '#451a03', '🌧️', 'Becek');
                            });
                            // 5. Masjid (Masjid 🕌)
                            this.drawTile(ctx, this.masjidTile.x, this.masjidTile.y, cellSize, '#047857', '🕌', 'Masjid');

                            // Draw Bot Santri (👳)
                            const bx = this.botX * cellSize + cellSize / 2;
                            const by = this.botY * cellSize + cellSize / 2;

                            ctx.save();
                            ctx.beginPath();
                            ctx.arc(bx, by, cellSize / 2.8, 0, Math.PI * 2);
                            ctx.fillStyle = '#10b981'; // emerald-500
                            ctx.fill();
                            ctx.lineWidth = 3;
                            ctx.strokeStyle = '#ffffff';
                            ctx.stroke();

                            // Emoji Avatar inside Bot
                            ctx.font = `${cellSize * 0.4}px sans-serif`;
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillText('👳', bx, by);

                            // Facing Direction Arrow Indicator
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

                            // High-contrast tile border
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
                    }
                }
                </script>
            </div>
        @endif
    </div>
</div>
