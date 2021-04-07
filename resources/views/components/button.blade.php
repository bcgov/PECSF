@props(['style' => 'primary', 'icon' => '', 'size' => 'md'])

<{{($attributes['href'] ?? '' ? 'a' : 'button')}} 
    {{ $attributes->merge(['class' => 'btn btn-'.$style. ' btn-'.$size]) }}
    >
    @if ($icon)
        <x-fa-icon :icon="$icon ?? ''" />&nbsp;
    @endif
    {{ $slot }}
</{{($attributes['href'] ?? '' ? 'a' : 'button')}}>
