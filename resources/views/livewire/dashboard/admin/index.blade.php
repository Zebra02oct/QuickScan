<div>
    <div
        class="bg-white rounded-2xl p-6 shadow-sm border border-sky-100 flex items-center justify-between overflow-hidden relative">
        <div class="relative z-10">
            <h3 class="text-2xl font-bold text-gray-800 mb-1">Selamat Datang, {{ Auth::user()->name }}! 👋</h3>
            <p class="text-gray-500">Pantau aktivitas absensi dan kelola data master dari sini.</p>
        </div>

        <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-sky-50 to-transparent pointer-events-none">
        </div>
        <i
            class="ri-dashboard-2-line absolute -right-6 -bottom-6 text-9xl text-sky-50 opacity-50 rotate-12 pointer-events-none"></i>
    </div>

</div>
