<div class="flex flex-col gap-6 w-full max-w-6xl mx-auto" wire:poll.3s>
    <x-slot:title>Daftar Siswa</x-slot:title>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <flux:heading size="xl">Daftar Siswa</flux:heading>
            <flux:subheading>Semua siswa terdaftar di sekolah Anda.</flux:subheading>
        </div>
        
        <div class="flex w-full sm:w-auto items-center gap-2">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari siswa atau NISN..." class="w-full sm:w-64" />
        </div>
    </div>

    <!-- Desktop & Mobile Table View -->
    <flux:card class="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                <thead class="bg-zinc-50/50 text-xs uppercase text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nama Siswa</th>
                        <th class="px-6 py-4 font-semibold">NISN / Username</th>
                        <th class="px-6 py-4 font-semibold">Kelas</th>
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
                                @forelse($student->studentClassrooms as $cls)
                                    <flux:badge size="sm" color="emerald" class="mr-1 mb-1">{{ $cls->name }}</flux:badge>
                                @empty
                                    <span class="text-xs text-zinc-400 italic">Belum ada kelas</span>
                                @endforelse
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" color="{{ $student->login_status['color'] }}">
                                    {{ $student->login_status['label'] }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="text-zinc-400 hover:text-zinc-600" wire:click="openEditModal({{ $student->id }})" />
                                <flux:button size="sm" variant="ghost" icon="trash" class="text-red-400 hover:text-red-600" wire:click="deleteStudent({{ $student->id }})" wire:confirm="Yakin ingin menghapus akun siswa {{ $student->name }}?" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <flux:icon.users class="size-8 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
                                <div class="text-zinc-500">Tidak ada siswa yang ditemukan.</div>
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

    <flux:modal name="edit-student-modal" class="min-w-[400px]">
        <form wire:submit="updateStudent">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Edit Data Siswa</flux:heading>
                    <flux:subheading>Ubah informasi nama, NISN, atau kata sandi siswa.</flux:subheading>
                </div>

                <flux:input wire:model="name" label="Nama Lengkap" placeholder="Masukkan nama siswa" required />
                
                <flux:input wire:model="nisn" label="NISN (Username)" placeholder="Masukkan NISN" required />

                <flux:input wire:model="password" type="password" label="Kata Sandi Baru (Kosongkan jika tidak diubah)" placeholder="Sandi baru..." viewable />

                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
