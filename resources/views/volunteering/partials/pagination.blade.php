@if ($paginator->hasPages())
    <ul class="custom pagination pagination">
        @if ($paginator->onFirstPage())
            <li class="disabled text-secondary  page-item"><span class="font-weight-bold">&lt;</span></li>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page"><li class="page-item text-primary font-weight-bold" >&lt;</li></a>
        @endif
        @if($paginator->currentPage() > 3)
            <a href="{{ $paginator->url(1) }}" aria-label="{{ 'Page 1 of ' . $paginator->lastPage() }}"><li class="hidden-xs page-item">1</li></a>
        @endif
        @if($paginator->currentPage() > 4)
            <li class="page-item" ><span>...</span></li>
        @endif
        @foreach(range(1, $paginator->lastPage()) as $i)
            @if($i >= $paginator->currentPage() - 2 && $i <= $paginator->currentPage() + 2)
                @if ($i == $paginator->currentPage())
                    <li class="active page-item" aria-label="{{ 'Page ' . $i . ' of ' . $paginator->lastPage() }}"><span>{{ $i }}</span></li>
                @else
                    <a href="{{ $paginator->url($i) }}" aria-label="{{ 'Page ' . $i . ' of ' . $paginator->lastPage() }}"><li class="page-item">{{ $i }}</li></a>
                @endif
            @endif
        @endforeach
        @if($paginator->currentPage() < $paginator->lastPage() - 3)
            <li class="page-item" aria-label="{{ 'Page ' . $paginator->currentPage() . ' of ' . $paginator->lastPage() }}"><span>...</span></li>
        @endif
        @if($paginator->currentPage() < $paginator->lastPage() - 2)
            <a href="{{ $paginator->url($paginator->lastPage()) }}" aria-label="Last page"><li class="hidden-xs page-item">{{ $paginator->lastPage() }}</li></a>
        @endif
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page"><li class="page-item text-primary font-weight-bold">&gt;</li></a>
        @else
            <li class="disabled page-item text-secondary font-weight-bold"><span>&gt;</span></li>
        @endif
    </ul>
@endif
