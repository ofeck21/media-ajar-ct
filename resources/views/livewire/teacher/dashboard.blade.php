<div>
    <div class="mb-8">
        <flux:heading size="xl" class="mb-2">Dashboard Guru</flux:heading>
        <flux:subheading>Selamat datang, {{ auth()->user()->name }}! Berikut ringkasan aktivitas kelas Anda.</flux:subheading>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-xl">
                    <flux:icon.building-library class="size-6" />
                </div>
                <div>
                    <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Kelas</div>
                    <div class="text-2xl font-bold">{{ $totalClassrooms }}</div>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <flux:icon.users class="size-6" />
                </div>
                <div>
                    <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Siswa</div>
                    <div class="text-2xl font-bold">{{ $totalStudents }}</div>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 rounded-xl">
                    <flux:icon.play-circle class="size-6" />
                </div>
                <div>
                    <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Sesi Permainan</div>
                    <div class="text-2xl font-bold">{{ $totalSessions }}</div>
                </div>
            </div>
        </flux:card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Sessions -->
        <div class="lg:col-span-2 space-y-6">
            <flux:card>
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="lg">Sesi Permainan Terbaru</flux:heading>
                    <flux:button size="sm" variant="subtle" :href="route('teacher.classrooms.index')" wire:navigate>Lihat Semua Kelas</flux:button>
                </div>
                
                @if($recentSessions->isEmpty())
                    <div class="py-12 text-center text-zinc-500">
                        <flux:icon.calendar class="size-12 mx-auto mb-3 opacity-20" />
                        <p>Belum ada sesi permainan yang dibuat.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($recentSessions as $session)
                            <div class="flex items-center justify-between p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <div class="flex gap-4 items-center">
                                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                                        <flux:icon.play-circle class="size-5 text-zinc-500" />
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $session->name }}</div>
                                        <div class="text-sm text-zinc-500 flex items-center gap-2">
                                            <span>{{ $session->classroom->name ?? 'Tanpa Kelas' }}</span>
                                            <span>•</span>
                                            <span>{{ $session->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <flux:badge size="sm" color="{{ match($session->status) { 'playing' => 'emerald', 'finished' => 'zinc', 'grouping' => 'blue', default => 'amber' } }}">
                                        {{ ucfirst($session->status) }}
                                    </flux:badge>
                                    <flux:button size="sm" variant="subtle" icon="arrow-right" :href="route('teacher.sessions.show', $session)" wire:navigate />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>
        </div>

        <!-- Quick Actions -->
        <div class="space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">Akses Cepat</flux:heading>
                <div class="space-y-3">
                    <flux:button class="w-full justify-start" icon="plus" variant="primary" :href="route('teacher.classrooms.index')" wire:navigate>
                        Buat Kelas Baru
                    </flux:button>
                    <flux:button class="w-full justify-start" icon="users" variant="outline" :href="route('teacher.students.index')" wire:navigate>
                        Kelola Daftar Siswa
                    </flux:button>
                </div>
            </flux:card>

            <flux:card class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/30 border-emerald-100 dark:border-emerald-900/50">
                <div class="flex items-start gap-4">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 rounded-lg shrink-0">
                        <flux:icon.light-bulb class="size-5" />
                    </div>
                    <div>
                        <h4 class="font-bold text-emerald-900 dark:text-emerald-100 mb-1">Tips Hari Ini</h4>
                        <p class="text-sm text-emerald-700 dark:text-emerald-300">
                            Pastikan untuk selalu mengecek hasil Jurnal Refleksi siswa setelah sesi berakhir agar Anda dapat mengetahui perkembangan emosi dan pemahaman CT mereka.
                        </p>
                    </div>
                </div>
            </flux:card>
        </div>
    </div>
</div>
