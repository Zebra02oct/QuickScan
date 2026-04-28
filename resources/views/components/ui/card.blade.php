@props([
    'title',
    'value' => 0,
    'subtitle' => null,
    'icon',
    'subtitleIcon' => null,
    'color' => 'primary',
    'size' => 'base',
    'badge' => null,
    'onClick' => null,
])

@php

    $themes = [
        'primary' => [
            'card_bg' => 'bg-white border-slate-200',
            'title_text' => 'text-slate-500',
            'value_text' => 'text-slate-800',
            'subtitle_text' => 'text-slate-400',
            'icon_text' => 'text-blue-500',
            'icon_bg' => 'bg-blue-50',
            'badge_bg' => 'bg-blue-100/50 text-blue-600 border border-blue-200',
            'circle_bg' => 'bg-blue-50',
        ],
        'warning' => [
            'card_bg' => 'bg-white border-slate-200',
            'title_text' => 'text-slate-500',
            'value_text' => 'text-slate-800',
            'subtitle_text' => 'text-amber-600 font-medium',
            'icon_text' => 'text-amber-500',
            'icon_bg' => 'bg-amber-50',
            'badge_bg' => 'bg-amber-100/50 text-amber-600 border border-amber-200',
            'circle_bg' => 'bg-amber-50',
        ],
        'success' => [
            'card_bg' => 'bg-white border-slate-200',
            'title_text' => 'text-slate-500',
            'value_text' => 'text-slate-800',
            'subtitle_text' => 'text-emerald-600 font-medium',
            'icon_text' => 'text-emerald-500',
            'icon_bg' => 'bg-emerald-50',
            'badge_bg' => 'bg-emerald-100/50 text-emerald-600 border border-emerald-200',
            'circle_bg' => 'bg-emerald-50',
        ],
        'purple' => [
            'card_bg' => 'bg-white border-slate-200',
            'title_text' => 'text-slate-500',
            'value_text' => 'text-slate-800',
            'subtitle_text' => 'text-purple-400',
            'icon_text' => 'text-purple-500',
            'icon_bg' => 'bg-purple-50',
            'badge_bg' => 'bg-purple-100/50 text-purple-600 border border-purple-200',
            'circle_bg' => 'bg-purple-50',
        ],
        'danger' => [
            'card_bg' => 'bg-red-50 border-red-200',
            'title_text' => 'text-red-700',
            'value_text' => 'text-red-700',
            'subtitle_text' => 'text-red-600 font-medium',
            'icon_text' => 'text-red-600',
            'icon_bg' => 'bg-red-100',
            'badge_bg' => 'bg-red-200 text-red-700 border border-red-300',
            'circle_bg' => 'bg-red-100',
        ],

        'glass-primary' => [
            'card_bg' => 'bg-white/10 backdrop-blur-md border-white/20',
            'title_text' => 'text-blue-100',
            'value_text' => 'text-white',
            'subtitle_text' => 'text-blue-200/80 font-medium',
            'icon_text' => 'text-blue-300',
            'icon_bg' => 'bg-blue-500/20 border border-blue-400/30 shadow-sm',
            'badge_bg' => 'bg-blue-500/30 text-blue-100 border border-blue-400/30',
            'circle_bg' => 'bg-white/5',
        ],
        'glass-warning' => [
            'card_bg' => 'bg-white/10 backdrop-blur-md border-white/20',
            'title_text' => 'text-blue-100',
            'value_text' => 'text-white',
            'subtitle_text' => 'text-amber-300 font-medium',
            'icon_text' => 'text-amber-400',
            'icon_bg' => 'bg-amber-500/20 border border-amber-400/30 shadow-sm',
            'badge_bg' => 'bg-amber-500/30 text-amber-100 border border-amber-400/30',
            'circle_bg' => 'bg-white/5',
        ],
        'glass-success' => [
            'card_bg' => 'bg-white/10 backdrop-blur-md border-white/20',
            'title_text' => 'text-blue-100',
            'value_text' => 'text-white',
            'subtitle_text' => 'text-emerald-300 font-medium',
            'icon_text' => 'text-emerald-400',
            'icon_bg' => 'bg-emerald-500/20 border border-emerald-400/30 shadow-sm',
            'badge_bg' => 'bg-emerald-500/30 text-emerald-100 border border-emerald-400/30',
            'circle_bg' => 'bg-white/5',
        ],
        'glass-purple' => [
            'card_bg' => 'bg-white/10 backdrop-blur-md border-white/20',
            'title_text' => 'text-blue-100',
            'value_text' => 'text-white',
            'subtitle_text' => 'text-purple-300 font-medium',
            'icon_text' => 'text-purple-400',
            'icon_bg' => 'bg-purple-500/20 border border-purple-400/30 shadow-sm',
            'badge_bg' => 'bg-purple-500/30 text-purple-100 border border-purple-400/30',
            'circle_bg' => 'bg-white/5',
        ],
    ];

    $sizes = [
        'xs' => [
            'padding' => 'p-3',
            'title' => 'text-xs',
            'value' => 'text-xl',
            'icon' => 'text-sm p-1.5',
            'circle' => 'w-10 h-10 -right-2 -top-2',
        ],
        'sm' => [
            'padding' => 'p-4',
            'title' => 'text-sm',
            'value' => 'text-2xl',
            'icon' => 'text-base p-2',
            'circle' => 'w-12 h-12 -right-3 -top-3',
        ],
        'base' => [
            'padding' => 'p-5',
            'title' => 'text-sm font-medium',
            'value' => 'text-3xl',
            'icon' => 'text-xl p-2.5',
            'circle' => 'w-16 h-16 -right-4 -top-4',
        ],
        'lg' => [
            'padding' => 'p-6',
            'title' => 'text-base font-semibold',
            'value' => 'text-4xl',
            'icon' => 'text-2xl p-3',
            'circle' => 'w-20 h-20 -right-5 -top-5',
        ],
    ];

    $theme = $themes[$color] ?? $themes['primary'];
    $sizing = $sizes[$size] ?? $sizes['base'];
