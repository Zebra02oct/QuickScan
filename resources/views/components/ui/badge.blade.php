@props([
    'type' => 'default',
    'text' => null,
    'icon' => null,
    'dot' => true,
    'size' => 'xs',
    'absolute' => false,
    'full' => false,
    'scrollable' => false,
    'forceDarkText' => false,
])

@php

    $positionClass = $absolute ? 'absolute z-50' : '';

    $widthClass = $full
        ? ($scrollable
            ? 'w-full flex justify-start'
            : 'w-full flex justify-center')
        : 'inline-flex w-fit';

    $scrollClass = $scrollable ? 'overflow-x-auto no-scrollbar' : '';

    $baseClass = trim(
        "$positionClass $widthClass $scrollClass items-center gap-1.5 rounded-lg font-bold border transition-all duration-300",
    );

    $styles = [
        'primary' => 'bg-blue-50 text-blue-700 border-blue-200',
        'secondary' => 'bg-slate-100 text-slate-700 border-slate-200',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
        'info' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'danger' => 'bg-rose-50 text-rose-700 border-rose-200',
        'manual' => 'bg-orange-50 text-orange-700 border-orange-300',
        'penyesuaian' => 'bg-purple-50 text-purple-700 border-purple-200',

        'glass' =>
            'bg-white/20 border-white/20 backdrop-blur-md shadow-sm ' .
            ($forceDarkText ? 'text-slate-900' : 'text-white'),
        'glass-success' =>
            'bg-emerald-400/20 border-emerald-400/30 backdrop-blur-md shadow-sm ' .
            ($forceDarkText ? 'text-emerald-900' : 'text-emerald-50'),
        'glass-dark' =>
            'bg-black/10 border-black/5 backdrop-blur-md shadow-sm ' .
            ($forceDarkText ? 'text-slate-900' : 'text-white'),

        'glass-emerald' =>
            'bg-emerald-500/20 border-emerald-500/30 backdrop-blur-md shadow-sm ' .
            ($forceDarkText ? 'text-emerald-900' : 'text-emerald-50'),
        'glass-purple' =>
            'bg-purple-500/20 border-purple-500/30 backdrop-blur-md shadow-sm ' .
            ($forceDarkText ? 'text-purple-900' : 'text-purple-50'),
    ];

    $classes = $styles[$type] ?? $styles['secondary'];

    $dotColors = [
        'primary' => 'bg-blue-500',
        'secondary' => 'bg-slate-400',
        'warning' => 'bg-amber-500',
        'info' => 'bg-cyan-500',
        'success' => 'bg-emerald-500',
        'danger' => 'bg-rose-500',
        'manual' => 'bg-orange-500',
        'penyesuaian' => 'bg-purple-500',
        'glass' => 'bg-white',
        'glass-success' => 'bg-emerald-300',
        'glass-dark' => 'bg-white/80',

        'glass-emerald' => 'bg-emerald-400',
        'glass-purple' => 'bg-purple-400',
    ];

    $dotColor = $dotColors[$type] ?? 'bg-slate-400';

    $sizes = [
        'xs' => 'text-[9px] sm:text-[10px] px-2 py-0.5 uppercase tracking-wider',
        'sm' => 'text-[10px] sm:text-xs px-2.5 py-1',
        'base' => 'text-xs sm:text-sm px-3 py-1.5',
        'lg' => 'text-sm sm:text-base px-4 py-2',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['xs'];

    $iconSizes = [
        'xs' => 'text-[10px] sm:text-xs',
        'sm' => 'text-xs sm:text-sm',
        'base' => 'text-sm sm:text-base',
        'lg' => 'text-base sm:text-lg',
    ];
    $iconClass = $iconSizes[$size] ?? $iconSizes['xs'];

    $dotSizes = [
        'xs' => 'w-1 h-1',
        'sm' => 'w-1.5 h-1.5',
        'base' => 'w-2 h-2',
        'lg' => 'w-2.5 h-2.5',
    ];
    $dotSizeClass = $dotSizes[$size] ?? $dotSizes['xs'];

    $compiledClasses = "$baseClass $classes $sizeClass";
@endphp

<span {{ $attributes->merge(['class' => $compiledClasses]) }}>

    @if ($icon)
        <i class="{{ $icon }} {{ $iconClass }} flex-shrink-0"></i>
    @elseif ($dot)
        <span class="{{ $dotSizeClass }} rounded-full {{ $dotColor }} flex-shrink-0 animate-pulse"></span>
    @endif

    <span class="{{ $scrollable ? 'whitespace-nowrap' : 'truncate' }}">
        {{ $text ?? $slot }}
    </span>

</span>
