<header x-data
    class="sticky top-0 z-20 flex items-center h-[70px] bg-white/80 backdrop-blur-md shadow-md shadow-sky-100/50 w-full transition-all duration-300">

    <div class="flex items-center justify-between w-full px-6">

        <div class="flex items-center gap-4">
            <button @click="$dispatch('toggle-sidebar')"
                class="md:hidden text-gray-500 hover:text-sky-600 transition-colors focus:outline-none">
                <i class="ri-menu-2-line text-2xl"></i>
            </button>
            <h2 class="font-bold text-gray-800 text-lg">{{ $title ?? 'Dashboard' }}</h2>
        </div>



        <div class="relative" x-data="{ open: false }" @click.outside="open = false">

            <button @click="open = !open"
                class="flex items-center gap-3 rounded-xl px-2 py-1.5 hover:bg-sky-50 transition-all duration-200 cursor-pointer border border-transparent focus:outline-none">

                <div
                    class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0 border-2 border-sky-400 shadow-sm shadow-sky-100 bg-sky-100 flex items-center justify-center text-sky-600 font-bold">
                    @if (Auth::user()->avatar_url ?? false)
                        <img alt="Profile" class="w-full h-full object-cover" src="{{ Auth::user()->avatar_url }}">
                    @else
                        {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                    @endif
                </div>

                <div class="hidden md:block text-left">
                    <p class="text-sm font-semibold text-gray-800 leading-tight">
                        {{ Auth::user()->name ?? 'Administrator' }}
                    </p>
                    <p class="text-xs text-sky-500 font-medium capitalize mt-0.5">
                        {{ Auth::user()->role ?? 'Admin' }}
                    </p>
                </div>

                <div class="w-4 h-4 flex items-center justify-center">
                    <i class="ri-arrow-down-s-line text-gray-400 transition-transform duration-300"
                        :class="open ? 'rotate-180' : ''"></i>
                </div>

            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg shadow-sky-100/50 border border-gray-100 py-2 z-50"
                style="display: none;">

                <div class="px-4 py-3 border-b border-gray-50 md:hidden mb-1">
                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'Administrator' }}</p>
                    <p class="text-xs text-sky-500 capitalize">{{ Auth::user()->role ?? 'Admin' }}</p>
                </div>

                <a href="#"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 hover:bg-sky-50 hover:text-sky-600 transition-colors">
                    <i class="ri-user-settings-line text-lg"></i> Profil Saya
                </a>

                <div class="h-px bg-gray-100 my-1"></div>

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <button type="submit" @click.prevent="$root.submit();"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                        <i class="ri-logout-box-r-line text-lg"></i> Keluar
                    </button>
                </form>

            </div>
        </div>

    </div>
</header>
