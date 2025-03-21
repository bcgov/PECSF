<li @if(isset($item['id'])) id="{{ $item['id'] }}" @endif class="nav-item has-treeview {{ $item['submenu_class'] }}">

    {{-- Menu toggler --}}
    <a class="nav-link py-3 {{ $item['class'] }} @if(isset($item['shift'])) {{ $item['shift'] }} @endif"
       href="{{ array_key_exists('url', $item) ? url($item['url']) : '' }}" {!! $item['data-compiled'] ?? '' !!}>

        <i class="xxx {{ $item['icon'] ?? 'far fa-fw fa-circle' }} {{
            isset($item['icon_color']) ? 'text-'.$item['icon_color'] : ''
        }}"></i>

        <p>
            {{ $item['text'] }}
            <i class="fas fa-angle-left right py-2"></i>

            @if(isset($item['label']))
                <span class="badge badge-{{ $item['label_color'] ?? 'primary' }} right">
                    {{ $item['label'] }}
                </span>
            @endif
        </p>

    </a>

    {{-- Menu items --}}
    <ul class="nav nav-treeview" role="menu" aria-labelledby="menubutton">
        @each('adminlte::partials.sidebar.menu-item', $item['submenu'], 'item')
    </ul>

</li>