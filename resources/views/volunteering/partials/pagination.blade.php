@if ($paginator->hasPages())
    <ul class="custom pagination pagination">
        @if ($paginator->onFirstPage())
            <li class="disabled text-secondary  page-item"><span class="font-weight-bold">&lt;</span></li>
        @else
            <li class="page-item text-primary font-weight-bold" ><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&lt;</a></li>
        @endif
        @if($paginator->currentPage() > 3)
            <li class="hidden-xs page-item"><a href="{{ $paginator->url(1) }}">1</a></li>
        @endif
        @if($paginator->currentPage() > 4)
            <li class="page-item" ><span>...</span></li>
        @endif
        @foreach(range(1, $paginator->lastPage()) as $i)
            @if($i >= $paginator->currentPage() - 2 && $i <= $paginator->currentPage() + 2)
                @if ($i == $paginator->currentPage())
                    <li class="active page-item"><span>{{ $i }}</span></li>
                @else
                    <li class="page-item"><a href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
                @endif
            @endif
        @endforeach
        @if($paginator->currentPage() < $paginator->lastPage() - 3)
            <li class="page-item"><span>...</span></li>
        @endif
        @if($paginator->currentPage() < $paginator->lastPage() - 2)
            <li class="hidden-xs page-item"><a href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a></li>
        @endif
        @if ($paginator->hasMorePages())
            <li class="page-item text-primary font-weight-bold"><a href="{{ $paginator->nextPageUrl() }}" rel="next">&gt;</a></li>
        @else
            <li class="disabled page-item text-secondary font-weight-bold"><span>&gt;</span></li>
        @endif
    </ul>
@endif
