@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation"
        class="flex items-center justify-between mt-6 border-t border-gray-100 pt-5">

        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-gray-400 bg-gray-50 border border-gray-200 cursor-not-allowed rounded-xl">
                    <i class="ri-arrow-left-s-line text-lg mr-1 -ml-1"></i> Kembali
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-indigo-600 bg-white border border-indigo-200 rounded-xl hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm">
                    <i class="ri-arrow-left-s-line text-lg mr-1 -ml-1"></i> Kembali
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-bold text-indigo-600 bg-white border border-indigo-200 rounded-xl hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm">
                    Lanjut <i class="ri-arrow-right-s-line text-lg ml-1 -mr-1"></i>
                </button>
            @else
                <span
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-bold text-gray-400 bg-gray-50 border border-gray-200 cursor-not-allowed rounded-xl">
                    Lanjut <i class="ri-arrow-right-s-line text-lg ml-1 -mr-1"></i>
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">

            <div>
                <p
                    class="text-sm text-gray-500 leading-5 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                    Menampilkan
                    <span class="font-black text-indigo-600">{{ $paginator->firstItem() }}</span>
                    -
                    <span class="font-black text-indigo-600">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-black text-indigo-600">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-xl">

                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span
                                class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-gray-50 border border-gray-200 cursor-not-allowed rounded-l-xl"
                                aria-hidden="true">
                                <i class="ri-arrow-left-s-line text-xl"></i>
                            </span>
                        </span>
                    @else
                        <button wire:click="previousPage" rel="prev"
                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-200 rounded-l-xl hover:bg-indigo-50 hover:text-indigo-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                            aria-label="{{ __('pagination.previous') }}">
                            <i class="ri-arrow-left-s-line text-xl"></i>
                        </button>
                    @endif

                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span
                                    class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-400 bg-white border border-gray-200 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span
                                            class="relative z-10 inline-flex items-center px-4 py-2 -ml-px text-sm font-black text-white bg-indigo-600 border border-indigo-600 cursor-default shadow-sm">{{ $page }}</span>
                                    </span>
                                @else
                                    <button wire:click="gotoPage({{ $page }})"
                                        class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:bg-indigo-50 hover:text-indigo-700 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                                        aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <button wire:click="nextPage" rel="next"
                            class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-200 rounded-r-xl hover:bg-indigo-50 hover:text-indigo-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                            aria-label="{{ __('pagination.next') }}">
                            <i class="ri-arrow-right-s-line text-xl"></i>
                        </button>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span
                                class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-50 border border-gray-200 cursor-not-allowed rounded-r-xl"
                                aria-hidden="true">
                                <i class="ri-arrow-right-s-line text-xl"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
