<div class="flex flex-col gap-6 w-full max-w-6xl mx-auto">
    <x-slot:title>Manajemen Kelas</x-slot:title>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <flux:heading size="xl">Manajemen Kelas</flux:heading>
            <flux:subheading>Daftar kelas yang Anda ajar.</flux:subheading>
        </div>
        
        <div class="flex w-full sm:w-auto items-center gap-2">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari kelas..." class="w-full sm:w-64" />
            <flux:modal.trigger name="create-classroom">
                <flux:button variant="primary" icon="plus" class="hidden sm:flex">Tambah Kelas</flux:button>
            </flux:modal.trigger>
            <flux:modal.trigger name="create-classroom">
                <flux:button variant="primary" icon="plus" class="sm:hidden" />
            </flux:modal.trigger>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($classrooms as $classroom)
            <flux:card class="relative flex flex-col hover:border-emerald-500/50 transition-colors">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <flux:icon.academic-cap class="size-6" />
                        </div>
                        <div>
                            <flux:heading size="lg">{{ $classroom->name }}</flux:heading>
                            <flux:subheading class="mt-0.5 text-xs flex items-center gap-1">
                                <flux:icon.users class="size-3" />
                                {{ $classroom->students()->count() }} Siswa
                            </flux:subheading>
                        </div>
                    </div>
                </div>
                
                <div class="mt-auto pt-4 flex gap-2">
                    <flux:button size="sm" variant="filled" class="w-full" href="{{ route('teacher.classrooms.show', $classroom) }}" wire:navigate>Lihat Kelas</flux:button>
                </div>
            </flux:card>
        @empty
            <div class="col-span-full py-16 text-center">
                <flux:icon.academic-cap class="size-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
                <flux:heading size="lg">Belum ada kelas</flux:heading>
                <flux:subheading>Buat kelas pertama Anda untuk mengundang siswa bermain.</flux:subheading>
                <flux:modal.trigger name="create-classroom">
                    <flux:button variant="primary" class="mt-6" icon="plus">Tambah Kelas Baru</flux:button>
                </flux:modal.trigger>
            </div>
        @endforelse
    </div>
    
    @if($classrooms->hasPages())
        <div class="pt-4">
            {{ $classrooms->links() }}
        </div>
    @endif

    <flux:modal name="create-classroom" class="min-w-[400px]">
        <form wire:submit="save">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Tambah Kelas Baru</flux:heading>
                    <flux:subheading>Masukkan nama kelas (misal: Kelas 7A).</flux:subheading>
                </div>

                <flux:input wire:model="name" label="Nama Kelas" placeholder="Nama kelas" required />

                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
