@props([
    'placeholder' => 'Cari data...',
    'size' => 'md',
])

@php

    $inputSizes = [
        'sm' => 'py-1.5 pl-8 pr-3 text-[10px] sm:text-[11px] rounded-lg',
        'md' => 'py-2 pl-9 pr-4 text-[11px] sm:text-xs rounded-md',
        'lg' => 'py-2.5 pl-10 pr-4 text-sm sm:text-base rounded-2xl',
    ];

    $iconSizes = [
        'sm' => 'text-xs left-2.5',
        'md' => 'text-sm left-3.5',
        'lg' => 'text-base left-4',
    ];

    $spinnerSizes = [
        'sm' => 'w-3 h-3 right-2.5',
        'md' => 'w-4 h-4 right-3.5',
        'lg' => 'w-5 h-5 right-4',
    ];

    $inputClass = $inputSizes[$size] ?? $inputSizes['md'];
    $iconClass = $iconSizes[$size] ?? $iconSizes['md'];
    $spinnerClass = $spinnerSizes[$size] ?? $spinnerSizes['md'];

    $wireModel = $attributes->wire('model')->value();
@endphp


<div {{ $attributes->only('class')->merge(['class' => 'relative shadow-sm group']) }}>


    <i
        class="ri-search-line absolute top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors {{ $iconClass }}"></i>


    <input {{ $attributes->except('class') }} placeholder="{{ $placeholder }}"
        class="w-full font-medium border text-black  border-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 bg-white transition-all hover:border-blue-300 {{ $inputClass }}"
        type="text" />


    @if ($wireModel)
        <div wire:loading wire:target="{{ $wireModel }}" class="absolute top-1/2 -translate-y-1/2 {{ $spinnerClass }}">
            <div class="animate-spin w-full h-full border-2 border-blue-500 border-t-transparent rounded-full"></div>
        </div>
    @endif
</div>
