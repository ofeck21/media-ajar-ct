<div class="flex flex-col gap-6 w-full max-w-6xl mx-auto">
    <x-slot:title>Manajemen Sekolah</x-slot:title>
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <flux:heading size="xl">Manajemen Sekolah</flux:heading>
            <flux:subheading>Kelola data sekolah dan jumlah pengguna terdaftar.</flux:subheading>
        </div>
        
        <div class="flex w-full sm:w-auto items-center gap-2">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari sekolah..." class="w-full sm:w-64" />
            <flux:button variant="primary" icon="plus" class="hidden sm:flex">Tambah</flux:button>
        </div>
    </div>

    <!-- Mobile View: Cards -->
    <div class="grid grid-cols-1 gap-4 sm:hidden">
        @forelse ($schools as $school)
            <flux:card>
                <div class="flex justify-between items-start">
                    <div>
                        <flux:heading size="lg">{{ $school->name }}</flux:heading>
                        <flux:subheading class="mt-1 flex items-center gap-1">
                            <flux:icon.map-pin class="size-4" />
                            {{ $school->address ?? 'Alamat belum diatur' }}
                        </flux:subheading>
                    </div>
                    <flux:badge size="sm" color="zinc">{{ $school->users_count }} Pengguna</flux:badge>
                </div>
                <div class="mt-4 flex gap-2 justify-end border-t border-zinc-100 dark:border-zinc-800 pt-3">
                    <flux:button size="sm" variant="ghost" icon="pencil-square">Edit</flux:button>
                    <flux:button size="sm" variant="ghost" class="text-red-500 hover:text-red-600" icon="trash">Hapus</flux:button>
                </div>
            </flux:card>
        @empty
            <flux:card class="text-center py-12">
                <flux:icon.building-library class="size-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
                <flux:heading size="lg">Tidak ada sekolah ditemukan</flux:heading>
                <flux:subheading>Coba ubah kata kunci pencarian Anda.</flux:subheading>
            </flux:card>
        @endforelse
    </div>

    <!-- Desktop View: Table -->
    <flux:card class="hidden sm:block p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                <thead class="bg-zinc-50/50 text-xs uppercase text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nama Sekolah</th>
                        <th class="px-6 py-4 font-semibold">Alamat</th>
                        <th class="px-6 py-4 font-semibold text-center">Pengguna</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($schools as $school)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-white flex items-center gap-3">
                                    <div class="size-8 rounded-md bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                        <flux:icon.building-library class="size-4" />
                                    </div>
                                    {{ $school->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                {{ $school->address ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:badge size="sm" color="zinc">{{ $school->users_count }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="text-zinc-400 hover:text-zinc-600" />
                                <flux:button size="sm" variant="ghost" icon="trash" class="text-red-400 hover:text-red-600" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <flux:icon.magnifying-glass class="size-8 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
                                <div class="text-zinc-500">Tidak ada sekolah ditemukan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($schools->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $schools->links() }}
            </div>
        @endif
    </flux:card>
</div>
