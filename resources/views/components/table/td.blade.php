@props(['align' => 'left'])

@php
    $alignClass =
        [
            'left' => 'text-left',
            'center' => 'text-center',
            'right' => 'text-right',
        ][$align] ?? 'text-left';
@endphp

<td
    {{ $attributes->merge(['class' => "px-3 sm:px-4 py-2.5 sm:py-3 text-[11px] sm:text-sm {$alignClass} transition-all"]) }}>
    {{ $slot }}
</td>
