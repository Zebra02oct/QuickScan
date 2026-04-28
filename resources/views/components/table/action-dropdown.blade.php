<div x-data="{
    open: false,
    updatePos() {
        if (!this.open) return;

        let btn = this.$refs.btn.getBoundingClientRect();
        let menu = this.$refs.menu;


        let menuWidth = menu.offsetWidth || 160;
        let menuHeight = menu.offsetHeight || 140;


        let leftPos = btn.right - menuWidth;
        if (leftPos < 10) leftPos = 10;

        menu.style.left = `${leftPos}px`;


        if (btn.bottom + menuHeight > window.innerHeight) {
            menu.style.top = `${btn.top - menuHeight - 4}px`;
            menu.style.transformOrigin = 'bottom right';
        } else {
            menu.style.top = `${btn.bottom + 4}px`;
            menu.style.transformOrigin = 'top right';
        }
    }
}" @keydown.escape.window="open = false" @scroll.window.capture="open = false"
    @resize.window="open = false"
    @click.window="if (open && !$refs.btn.contains($event.target) && (!$refs.menu || !$refs.menu.contains($event.target))) { open = false }"
    class="relative inline-block text-left">


    <button x-ref="btn" @click="open = !open; if(open) $nextTick(() => updatePos())" type="button"
        class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors focus:outline-none"
        :class="open ? 'bg-slate-200 dark:bg-slate-700' : ''">
        <i class="bx bx-dots-vertical-rounded text-lg sm:text-xl"></i>
    </button>


    <template x-teleport="body">
        <div x-ref="menu" x-show="open" x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95" style="display: none;"
            class="fixed w-40 sm:w-48 bg-white dark:bg-slate-800 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.15)] border border-slate-200/60 dark:border-slate-700/60 p-1 sm:p-1.5 z-[99999] text-left">

            {{ $slot }}

        </div>
    </template>
</div>
