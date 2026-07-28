<div class="flex flex-col gap-6 w-full max-w-6xl mx-auto">
    <x-slot:title>Manajemen Pengguna</x-slot:title>
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <flux:heading size="xl">Manajemen Pengguna</flux:heading>
            <flux:subheading>Kelola data akses Superadmin, Guru, dan Siswa.</flux:subheading>
        </div>
        
        <div class="flex w-full sm:w-auto flex-col sm:flex-row items-center gap-2">
            <flux:select wire:model.live="roleFilter" placeholder="Semua Peran" class="w-full sm:w-40">
                <flux:select.option value="">Semua Peran</flux:select.option>
                <flux:select.option value="superadmin">Superadmin</flux:select.option>
                <flux:select.option value="teacher">Guru</flux:select.option>
                <flux:select.option value="student">Siswa</flux:select.option>
            </flux:select>
            
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama, email, nisn..." class="w-full sm:w-64" />
            <flux:button variant="primary" icon="plus" class="hidden sm:flex">Tambah</flux:button>
        </div>
    </div>

    <!-- Mobile View: Cards -->
    <div class="grid grid-cols-1 gap-4 sm:hidden">
        @forelse ($users as $user)
            <flux:card>
                <div class="flex items-start gap-3">
                    <flux:avatar :name="$user->name" size="sm" />
                    <div class="flex-1">
                        <flux:heading size="md" class="truncate">{{ $user->name }}</flux:heading>
                        <flux:subheading class="text-xs mt-0.5 truncate">{{ $user->email ?? $user->username }}</flux:subheading>
                    </div>
                    <flux:badge size="sm" :color="$user->role === 'superadmin' ? 'red' : ($user->role === 'teacher' ? 'blue' : 'green')">
                        {{ ucfirst($user->role) }}
                    </flux:badge>
                </div>
                
                @if($user->school)
                <div class="mt-3 flex items-center gap-1.5 text-xs text-zinc-500">
                    <flux:icon.building-library class="size-3" />
                    <span class="truncate">{{ $user->school->name }}</span>
                </div>
                @endif
                
                <div class="mt-4 flex gap-2 justify-end border-t border-zinc-100 dark:border-zinc-800 pt-3">
                    <flux:button size="sm" variant="ghost" icon="pencil-square">Edit</flux:button>
                    <flux:button size="sm" variant="ghost" class="text-red-500 hover:text-red-600" icon="trash">Hapus</flux:button>
                </div>
            </flux:card>
        @empty
            <flux:card class="text-center py-12">
                <flux:icon.users class="size-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
                <flux:heading size="lg">Tidak ada pengguna ditemukan</flux:heading>
                <flux:subheading>Coba ubah kata kunci atau filter pencarian Anda.</flux:subheading>
            </flux:card>
        @endforelse
    </div>

    <!-- Desktop View: Table -->
    <flux:card class="hidden sm:block p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                <thead class="bg-zinc-50/50 text-xs uppercase text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Pengguna</th>
                        <th class="px-6 py-4 font-semibold">Peran (Role)</th>
                        <th class="px-6 py-4 font-semibold">Asal Sekolah</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($users as $user)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <flux:avatar :name="$user->name" size="sm" />
                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-xs text-zinc-500">{{ $user->email ?? $user->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" :color="$user->role === 'superadmin' ? 'red' : ($user->role === 'teacher' ? 'blue' : 'green')" class="font-medium">
                                    {{ ucfirst($user->role) }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->school)
                                    <div class="flex items-center gap-1.5">
                                        <flux:icon.building-library class="size-4 text-zinc-400" />
                                        {{ $user->school->name }}
                                    </div>
                                @else
                                    <span class="text-zinc-400 italic">Tidak ada sekolah</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="text-zinc-400 hover:text-zinc-600" />
                                <flux:button size="sm" variant="ghost" icon="trash" class="text-red-400 hover:text-red-600" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <flux:icon.users class="size-8 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
                                <div class="text-zinc-500">Tidak ada pengguna yang cocok dengan pencarian Anda.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $users->links() }}
            </div>
        @endif
    </flux:card>
</div>
