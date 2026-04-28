@props(['paginator', 'itemName' => 'data'])

@if ($paginator->hasPages() || $paginator->total() > 0)
    <div
        class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-4 sm:px-6 rounded-2xl shadow-sm transition-all duration-300">


        <div
            class="text-[13px] text-slate-500 dark:text-slate-400 text-center md:text-left w-full md:w-auto order-2 md:order-1">
            Menampilkan
            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $paginator->firstItem() ?? 0 }}</span>
            -
            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $paginator->lastItem() ?? 0 }}</span>
            dari total
            <span class="font-bold text-blue-600 dark:text-blue-500">{{ $paginator->total() }}</span>
            {{ $itemName }}
        </div>


        <div
            class="w-full md:w-auto flex justify-center md:justify-end order-1 md:order-2 overflow-x-auto hide-scrollbar pb-1 md:pb-0">
            <div class="custom-pagination-style">
                {{ $paginator->links(data: ['scrollTo' => false]) }}
            </div>
        </div>

    </div>

    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }


        .custom-pagination-style nav {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .custom-pagination-style nav>div.hidden {
            display: flex !important;

        }

        .custom-pagination-style span.relative.z-0.inline-flex.shadow-sm.rounded-md {
            box-shadow: none !important;
            gap: 0.35rem;

        }


        .custom-pagination-style a,
        .custom-pagination-style span[aria-disabled],
        .custom-pagination-style span[aria-current] {
            border-radius: 0.5rem !important;

            border: 1px solid #e2e8f0 !important;

            margin: 0 !important;
            padding: 0.35rem 0.75rem !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            transition: all 0.2s ease-in-out;
            background-color: #ffffff;
            color: #64748b;
        }


        .dark .custom-pagination-style a,
        .dark .custom-pagination-style span[aria-disabled] {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #94a3b8 !important;
        }


        .custom-pagination-style a:hover {
            background-color: #f8fafc !important;
            color: #2563eb !important;
            border-color: #bfdbfe !important;
        }

        .dark .custom-pagination-style a:hover {
            background-color: #334155 !important;
            color: #60a5fa !important;
            border-color: #475569 !important;
        }


        .custom-pagination-style span[aria-current="page"]>span {
            background-color: #2563eb !important;
            /* Biru solid */
            color: #ffffff !important;
            border-color: #2563eb !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }


        .custom-pagination-style span[aria-disabled="true"] {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f1f5f9 !important;
        }

        .dark .custom-pagination-style span[aria-disabled="true"] {
            background-color: #0f172a !important;
        }


        .custom-pagination-style .rounded-l-md,
        .custom-pagination-style .rounded-r-md {
            border-radius: 0.5rem !important;
        }
    </style>
@endif
