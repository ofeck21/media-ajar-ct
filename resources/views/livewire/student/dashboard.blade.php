<div class="min-h-[calc(100vh-4rem)] bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800 p-4 sm:p-8 flex flex-col justify-center items-center" wire:poll.3s>
    <x-slot:title>Dashboard Siswa</x-slot:title>

    <div class="max-w-2xl w-full space-y-6 text-center">
        <!-- Banner Sambutan -->
        <div class="bg-white dark:bg-zinc-800/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-emerald-100 dark:border-zinc-700 relative overflow-hidden">
            <div class="absolute -right-8 -top-8 size-32 bg-emerald-400/20 rounded-full blur-2xl"></div>
            <div class="absolute -left-8 -bottom-8 size-32 bg-teal-400/20 rounded-full blur-2xl"></div>

            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 px-4 py-1.5 rounded-full text-sm font-semibold mb-4 border border-emerald-200 dark:border-emerald-800">
                    <span class="animate-bounce">👋</span> Selamat Datang, {{ auth()->user()->name }}!
                </div>

                @if(!$classroom)
                    <div class="py-6 space-y-3">
                        <div class="size-16 mx-auto bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center">
                            <flux:icon.exclamation-circle class="size-8" />
                        </div>
                        <h2 class="text-xl font-bold text-zinc-800 dark:text-white">Kamu Belum Terdaftar di Kelas</h2>
                        <p class="text-zinc-500 text-sm max-w-md mx-auto">Silakan minta gurumu untuk memasukkan NISN (<span class="font-mono text-emerald-600 font-semibold">{{ auth()->user()->nisn }}</span>) ke dalam kelas pembelajaran.</p>
                    </div>
                @else
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl p-4 mb-6 shadow-lg flex items-center justify-between">
                        <div class="text-left">
                            <div class="text-xs text-emerald-100 font-medium">Kelas Kamu:</div>
                            <div class="text-xl font-black flex items-center gap-2">
                                <span>{{ $classroom->name }}</span>
                                @if($group)
                                    <span class="bg-white/20 text-white text-xs px-2.5 py-1 rounded-full font-bold">👥 {{ $group->name }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="bg-white/20 p-2.5 rounded-xl backdrop-blur-sm">
                            <flux:icon.academic-cap class="size-6 text-white" />
                        </div>
                    </div>

                    @if($activeSession)
                        @if($activeSession->status === 'waiting')
                            <div class="py-6 space-y-4">
                                <div class="relative size-20 mx-auto flex items-center justify-center">
                                    <div class="absolute inset-0 bg-emerald-400/30 rounded-full animate-ping"></div>
                                    <div class="relative bg-emerald-500 text-white p-4 rounded-full shadow-lg">
                                        <flux:icon.clock class="size-10" />
                                    </div>
                                </div>
                                <h3 class="text-2xl font-black text-zinc-800 dark:text-white">Sesi Permainan Siap Dimulai!</h3>
                                <p class="text-zinc-500 text-sm max-w-md mx-auto">Gurumu baru saja membuka sesi <strong class="text-emerald-600 dark:text-emerald-400">"{{ $activeSession->name }}"</strong>. Bersiaplah, halaman akan otomatis berpindah saat tes dimulai!</p>
                            </div>
                        @endif
                    @else
                        <div class="py-8 space-y-3">
                            <div class="size-16 mx-auto bg-zinc-100 dark:bg-zinc-700 text-zinc-400 rounded-2xl flex items-center justify-center">
                                <flux:icon.moon class="size-8" />
                            </div>
                            <h3 class="text-lg font-bold text-zinc-700 dark:text-zinc-300">Belum Ada Sesi Aktif</h3>
                            <p class="text-zinc-400 text-xs">Santai dulu! Halaman ini akan otomatis diperbarui begitu gurumu memulai sesi baru.</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Section Riwayat Permainan Siswa -->
        <div class="bg-white dark:bg-zinc-800 rounded-3xl p-6 shadow-xl border border-emerald-100 dark:border-zinc-700 space-y-4 text-left">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-700 pb-3">
                <div class="flex items-center gap-2">
                    <flux:icon.clock class="size-5 text-emerald-500" />
                    <h3 class="font-black text-base text-zinc-800 dark:text-white">Riwayat Permainan Kamu</h3>
                </div>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-950/60 px-3 py-1 rounded-full">
                    {{ $myResults->count() }} Permainan Selesai
                </span>
            </div>

            @if($myResults->isEmpty())
                <div class="py-8 text-center space-y-2">
                    <div class="text-3xl">🎮</div>
                    <p class="text-sm font-semibold text-zinc-600 dark:text-zinc-300">Belum Ada Riwayat Permainan</p>
                    <p class="text-xs text-zinc-400 max-w-sm mx-auto">Selesaikan sesi permainan pertamamu bersama teman kelompok untuk melihat catatan skor dan refleksimu di sini.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($myResults as $res)
                        @php
                            $reflections = $res->reflections ?? [];
                            $totalPts = $reflections['total_points'] ?? ($res->total_stars * 100);
                            $duration = $reflections['duration_seconds'] ?? 0;
                            $emotion = $reflections['emotion'] ?? '-';
                            $pillar = $reflections['pillar'] ?? '-';
                            $loopCount = $reflections['loop_count'] ?? 0;
                        @endphp
                        <div class="bg-zinc-50 dark:bg-zinc-900/80 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-700 space-y-3 shadow-sm hover:border-emerald-300 transition-all">
                            <!-- Header Row: Session Name, Group & Score Badge -->
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <div class="font-black text-sm text-zinc-800 dark:text-white flex items-center gap-2">
                                        <span>{{ $res->gameSession?->name ?? 'Sesi Permainan' }}</span>
                                        @if($res->group)
                                            <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 px-2 py-0.5 rounded-md">
                                                👥 {{ $res->group->name }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-zinc-400 mt-0.5 flex items-center gap-2">
                                        <span>📅 {{ $res->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                        <span>•</span>
                                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">Level: {{ $res->diagnostic_level ?? 'Basic' }}</span>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <div class="font-black text-base text-emerald-600 dark:text-emerald-400">
                                        +{{ number_format($totalPts) }} PTS ⭐
                                    </div>
                                    <div class="text-xs text-amber-500 font-bold">
                                        {{ str_repeat('⭐', $res->total_stars) }} ({{ $res->total_stars }} Star)
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Stats Pill -->
                            <div class="bg-white dark:bg-zinc-800 p-3 rounded-xl border border-zinc-100 dark:border-zinc-700/80 grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
                                <div>
                                    <span class="text-zinc-400 block text-[10px] uppercase font-bold">⏱️ Waktu</span>
                                    <span class="font-bold text-zinc-800 dark:text-white">{{ $duration }}s</span>
                                </div>
                                <div>
                                    <span class="text-zinc-400 block text-[10px] uppercase font-bold">😊 Emosi</span>
                                    <span class="font-bold text-zinc-800 dark:text-white truncate block">{{ $emotion }}</span>
                                </div>
                                <div>
                                    <span class="text-zinc-400 block text-[10px] uppercase font-bold">🧩 Pilar CT</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400 truncate block">{{ $pillar }}</span>
                                </div>
                                <div>
                                    <span class="text-zinc-400 block text-[10px] uppercase font-bold">🔁 Loop Used</span>
                                    <span class="font-bold text-purple-600 dark:text-purple-400">{{ $loopCount }}x Loop</span>
                                </div>
                            </div>

                            <!-- Action Playback Button -->
                            <button type="button" wire:click="openReplay({{ $res->id }})" class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold text-xs py-2.5 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-md hover:shadow-lg active:scale-95">
                                <span>🎬 Putar Ulang Permainan Ini</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Kartu Tips/Info CT -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-left">
            <div class="bg-white dark:bg-zinc-800 p-5 rounded-2xl border border-zinc-100 dark:border-zinc-700 shadow-md flex items-start gap-4">
                <div class="bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 p-3 rounded-xl">
                    <flux:icon.light-bulb class="size-6" />
                </div>
                <div>
                    <h4 class="font-bold text-sm text-zinc-800 dark:text-white">Apa itu CT?</h4>
                    <p class="text-xs text-zinc-500 mt-1">Computational Thinking membantu kita memecahkan masalah rumit dengan langkah-langkah sederhana!</p>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 p-5 rounded-2xl border border-zinc-100 dark:border-zinc-700 shadow-md flex items-start gap-4">
                <div class="bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 p-3 rounded-xl">
                    <flux:icon.user-group class="size-6" />
                </div>
                <div>
                    <h4 class="font-bold text-sm text-zinc-800 dark:text-white">Belajar Berkelompok</h4>
                    <p class="text-xs text-zinc-500 mt-1">Setelah tes awal, kamu akan berkolaborasi bersama teman kelompokmu!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL REPLAY PERMAINAN SISWA                             -->
    <!-- ========================================================= -->
    <div x-data="santriBotReplayEngine()" x-show="$wire.showReplayModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 text-left">
        <div class="bg-white dark:bg-zinc-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-emerald-100 dark:border-zinc-700 space-y-5 animate-scaleUp">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-700 pb-3">
                <div>
                    <h3 class="text-base font-black text-zinc-800 dark:text-white flex items-center gap-2">
                        <span>🎬 Replay Permainan Kamu</span>
                    </h3>
                    <p class="text-xs text-zinc-500 font-medium" x-text="$wire.replayStudent ? $wire.replayStudent.session_name : ''"></p>
                </div>
                <button type="button" wire:click="closeReplay" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-white text-xl font-bold p-1 cursor-pointer">&times;</button>
            </div>

            <!-- Student Performance Summary Card -->
            @if($replayStudent)
                <div class="bg-zinc-50 dark:bg-zinc-900/70 p-3 rounded-2xl border border-zinc-200 dark:border-zinc-700 grid grid-cols-4 gap-1 text-center text-xs font-semibold">
                    <div>
                        <span class="text-zinc-400 block text-[9px] uppercase font-bold">⭐ Total Skor</span>
                        <span class="font-black text-emerald-600 dark:text-emerald-400 text-xs">+{{ number_format($replayStudent['points']) }} PTS</span>
                    </div>
                    <div>
                        <span class="text-zinc-400 block text-[9px] uppercase font-bold">⏱️ Durasi</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400 text-xs">{{ $replayStudent['duration'] }}s</span>
                    </div>
                    <div>
                        <span class="text-zinc-400 block text-[9px] uppercase font-bold">😊 Emosi</span>
                        <span class="font-bold text-zinc-800 dark:text-white text-xs truncate block">{{ $replayStudent['emotion'] }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-400 block text-[9px] uppercase font-bold">🧩 Pilar CT</span>
                        <span class="font-bold text-amber-500 text-xs truncate block">{{ $replayStudent['pillar'] }}</span>
                    </div>
                </div>
            @endif

            <!-- Replay Canvas Screen -->
            <div class="space-y-4">
                <div class="relative mx-auto flex justify-center items-center bg-zinc-950 rounded-2xl p-2 shadow-inner overflow-hidden border-2 border-emerald-500/30">
                    <canvas id="studentReplayCanvas" width="340" height="340" class="rounded-xl shadow-md max-w-full h-auto"></canvas>
                </div>

                <!-- Sequence Chips with Active Highlighting -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-[11px] font-black text-zinc-500 uppercase tracking-wider">
                        <span>Urutan Algoritma Terpakai:</span>
                        <span x-text="workspace.length + ' Langkah'"></span>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900/60 rounded-2xl border-2 border-dashed border-zinc-200 dark:border-zinc-700 flex flex-wrap gap-1.5 items-center min-h-[55px] max-h-[110px] overflow-y-auto">
                        <template x-if="workspace.length === 0">
                            <div class="w-full text-center text-zinc-400 text-xs italic py-2">
                                Belum ada data rekaman permainan.
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
                    const canvas = document.getElementById('studentReplayCanvas');
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
</div>
