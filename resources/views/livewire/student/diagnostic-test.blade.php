<div class="min-h-[calc(100vh-4rem)] bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800 p-4 sm:p-8 flex justify-center items-center" @if($isSubmitted) wire:poll.3s @endif>
    <x-slot:title>Tes Diagnostik CT</x-slot:title>

    <div class="max-w-3xl w-full">
        @if($isSubmitted)
            <div class="bg-white dark:bg-zinc-800 rounded-3xl p-10 shadow-xl border border-emerald-100 dark:border-zinc-700 text-center space-y-6 animate-pulse">
                <div class="size-24 mx-auto bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 rounded-full flex items-center justify-center shadow-lg">
                    <flux:icon.check-circle class="size-14" />
                </div>
                <div class="space-y-2">
                    <h2 class="text-3xl font-black text-zinc-800 dark:text-white">Tes Diagnostik Selesai! 🎉</h2>
                    <p class="text-zinc-500 text-base max-w-md mx-auto">Jawabanmu sudah berhasil disimpan. Sistem sedang menunggu teman-teman yang lain dan menyiapkan pembagian kelompok terbaik!</p>
                </div>
                <div class="inline-flex items-center gap-2 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-300 px-4 py-2 rounded-full text-xs font-semibold border border-emerald-200 dark:border-emerald-800">
                    <flux:icon.arrow-path class="size-4 animate-spin" /> Sedang Menunggu Instruksi Guru...
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 rounded-3xl p-6 sm:p-8 shadow-xl border border-emerald-100 dark:border-zinc-700 space-y-8">
                <!-- Header Quizz -->
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-700 pb-6">
                    <div>
                        <div class="inline-flex items-center gap-1.5 text-xs font-extrabold text-emerald-600 bg-emerald-100 dark:bg-emerald-950/60 px-3 py-1 rounded-full uppercase tracking-wider mb-1">
                            🎯 Tes Diagnostik awal
                        </div>
                        <h2 class="text-2xl font-black text-zinc-800 dark:text-white">Computational Thinking</h2>
                    </div>
                    <div class="bg-emerald-500 text-white font-black px-4 py-2 rounded-2xl shadow-md text-sm">
                        {{ count($questions) }} Soal
                    </div>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 p-4 rounded-2xl text-sm flex items-center gap-3">
                        <flux:icon.exclamation-triangle class="size-5 shrink-0" />
                        <span>Mohon isi semua jawaban sebelum mengumpulkan hasil tes.</span>
                    </div>
                @endif

                <form wire:submit="submit" class="space-y-8">
                    @foreach($questions as $index => $q)
                        <div class="bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700 p-6 rounded-2xl space-y-4 hover:border-emerald-300 dark:hover:border-emerald-700 transition-all">
                            <div class="flex items-start gap-3">
                                <span class="bg-emerald-600 text-white font-black text-sm size-7 rounded-xl flex items-center justify-center shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <h3 class="font-bold text-base text-zinc-800 dark:text-zinc-100 leading-snug">
                                    {{ $q->question }}
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 gap-3 pt-2">
                                @foreach($q->options as $optIndex => $option)
                                    <label class="flex items-center gap-4 p-4 bg-white dark:bg-zinc-800 border-2 border-zinc-200 dark:border-zinc-700 rounded-2xl cursor-pointer hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/30 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/80 dark:has-[:checked]:bg-emerald-950/50">
                                        <input type="radio" wire:model="answers.{{ $q->id }}" value="{{ $optIndex }}" class="size-5 text-emerald-600 focus:ring-emerald-500 border-zinc-300" />
                                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-200 flex-1">{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold px-8 py-4 rounded-2xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-3 text-base">
                            <span>Kumpulkan Jawaban</span>
                            <flux:icon.paper-airplane class="size-5" />
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
