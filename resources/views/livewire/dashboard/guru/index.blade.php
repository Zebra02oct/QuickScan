<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 mt-6">

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.500ms
                class="p-4 mb-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3 text-rose-700">
                    <i class="ri-error-warning-fill text-2xl"></i>
                    <p class="text-sm font-bold">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-rose-400 hover:text-rose-600 transition-colors">
                    <i class="ri-close-line text-xl font-bold"></i>
                </button>
            </div>
        @endif

        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.500ms
                class="p-4 mb-4 bg-green-50 border border-green-200 rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3 text-green-700">
                    <i class="ri-checkbox-circle-fill text-2xl"></i>
                    <p class="text-sm font-bold">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-400 hover:text-green-600 transition-colors">
                    <i class="ri-close-line text-xl font-bold"></i>
                </button>
            </div>
        @endif

    </div>
    <div
        class="bg-white rounded-2xl p-6 shadow-sm border border-sky-100 flex items-center justify-between overflow-hidden relative">
        <div class="relative z-10">
            <h3 class="text-2xl font-bold text-gray-800 mb-1">Selamat Datang, {{ Auth::user()->name }}! 👋</h3>
            <p class="text-gray-500">Pantau aktivitas absensi dan kelola data master dari sini.</p>
        </div>

        <div
            class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-sky-50 to-transparent pointer-events-none">
        </div>
        <i
            class="ri-dashboard-2-line absolute -right-6 -bottom-6 text-9xl text-sky-50 opacity-50 rotate-12 pointer-events-none"></i>
    </div>

</div>
