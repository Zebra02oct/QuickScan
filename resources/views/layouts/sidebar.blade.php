<div x-data="{ sidebarOpen: false }" @toggle-sidebar.window="sidebarOpen = !sidebarOpen">

    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-slate-900/50  md:hidden" style="display: none;">
    </div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-[260px] bg-white border-r border-gray-100 flex flex-col transition-transform duration-300 transform md:translate-x-0 shadow-sm">

        <div class="flex items-center justify-center h-20 border-b border-gray-50 px-6">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 w-full group">
                <div class="w-10 h-10 flex-shrink-0">
                    <img src="{{ asset('img/logo.webp') }}" alt="Logo Absensi"
                        class="w-full h-full object-contain drop-shadow-sm transition-all group-hover:animate-bounce-slow" />
                </div>

                <div class="flex flex-col">
                    <span
                        class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-sky-600 to-sky-800 leading-none">
                        Absensi
                    </span>
                    <span class="text-[11px] font-semibold text-gray-400 mt-0.5 tracking-wide">
                        SMK Katolik Santa
                    </span>
                </div>
            </a>
        </div>

        <div class="flex-1 overflow-y-auto py-6 px-4 scrollbar-hide">
            <ul class="space-y-1.5">

                <li>
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-sky-50 text-sky-600 font-semibold' : 'text-gray-500 hover:bg-sky-50/60 hover:text-sky-600 hover:translate-x-1' }}">
                        <i
                            class="ri-dashboard-3-{{ request()->routeIs('dashboard') ? 'fill' : 'line' }} text-xl group-hover:animate-jiggle"></i>
                        <span class="text-sm">Dashboard</span>
                    </a>
                </li>

                @if (Auth::user()->role === 'admin')
                    <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider mt-6 mb-3">Manajemen
                        Pengguna
                    </p>

                    <li x-data="{
                        open: {{ request()->is('admin/manajemenData-*') ? 'true' : 'false' }}
                    }">

                        <button @click="open = !open"
                            class="group w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 {{ request()->is('admin/manajemenData-*') ? 'bg-sky-50 text-sky-600 font-semibold' : 'text-gray-500 hover:bg-sky-50/60 hover:text-sky-600 hover:translate-x-1' }}">
                            <div class="flex items-center gap-3">
                                <i
                                    class="ri-group-{{ request()->is('admin/manajemenData-*') ? 'fill' : 'line' }} text-xl group-hover:animate-jiggle"></i>
                                <span class="text-sm">User</span>
                            </div>
                            <i class="ri-arrow-down-s-line text-lg transition-transform duration-300"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>

                        <ul x-show="open" x-collapse.duration.300ms class="mt-1 space-y-1 px-3">

                            <li>
                                <a href="{{ route('admin.siswa.index') }}" wire:navigate
                                    class="group flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-300 {{ request()->routeIs('admin.siswa.index') ? 'text-sky-600 font-semibold bg-sky-50/80' : 'text-gray-500 hover:text-sky-600 hover:bg-sky-50/50 hover:translate-x-1' }}">
                                    <div
                                        class="w-1.5 h-1.5 rounded-full transition-transform duration-300 group-hover:scale-150 {{ request()->routeIs('admin.siswa.index') ? 'bg-sky-500' : 'bg-gray-300 group-hover:bg-sky-400' }}">
                                    </div>
                                    <span class="text-sm">Data Siswa</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.guru.index') }}" wire:navigate
                                    class="group flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-300 {{ request()->routeIs('admin.guru.index') ? 'text-sky-600 font-semibold bg-sky-50/80' : 'text-gray-500 hover:text-sky-600 hover:bg-sky-50/50 hover:translate-x-1' }}">
                                    <div
                                        class="w-1.5 h-1.5 rounded-full transition-transform duration-300 group-hover:scale-150 {{ request()->routeIs('admin.guru.index') ? 'bg-sky-500' : 'bg-gray-300 group-hover:bg-sky-400' }}">
                                    </div>
                                    <span class="text-sm">Data Guru</span>
                                </a>
                            </li>

                        </ul>
                    </li>

                    <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider mt-6 mb-3">Data Master
                    </p>

                    <li>
                        <a href="{{ route('admin.kelas.index') }}" wire:navigate
                            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 
        {{ request()->routeIs('admin.kelas.*')
            ? 'bg-sky-50 text-sky-600 font-semibold'
            : 'text-gray-500 hover:bg-sky-50/60 hover:text-sky-600 hover:translate-x-1' }}">

                            <i
                                class="ri-building-{{ request()->routeIs('admin.kelas.*') ? 'fill' : 'line' }} 
           text-xl group-hover:animate-jiggle"></i>

                            <span class="text-sm">Manajemen Kelas</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.mapel.index') }}" wire:navigate
                            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 
        {{ request()->routeIs('admin.mapel.*')
            ? 'bg-sky-50 text-sky-600 font-semibold'
            : 'text-gray-500 hover:bg-sky-50/60 hover:text-sky-600 hover:translate-x-1' }}">

                            <i
                                class="ri-book-open-{{ request()->routeIs('admin.mapel.*') ? 'fill' : 'line' }} 
           text-xl group-hover:animate-jiggle"></i>

                            <span class="text-sm">Manajemen Mapel</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.guruMapel.index') }}" wire:navigate
                            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 
        {{ request()->routeIs('admin.guruMapel.index')
            ? 'bg-sky-50 text-sky-600 font-semibold'
            : 'text-gray-500 hover:bg-sky-50/60 hover:text-sky-600 hover:translate-x-1' }}">

                            <i
                                class="ri-calendar-todo-{{ request()->routeIs('admin.guruMapel.index') ? 'fill' : 'line' }} 
           text-xl group-hover:animate-jiggle"></i>

                            <span class="text-sm">Manajemen Guru Mapel</span>
                        </a>
                    </li>

                    <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider mt-6 mb-3">Data Absensi
                    </p>

                    <li>
                        <a href="{{ route('admin.manajemenAbsensi.index') }}" wire:navigate
                            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 
        {{ request()->routeIs('admin.manajemenAbsensi.index')
            ? 'bg-sky-50 text-sky-600 font-semibold'
            : 'text-gray-500 hover:bg-sky-50/60 hover:text-sky-600 hover:translate-x-1' }}">

                            <i
                                class="ri-folder-user-{{ request()->routeIs('admin.manajemenAbsensi.index') ? 'fill' : 'line' }} 
           text-xl group-hover:animate-jiggle"></i>

                            <span class="text-sm">Manajemen Absensi</span>
                        </a>
                    </li>
            </ul>
        </div>
        @endif

        @if (Auth::user()->role === 'guru')
            <li>
                <a href="{{ route('guru.absen.buka') }}" wire:navigate
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 
        {{ request()->routeIs('guru.absen.buka')
            ? 'bg-sky-50 text-sky-600 font-semibold'
            : 'text-gray-500 hover:bg-sky-50/60 hover:text-sky-600 hover:translate-x-1' }}">

                    <i
                        class="ri-slideshow-{{ request()->routeIs('guru.absen.buka') ? 'fill' : 'line' }} 
           text-xl group-hover:animate-jiggle"></i>

                    <span class="text-sm">Buka Sesi Absensi</span>
                </a>
            </li>
            <li>
                <a href="{{ route('guru.manajemenAbsensi') }}" wire:navigate
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 
        {{ request()->routeIs('guru.manajemenAbsensi')
            ? 'bg-sky-50 text-sky-600 font-semibold'
            : 'text-gray-500 hover:bg-sky-50/60 hover:text-sky-600 hover:translate-x-1' }}">

                    <i
                        class="ri-folder-user-{{ request()->routeIs('guru.manajemenAbsensi') ? 'fill' : 'line' }} 
           text-xl group-hover:animate-jiggle"></i>

                    <span class="text-sm">Manajemen Absensi</span>
                </a>
            </li>
        @endif

        @if (Auth::user()->role === 'siswa')
            <li>
                <a href="{{ route('siswa.absen.scan') }}" wire:navigate
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 
        {{ request()->routeIs('siswa.absen.scan')
            ? 'bg-sky-50 text-sky-600 font-semibold'
            : 'text-gray-500 hover:bg-sky-50/60 hover:text-sky-600 hover:translate-x-1' }}">

                    <i
                        class="ri-slideshow-{{ request()->routeIs('siswa.absen.scan') ? 'fill' : 'line' }} 
           text-xl group-hover:animate-jiggle"></i>

                    <span class="text-sm">Absensi</span>
                </a>
            </li>
            <li>
                <a href="{{ route('siswa.riwayatKehadiran') }}" wire:navigate
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 
        {{ request()->routeIs('siswa.riwayatKehadiran')
            ? 'bg-sky-50 text-sky-600 font-semibold'
            : 'text-gray-500 hover:bg-sky-50/60 hover:text-sky-600 hover:translate-x-1' }}">

                    <i
                        class="ri-history-{{ request()->routeIs('siswa.riwayatKehadiran') ? 'fill' : 'line' }} 
           text-xl group-hover:animate-jiggle"></i>

                    <span class="text-sm">Riwayat Kehadiran</span>
                </a>
            </li>
        @endif

        <div class="p-4 border-t border-sky-100">
            <form id="logout-form-sidebar" method="POST" action="{{ route('logout') }}" class="hidden">
                @csrf
            </form>

            <button type="button" onclick="confirmLogout()"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-sky-100 border border-sky-200 hover:bg-sky-200 hover:shadow-md transition-all duration-300 group focus:outline-none">

                <div
                    class="w-10 h-10 flex-shrink-0 rounded-full overflow-hidden border-2 border-white shadow-sm bg-sky-400 flex items-center justify-center text-white font-bold text-sm">
                    @if (Auth::user()->avatar_url ?? false)
                        <img alt="Profile" class="w-full h-full object-cover" src="{{ Auth::user()->avatar_url }}">
                    @else
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    @endif
                </div>

                <div class="flex-1 min-w-0 text-left">
                    <p class="text-sm font-bold text-sky-800 truncate group-hover:text-sky-900">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </p>
                    <p
                        class="text-xs font-medium text-sky-600 truncate flex items-center gap-1 group-hover:text-red-500 transition-colors">
                        <i class="ri-logout-circle-r-line group-hover:animate-jiggle"></i> Logout
                    </p>
                </div>

                <div
                    class="w-7 h-7 rounded-full bg-white/60 flex items-center justify-center text-sky-600 group-hover:text-red-600 group-hover:bg-red-50 transition-colors">
                    <i class="ri-logout-box-r-line text-lg group-hover:animate-jiggle"></i>
                </div>

            </button>
        </div>

    </aside>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmLogout() {
                Swal.fire({
                    title: 'Yakin ingin keluar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0ea5e9',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: '<i class="ri-check-line"></i> Ya, Keluar!',
                    cancelButtonText: '<i class="ri-close-line"></i> Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl',
                        title: 'text-gray-800 font-bold',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logout-form-sidebar').submit();
                    }
                })
            }
        </script>
    @endpush
</div>
