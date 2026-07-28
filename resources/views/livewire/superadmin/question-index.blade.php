<div class="flex flex-col gap-6 w-full max-w-6xl mx-auto">
    <x-slot:title>Manajemen Soal Diagnostik</x-slot:title>
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <flux:heading size="xl">Bank Soal Diagnostik</flux:heading>
            <flux:subheading>Kelola soal-soal tes awal (Computational Thinking) untuk siswa.</flux:subheading>
        </div>
        
        <div class="flex w-full sm:w-auto items-center gap-2">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari soal..." class="w-full sm:w-64" />
            <flux:button variant="primary" icon="plus" wire:click="openCreateModal">Tambah Soal</flux:button>
        </div>
    </div>

    <!-- Desktop View: Table -->
    <flux:card class="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                <thead class="bg-zinc-50/50 text-xs uppercase text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold w-1/2">Pertanyaan</th>
                        <th class="px-6 py-4 font-semibold">Pilihan Jawaban</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($questions as $q)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-white line-clamp-2">{{ $q->question }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <ul class="list-disc pl-4 text-xs space-y-1">
                                    @foreach($q->options as $index => $option)
                                        <li class="{{ $index === $q->correct_answer_index ? 'text-emerald-600 dark:text-emerald-400 font-bold' : '' }}">
                                            {{ $option }}
                                            @if($index === $q->correct_answer_index)
                                                <flux:icon.check-circle class="inline size-3 ml-1" />
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="text-zinc-400 hover:text-zinc-600" wire:click="openEditModal({{ $q->id }})" />
                                <flux:button size="sm" variant="ghost" icon="trash" class="text-red-400 hover:text-red-600" wire:click="delete({{ $q->id }})" wire:confirm="Yakin ingin menghapus soal ini?" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center">
                                <flux:icon.document-text class="size-8 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
                                <div class="text-zinc-500">Belum ada soal diagnostik yang ditambahkan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($questions->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $questions->links() }}
            </div>
        @endif
    </flux:card>

    <flux:modal name="question-modal" class="min-w-[600px]">
        <form wire:submit="save">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ $isEditing ? 'Edit Soal' : 'Tambah Soal Baru' }}</flux:heading>
                </div>

                <flux:textarea wire:model="question" label="Pertanyaan" rows="3" required />
                
                <div class="space-y-4">
                    <flux:heading size="sm">Pilihan Jawaban</flux:heading>
                    
                    @foreach($options as $index => $option)
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center h-10">
                            <flux:radio wire:model="correct_answer_index" value="{{ $index }}" name="correct_answer" />
                        </div>
                        <div class="flex-1">
                            <flux:input wire:model="options.{{ $index }}" placeholder="Pilihan {{ chr(65 + $index) }}" required />
                        </div>
                    </div>
                    @endforeach
                    <div class="text-xs text-zinc-500 mt-2">Pilih tombol bulat di sebelah kiri untuk menandai jawaban yang benar.</div>
                </div>

                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Simpan Soal</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
