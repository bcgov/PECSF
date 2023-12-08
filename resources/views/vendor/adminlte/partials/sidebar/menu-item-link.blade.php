<li @isset($item['id']) id="{{ $item['id'] }}" @endisset class="nav-item" role="none">

    <a class="nav-link py-3 {{ $item['class'] }} @isset($item['shift']) {{ $item['shift'] }} @endisset" 
       href="{{ $item['href'] }}" @isset($item['target']) target="{{ $item['target'] }}" @endisset  role="menuitem"
       {!! $item['data-compiled'] ?? '' !!}>

        <i class="{{ $item['icon'] ?? 'far fa-fw fa-circle' }} {{
            isset($item['icon_color']) ? 'text-'.$item['icon_color'] : ''
        }}"></i>

        <p>
            {{ $item['text'] }}

            @isset($item['label'])
                <span class="badge badge-{{ $item['label_color'] ?? 'primary' }} right">
                    {{ $item['label'] }}
                </span>
            @endisset
        </p>

    </a>

</li>
