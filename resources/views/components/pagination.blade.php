@if ($paginator->hasPages())
    <nav class="flex items-center justify-between">
        <div class="text-sm text-on-surface-variant">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </div>
        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-on-surface-variant/50 bg-surface-container rounded-lg">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-on-surface bg-surface-container hover:bg-surface-container-high rounded-lg transition-colors">Previous</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-3 py-1.5 text-sm text-on-surface-variant">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1.5 text-sm text-on-primary bg-primary-container rounded-lg font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-on-surface bg-surface-container hover:bg-surface-container-high rounded-lg transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-on-surface bg-surface-container hover:bg-surface-container-high rounded-lg transition-colors">Next</a>
            @else
                <span class="px-3 py-1.5 text-sm text-on-surface-variant/50 bg-surface-container rounded-lg">Next</span>
            @endif
        </div>
    </nav>
@endif
