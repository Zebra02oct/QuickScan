@props([
    'color' => 'primary',
    'size' => 'md',
    'icon' => null,
    'type' => 'button',
    'loadingText' => 'Memproses',
    'full' => false,
])

@php
    // 1. Tentukan Layout
    $displayClass = $full ? 'w-full flex' : 'inline-flex';

    // 2. Class Dasar
    $baseClass =
        $displayClass .
        ' relative items-center justify-center font-bold rounded-xl transition-all duration-300 hover:-translate-y-0.5 disabled:opacity-70 disabled:cursor-not-allowed overflow-hidden active:scale-95 group';

    // 3. Kamus Warna (Biru Langit, Kuning, Putih, Merah)
    $colors = [
        'primary' =>
            'bg-gradient-to-r from-sky-400 to-sky-600 text-white hover:from-sky-500 hover:to-sky-700 shadow-[0_4px_12px_rgba(56,189,248,0.3)] hover:shadow-[0_6px_16px_rgba(56,189,248,0.4)]',
        'warning' =>
            'bg-gradient-to-r from-yellow-300 to-yellow-500 text-yellow-900 hover:from-yellow-400 hover:to-yellow-600 shadow-[0_4px_12px_rgba(253,224,71,0.3)] hover:shadow-[0_6px_16px_rgba(253,224,71,0.4)]',
        'danger' =>
            'bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 shadow-[0_4px_12px_rgba(239,68,68,0.3)] hover:shadow-[0_6px_16px_rgba(239,68,68,0.4)]',
        'white' =>
            'bg-white text-gray-700 hover:bg-sky-50 border border-gray-200 hover:border-sky-200 hover:text-sky-600 shadow-sm hover:shadow-md',
    ];

    // 4. Kamus Ukuran
    $sizes = [
        'xs' => 'px-3 py-1.5 text-xs',
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];

    $iconSizes = [
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'md' => 'text-lg',
        'lg' => 'text-xl',
    ];

    $gapSizes = [
        'xs' => 'gap-1.5',
        'sm' => 'gap-2',
        'md' => 'gap-2',
        'lg' => 'gap-3',
    ];

    $compiledClasses =
        $baseClass . ' ' . ($colors[$color] ?? $colors['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
    $iconClass = $iconSizes[$size] ?? $iconSizes['md'];
    $gapClass = $gapSizes[$size] ?? $gapSizes['md'];

    $isLink = $attributes->has('href');

    $isLivewire =
        !$isLink &&
        ($attributes->has('wire:click') || $attributes->has('wire:submit') || $attributes->has('wire:target'));
    $target = $isLivewire
        ? $attributes->get('wire:target') ?? ($attributes->get('wire:click') ?? $attributes->get('wire:submit'))
        : null;
@endphp

@if ($isLink)
    <a {{ $attributes->merge(['class' => $compiledClasses]) }}>
        <div class="flex items-center justify-center w-full {{ $gapClass }}">
            @if ($icon)
                <i
                    class="{{ $icon }} flex-shrink-0 group-hover:animate-jiggle transition-transform {{ $iconClass }}"></i>
            @endif
            <span class="truncate">{{ $slot }}</span>
        </div>
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $compiledClasses]) }}
        @if ($isLivewire) wire:loading.attr="disabled" wire:target="{{ $target }}" @endif>

        @if ($isLivewire)
            <div wire:loading wire:target="{{ $target }}"
                class="flex items-center justify-center w-full {{ $gapClass }}">
                <span class="truncate">{{ $loadingText }}<span class="animate-ellipsis"></span></span>
            </div>

            <div wire:loading.remove wire:target="{{ $target }}"
                class="flex items-center justify-center w-full {{ $gapClass }}">
                @if ($icon)
                    <i
                        class="{{ $icon }} flex-shrink-0 group-hover:animate-jiggle transition-transform {{ $iconClass }}"></i>
                @endif
                <span class="truncate">{{ $slot }}</span>
            </div>
        @else
            <div class="flex items-center justify-center w-full {{ $gapClass }}">
                @if ($icon)
                    <i
                        class="{{ $icon }} flex-shrink-0 group-hover:animate-jiggle transition-transform {{ $iconClass }}"></i>
                @endif
                <span class="truncate">{{ $slot }}</span>
            </div>
        @endif

    </button>
@endif
