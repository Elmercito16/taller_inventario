@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-sm text-gray-500">
            @if ($paginator->firstItem())
                Mostrando <span class="font-medium text-gray-900">{{ $paginator->firstItem() }}</span>
                a <span class="font-medium text-gray-900">{{ $paginator->lastItem() }}</span>
                @if (method_exists($paginator, 'total'))
                    de <span class="font-medium text-gray-900">{{ $paginator->total() }}</span>
                @endif
            @else
                {{ $paginator->count() }} resultados
            @endif
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="btn-secondary btn-sm opacity-50 cursor-not-allowed" aria-disabled="true">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-secondary btn-sm">Anterior</a>
            @endif

            @if (isset($elements))
                <div class="hidden md:flex items-center gap-1">
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="px-2 text-sm text-gray-400">{{ $element }}</span>
                        @endif
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-lg bg-primary-600 px-2 text-xs font-medium text-white">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-lg px-2 text-xs font-medium text-gray-600 hover:bg-gray-100">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn-secondary btn-sm">Siguiente</a>
            @else
                <span class="btn-secondary btn-sm opacity-50 cursor-not-allowed" aria-disabled="true">Siguiente</span>
            @endif
        </div>
    </nav>
@endif
