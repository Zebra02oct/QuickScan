@props([
    'title' => 'Modal',
    'icon' => 'ri-information-line',
    'iconColor' => 'text-sky-500',
    'maxWidth' => 'md',
])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '4xl' => 'max-w-4xl',
        default => 'max-w-md',
    };
@endphp

<template x-teleport="body">
    <div x-data="{
        open: false,
        isSaving: false,
        isLoading: false,
        openModal() {
            this.open = true;
            document.body.classList.add('overflow-hidden');
        },
        closeModal(force = false) {
            if (this.isSaving && !force) return;
    
            this.open = false;
            this.isSaving = false;
            document.body.classList.remove('overflow-hidden');
        }
    }" {{ $attributes }} @close-modal.window="closeModal(true)"
        @is-saving.window="isSaving = $event.detail" @keydown.escape.window="closeModal()">

        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 z-[999998] ">
        </div>

        <div x-show="open" x-cloak @click.self="closeModal()"
            class="fixed inset-0 z-[999999] flex items-center justify-center p-4 sm:p-6">

            <div x-show="open" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="rounded-2xl shadow-2xl {{ $maxWidthClass }} w-full relative overflow-hidden bg-white border border-slate-200/60 max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-4rem)] flex flex-col">

                <div x-show="isLoading" class="flex flex-col h-full bg-white animate-pulse w-full">
                    <div
                        class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-slate-200"></div>
                            <div class="h-5 w-32 bg-slate-200 rounded-md"></div>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-slate-200"></div>
                    </div>

                    <div class="p-5 sm:p-6 space-y-6 flex-1 overflow-hidden">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-200 rounded-full shrink-0"></div>
                            <div class="space-y-2 w-full">
                                <div class="h-4 w-40 sm:w-48 bg-slate-200 rounded"></div>
                                <div class="h-3 w-24 sm:w-32 bg-slate-200 rounded"></div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="h-16 sm:h-20 w-full bg-slate-200 rounded-xl"></div>
                            <div class="h-16 sm:h-20 w-full bg-slate-200 rounded-xl"></div>
                        </div>
                    </div>

                    <div
                        class="px-5 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2 sm:gap-3 shrink-0">
                        <div class="h-9 w-20 bg-slate-200 rounded-lg hidden sm:block"></div>
                        <div class="h-9 w-full sm:w-32 bg-slate-200 rounded-lg"></div>
                    </div>
                </div>

                <div x-show="!isLoading" style="display: none;"
                    class="flex flex-col h-full overflow-hidden animate-[fadeIn_0.3s_ease-in-out]">

                    <div
                        class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
                        <h3 class="text-[1.1rem] font-bold text-slate-800 flex items-center gap-2">
                            <i class="{{ $icon }} {{ $iconColor }} text-xl"></i> {{ $title }}
                        </h3>
                        <button type="button" @click="closeModal()" :disabled="isSaving"
                            :class="isSaving ? 'opacity-50 cursor-not-allowed' : 'hover:text-rose-600 hover:bg-rose-50'"
                            class="text-slate-400 bg-slate-100 rounded-full w-8 h-8 flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-rose-500/20">
                            <i class="ri-close-line text-xl font-bold"></i>
                        </button>
                    </div>

                    <div class="overflow-y-auto p-5 custom-scrollbar text-slate-700">
                        {{ $slot }}
                    </div>

                    @isset($footer)
                        <div
                            class="px-5 py-4 bg-slate-50 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3 shrink-0">
                            {{ $footer }}
                        </div>
                    @endisset

                </div>
            </div>
        </div>
    </div>
</template>
