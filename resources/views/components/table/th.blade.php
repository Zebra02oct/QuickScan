@props(['align' => 'left'])

@php
    $alignClass =
        [
            'left' => 'text-left',
            'center' => 'text-center',
            'right' => 'text-right',
        ][$align] ?? 'text-left';
@endphp

<th
    {{ $attributes->merge(['class' => "px-3 sm:px-4 py-3 sm:py-3.5 font-semibold {$alignClass} uppercase text-[10px] sm:text-[0.7rem] tracking-wider transition-all"]) }}>
    {{ $slot }}
</th>
