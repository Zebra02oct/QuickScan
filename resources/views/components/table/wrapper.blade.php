<div
    class="overflow-hidden rounded-xl sm:rounded-[1.25rem] border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm sm:shadow-md mt-3 sm:mt-4">
    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600">
        <table class="w-full text-left text-sm whitespace-nowrap align-middle">
            @if (isset($header))
                <thead
                    class="bg-slate-50/80 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-[10px] sm:text-xs uppercase tracking-wider border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        {{ $header }}
                    </tr>
                </thead>
            @endif
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-300">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
