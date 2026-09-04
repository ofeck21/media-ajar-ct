@props([
    'variant' => 'default', // 'default', 'student', 'floating', 'menu-item'
])

<div x-data="{
    isFullscreen: false,
    checkFullscreen() {
        this.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
    },
    toggleFullscreen() {
        window.toggleFullscreen ? window.toggleFullscreen() : null;
        this.checkFullscreen();
    }
}"
x-init="checkFullscreen()"
@fullscreenchange.window="checkFullscreen()"
@webkitfullscreenchange.window="checkFullscreen()"
@mozfullscreenchange.window="checkFullscreen()"
@MSFullscreenChange.window="checkFullscreen()"
class="{{ $variant === 'menu-item' ? 'w-full' : 'inline-flex items-center' }}"
>
    @if($variant === 'student')
        <button 
            type="button" 
            @click="toggleFullscreen()"
            :title="isFullscreen ? 'Keluar Layar Penuh (ESC)' : 'Layar Penuh (Full Screen)'"
            class="text-xs bg-white/10 hover:bg-white/20 active:scale-95 text-white font-medium px-3 py-1.5 rounded-full transition-all flex items-center gap-1.5 border border-white/20 cursor-pointer shadow-xs"
            aria-label="Toggle Fullscreen"
        >
            <svg x-show="!isFullscreen" class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
            </svg>
            <svg x-show="isFullscreen" x-cloak class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
            </svg>
            <span class="hidden sm:inline" x-text="isFullscreen ? 'Keluar Fullscreen' : 'Layar Penuh'"></span>
        </button>
    @elseif($variant === 'floating')
        <button 
            type="button" 
            @click="toggleFullscreen()"
            :title="isFullscreen ? 'Keluar Layar Penuh (ESC)' : 'Layar Penuh (Full Screen)'"
            class="fixed bottom-5 right-5 z-50 p-3 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-110 active:scale-95 border border-white/20 focus:outline-none focus:ring-4 focus:ring-emerald-500/40 cursor-pointer"
            aria-label="Toggle Fullscreen"
        >
            <svg x-show="!isFullscreen" class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
            </svg>
            <svg x-show="isFullscreen" x-cloak class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
            </svg>
        </button>
    @elseif($variant === 'menu-item')
        <button
            type="button"
            @click="toggleFullscreen()"
            class="flex w-full items-center gap-2 px-2 py-1.5 text-xs sm:text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition-colors cursor-pointer"
        >
            <svg x-show="!isFullscreen" class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
            </svg>
            <svg x-show="isFullscreen" x-cloak class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
            </svg>
            <span x-text="isFullscreen ? 'Keluar Layar Penuh' : 'Mode Layar Penuh'"></span>
        </button>
    @else
        <button 
            type="button" 
            @click="toggleFullscreen()"
            :title="isFullscreen ? 'Keluar Layar Penuh (ESC)' : 'Layar Penuh (Full Screen)'"
            {{ $attributes->merge(['class' => 'relative inline-flex items-center justify-center p-2 rounded-xl text-zinc-600 dark:text-zinc-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 cursor-pointer active:scale-95']) }}
            aria-label="Toggle Fullscreen"
        >
            <svg x-show="!isFullscreen" class="size-5 transition-transform duration-200 hover:scale-110" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
            </svg>
            <svg x-show="isFullscreen" x-cloak class="size-5 transition-transform duration-200 hover:scale-110" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
            </svg>
        </button>
    @endif
</div>
