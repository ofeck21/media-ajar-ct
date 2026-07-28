<div class="flex flex-col gap-6 w-full max-w-6xl mx-auto" wire:poll.3s>
    <x-slot:title>Detail Kelas - {{ $classroom->name }}</x-slot:title>

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" size="sm" icon="arrow-left" href="{{ route('teacher.classrooms.index') }}" wire:navigate class="hidden sm:flex" />
            <div>
                <flux:heading size="xl" class="flex items-center gap-2">
                    <flux:button variant="ghost" size="sm" icon="arrow-left" href="{{ route('teacher.classrooms.index') }}" wire:navigate class="sm:hidden -ml-2" />
                    {{ $classroom->name }}
                </flux:heading>
                <flux:subheading>Daftar siswa terdaftar dan manajemen kelas.</flux:subheading>
            </div>
        </div>
        
        <div class="flex w-full sm:w-auto items-center gap-2 flex-wrap sm:flex-nowrap">
            <flux:modal.trigger name="create-session">
                <flux:button variant="primary" icon="play" class="hidden sm:flex">Buat Sesi Baru</flux:button>
            </flux:modal.trigger>

            @if($unassignedStudents->isNotEmpty())
                <flux:modal.trigger name="add-unassigned-student">
                    <flux:button variant="filled" icon="user-plus" class="hidden sm:flex">Pilih Siswa Tanpa Kelas ({{ $unassignedStudents->count() }})</flux:button>
                </flux:modal.trigger>
            @endif

            <flux:modal.trigger name="create-student">
                <flux:button icon="plus" class="hidden sm:flex">Tambah Siswa Baru</flux:button>
            </flux:modal.trigger>
            
            <flux:button variant="ghost" icon="trash" class="text-red-400 hover:text-red-600 hidden sm:flex" wire:click="confirmDeleteClassroom" />
            
            <flux:dropdown class="sm:hidden">
                <flux:button icon="ellipsis-vertical" />
                <flux:navmenu>
                    <flux:modal.trigger name="create-session">
                        <flux:navmenu.item icon="play">Buat Sesi Baru</flux:navmenu.item>
                    </flux:modal.trigger>
                    @if($unassignedStudents->isNotEmpty())
                        <flux:modal.trigger name="add-unassigned-student">
                            <flux:navmenu.item icon="user-plus">Pilih Siswa Tanpa Kelas</flux:navmenu.item>
                        </flux:modal.trigger>
                    @endif
                    <flux:modal.trigger name="create-student">
                        <flux:navmenu.item icon="plus">Tambah Siswa Baru</flux:navmenu.item>
                    </flux:modal.trigger>
                    <flux:navmenu.item icon="trash" wire:click="confirmDeleteClassroom">Hapus Kelas</flux:navmenu.item>
                </flux:navmenu>
            </flux:dropdown>
        </div>
    </div>
    
    <div class="mb-2 flex items-center justify-between">
        <flux:heading size="lg">Sesi Permainan (Game Sessions)</flux:heading>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        @foreach($classroom->gameSessions()->latest()->get() as $sess)
            <flux:card class="flex flex-col hover:border-emerald-500/50 transition-colors cursor-pointer relative">
                <a href="{{ route('teacher.sessions.show', $sess) }}" class="absolute inset-0 z-10" wire:navigate></a>
                <flux:heading size="md">{{ $sess->name }}</flux:heading>
                <div class="mt-2 text-xs text-zinc-500">
                    Dibuat: {{ $sess->created_at->format('d M Y H:i') }}
                </div>
                <div class="mt-4">
                    <flux:badge size="sm" color="{{ match($sess->status) {
                        'waiting' => 'zinc',
                        'diagnostic' => 'blue',
                        'grouping' => 'orange',
                        'playing' => 'emerald',
                        'finished' => 'red',
                    } }}">
                        {{ strtoupper($sess->status) }}
                    </flux:badge>
                </div>
            </flux:card>
        @endforeach
        @if($classroom->gameSessions()->count() === 0)
            <div class="col-span-full py-8 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-lg">
                <div class="text-zinc-500 text-sm">Belum ada sesi permainan yang dibuat.</div>
            </div>
        @endif
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-6 mb-2">
        <flux:heading size="lg">Daftar Siswa ({{ $students->total() }})</flux:heading>
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama siswa atau NISN..." class="w-full sm:w-64" />
    </div>

    <!-- Mobile View: Cards -->
    <div class="grid grid-cols-1 gap-4 sm:hidden">
        @forelse ($students as $student)
            <flux:card>
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <flux:avatar :name="$student->name" size="sm" />
                        <div>
                            <flux:heading size="md" class="truncate">{{ $student->name }}</flux:heading>
                            <flux:subheading class="text-xs mt-0.5 truncate">NISN: {{ $student->nisn }}</flux:subheading>
                        </div>
                    </div>
                    <flux:badge size="sm" color="{{ $student->login_status['color'] }}">
                        {{ $student->login_status['label'] }}
                    </flux:badge>
                </div>
                <div class="mt-4 flex gap-2 justify-end border-t border-zinc-100 dark:border-zinc-800 pt-3">
                    @if($otherClassrooms->isNotEmpty())
                        <flux:button size="sm" variant="ghost" class="text-blue-500 hover:text-blue-600" icon="arrow-right-circle" wire:click="openMoveModal({{ $student->id }})">Pindah</flux:button>
                    @endif
                    <flux:button size="sm" variant="ghost" class="text-red-500 hover:text-red-600" icon="trash" wire:click="confirmRemoveStudent({{ $student->id }}, '{{ addslashes($student->name) }}')">Keluarkan</flux:button>
                </div>
            </flux:card>
        @empty
            <flux:card class="text-center py-12">
                <flux:icon.users class="size-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
                <flux:heading size="lg">Belum ada siswa</flux:heading>
                <flux:subheading>Tambahkan siswa atau pilih dari daftar siswa tanpa kelas.</flux:subheading>
            </flux:card>
        @endforelse
    </div>

    <!-- Desktop View: Table -->
    <flux:card class="hidden sm:block p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                <thead class="bg-zinc-50/50 text-xs uppercase text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nama Siswa</th>
                        <th class="px-6 py-4 font-semibold">NISN / Username</th>
                        <th class="px-6 py-4 font-semibold">Status Login</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($students as $student)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <flux:avatar :name="$student->name" size="sm" />
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $student->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-mono text-xs text-zinc-500 bg-zinc-100 dark:bg-zinc-800 px-2.5 py-1 rounded inline-block">{{ $student->nisn }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" color="{{ $student->login_status['color'] }}">
                                    {{ $student->login_status['label'] }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                @if($otherClassrooms->isNotEmpty())
                                    <flux:button size="sm" variant="ghost" icon="arrow-right-circle" class="text-blue-500 hover:text-blue-600" title="Pindahkan Kelas" wire:click="openMoveModal({{ $student->id }})" />
                                @endif
                                <flux:button size="sm" variant="ghost" icon="trash" class="text-red-400 hover:text-red-600" title="Keluarkan Siswa" wire:click="confirmRemoveStudent({{ $student->id }}, '{{ addslashes($student->name) }}')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center">
                                <flux:icon.users class="size-8 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
                                <div class="text-zinc-500">Belum ada siswa di kelas ini.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $students->links() }}
            </div>
        @endif
    </flux:card>

    <!-- Modal: Pilih Siswa Tanpa Kelas (Multi-Select) -->
    <flux:modal name="add-unassigned-student" class="min-w-[450px]">
        <form wire:submit="addExistingStudent">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Pilih Siswa Tanpa Kelas</flux:heading>
                    <flux:subheading>Pilih satu atau beberapa siswa terdaftar yang belum memiliki kelas.</flux:subheading>
                </div>

                @error('selectedUnassignedStudentIds')
                    <div class="text-xs text-red-500 font-medium bg-red-50 dark:bg-red-950/50 p-2.5 rounded-lg border border-red-200 dark:border-red-800">
                        {{ $message }}
                    </div>
                @enderror

                <div class="max-h-60 overflow-y-auto space-y-2 border border-zinc-200 dark:border-zinc-700 rounded-xl p-3 bg-zinc-50/50 dark:bg-zinc-800/50">
                    @forelse($unassignedStudents as $st)
                        <label class="flex items-center gap-3 p-2.5 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 cursor-pointer transition-colors">
                            <input type="checkbox" wire:model="selectedUnassignedStudentIds" value="{{ $st->id }}" class="size-4 rounded text-emerald-600 focus:ring-emerald-500 border-zinc-300 dark:border-zinc-600" />
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm text-zinc-900 dark:text-white truncate">{{ $st->name }}</div>
                                <div class="text-xs text-zinc-400">NISN: {{ $st->nisn }}</div>
                            </div>
                        </label>
                    @empty
                        <div class="text-center py-6 text-xs text-zinc-500">
                            Semua siswa sudah memiliki kelas.
                        </div>
                    @endforelse
                </div>

                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" icon="plus">Masukkan ke Kelas</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    <!-- Modal: Tambah Siswa Baru -->
    <flux:modal name="create-student" class="min-w-[400px]">
        <form wire:submit="saveStudent">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Tambah Siswa Baru</flux:heading>
                    <flux:subheading>Akun siswa akan dibuat otomatis. Sandi bawaan: <span class="font-mono bg-zinc-100 px-1 dark:bg-zinc-800 font-bold text-emerald-600">12345678</span></flux:subheading>
                </div>

                <flux:input wire:model="name" label="Nama Lengkap" placeholder="Masukkan nama siswa" required />
                
                <flux:input wire:model="nisn" label="NISN (digunakan sebagai Username)" placeholder="Masukkan NISN unik" required />

                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Simpan Siswa</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    <!-- Modal: Buat Sesi Permainan -->
    <flux:modal name="create-session" class="min-w-[400px]">
        <form wire:submit="createSession">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Buat Sesi Permainan Baru</flux:heading>
                    <flux:subheading>Siswa di kelas ini dapat bergabung ke sesi untuk mulai belajar dan bermain.</flux:subheading>
                </div>

                <flux:input wire:model="sessionName" label="Nama Sesi" placeholder="Misal: Sesi Safari Mengaji 1" required />
                <flux:input wire:model="durationMinutes" type="number" min="1" max="180" label="Durasi Timer (Menit)" placeholder="10" required />

                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Buat Sesi</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    <!-- Modal: Pindahkan Kelas -->
    <flux:modal name="move-student" class="min-w-[400px]">
        <form wire:submit="moveStudent">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Pindahkan Siswa</flux:heading>
                    <flux:subheading>Pilih kelas tujuan untuk memindahkan siswa ini.</flux:subheading>
                </div>

                <flux:select wire:model="moveTargetClassroom" label="Kelas Tujuan" placeholder="Pilih kelas tujuan..." required>
                    @foreach($otherClassrooms as $cls)
                        <flux:select.option value="{{ $cls->id }}">{{ $cls->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" icon="arrow-right-circle">Pindahkan Kelas</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    <!-- Modal Konfirmasi Hapus Kelas (Tanpa Native Browser Alert) -->
    <flux:modal name="confirm-delete-classroom" class="min-w-[400px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="text-red-600 dark:text-red-400 flex items-center gap-2">
                    <flux:icon.exclamation-triangle class="size-6 shrink-0" />
                    Konfirmasi Hapus Kelas
                </flux:heading>
                <flux:subheading class="mt-2">Apakah Anda yakin ingin menghapus kelas <strong>{{ $classroom->name }}</strong>? Tindakan ini tidak dapat dibatalkan.</flux:subheading>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="deleteClassroom">Ya, Hapus Kelas</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Konfirmasi Keluarkan Siswa (Tanpa Native Browser Alert) -->
    <flux:modal name="confirm-remove-student" class="min-w-[400px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="text-red-600 dark:text-red-400 flex items-center gap-2">
                    <flux:icon.user-minus class="size-6 shrink-0" />
                    Konfirmasi Keluarkan Siswa
                </flux:heading>
                <flux:subheading class="mt-2">Apakah Anda yakin ingin mengeluarkan <strong>{{ $removingStudentName }}</strong> dari kelas <strong>{{ $classroom->name }}</strong>?</flux:subheading>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="executeRemoveStudent">Ya, Keluarkan Siswa</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