@endphp

<div @if ($onClick) @click="{{ $onClick }}" @endif
    class="{{ $theme['card_bg'] }} {{ $sizing['padding'] }} rounded-xl border shadow-sm hover:shadow-md hover:-translate-y-1 transition-all {{ $onClick ? 'cursor-pointer' : 'cursor-default' }} group relative overflow-hidden">

    <div
        class="absolute {{ $sizing['circle'] }} {{ $theme['circle_bg'] }} rounded-full group-hover:scale-150 transition-transform duration-500">
    </div>

    <div class="relative z-10 flex flex-col h-full justify-between">
        <div class="flex justify-between items-start mb-2 gap-2">
            <div>
                <p class="{{ $sizing['title'] }} {{ $theme['title_text'] }}">
                    {{ $title }}
                </p>

                @if ($badge)
                    <span
                        class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold rounded-md {{ $theme['badge_bg'] }}">
                        {{ $badge }}
                    </span>
                @endif
            </div>

            <div
                class="{{ $theme['icon_bg'] }} {{ $theme['icon_text'] }} rounded-lg flex items-center justify-center transition-transform group-hover:rotate-12 shrink-0">
                <i class="{{ $icon }} {{ $sizing['icon'] }}"></i>
            </div>
        </div>

        <div>
            <h3 class="font-bold {{ $sizing['value'] }} {{ $theme['value_text'] }} tracking-tight mt-1">
                {{ $value ?? 0 }}
            </h3>

            @if ($subtitle)
                <p class="text-[11px] md:text-xs {{ $theme['subtitle_text'] }} mt-1.5 flex items-center">
                    @if ($subtitleIcon)
                        <i class="{{ $subtitleIcon }} mr-1"></i>
                    @endif
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    </div>
</div>
