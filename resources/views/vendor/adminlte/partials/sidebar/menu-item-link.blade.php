<li @if(isset($item['id'])) id="{{ $item['id'] }}" @endif class="nav-item">

    <a class="nav-link py-3 {{ $item['class'] }} @if(isset($item['shift'])) {{ $item['shift'] }} @endif"
       href="{{ $item['href'] }}" @if(isset($item['target'])) target="{{ $item['target'] }}" @endif
       {!! $item['data-compiled'] ?? '' !!}>

       @if (str_contains($item['icon'], ' far '))
            <i class="{{ $item['icon'] ?? 'far fa-fw fa-circle' }} {{
                isset($item['icon_color']) ? 'text-'.$item['icon_color'] : ''
            }}"></i> 
        @else
            <i>
            <svg class="icon mr-2" style="width:24px; height: 24px">
                <use xlink:href="{{asset('img/icons/sprite.svg')}}#sprite-{{$item['icon'] ?? 'home'}}"></use>
            </svg>
            </i>
        @endif
        <p style="vertical-align:middle">
            {{ $item['text'] }}

            @if(isset($item['label']))
                <span class="badge badge-{{ $item['label_color'] ?? 'primary' }} right">
                    {{ $item['label'] }}
                </span>
            @endif
        </p>

    </a>

</li>