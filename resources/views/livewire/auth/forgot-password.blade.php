<div class="min-h-screen flex overflow-hidden bg-white">
    <div
        class="hidden lg:flex lg:w-2/5 relative flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-sky-400 via-sky-500 to-sky-700">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <div class="absolute top-10 left-10 w-32 h-32 border-4 border-white rounded-full"></div>
            <div class="absolute bottom-20 right-10 w-48 h-48 border-4 border-white rounded-full"></div>
            <div class="absolute top-1/2 left-1/4 w-20 h-20 border-4 border-white rotate-45"></div>
        </div>
        <div class="relative z-10 text-center px-10">
            <div class="w-56 h-56 mx-auto mb-8 animate-float">
                <img alt="School illustration" class="w-full h-full object-contain drop-shadow-2xl"
                    src="{{ asset('img/logo.webp') }}" />
            </div>
            <h2 class="text-white font-bold text-3xl mb-3 drop-shadow">Sistem Absensi Digital</h2>
            <p class="text-white/80 text-base leading-relaxed">Pusat Bantuan Akun</p>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center px-6 py-12 relative">
        <div class="w-full max-w-md relative z-10">

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Lupa Password?</h1>
                <p class="text-gray-500 text-sm">
                    Masukkan alamat email Anda dan sistem akan mengirim tautan untuk mengatur ulang
                    password.
                </p>
            </div>

            @if ($status)
                <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-100 flex items-start gap-3">
                    <i class="ri-checkbox-circle-fill text-green-500 text-xl"></i>
                    <p class="text-sm text-green-700 mt-0.5">{{ $status }}</p>
                </div>
            @endif

            <form wire:submit="sendPasswordResetLink" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email Terdaftar</label>
                    <div class="relative">
                        <div
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400">
                            <i class="ri-mail-line text-lg"></i>
                        </div>
                        <input wire:model="email" type="email" placeholder="nama@email.com" required autofocus
                            class="w-full pl-11 pr-4 py-3 text-sm border border-gray-200 rounded-xl outline-none transition-all duration-200 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 bg-gray-50 focus:bg-white" />
                    </div>
                    @error('email')
                        <span class="text-red-500 text-xs mt-1.5 block font-medium"><i
                                class="ri-error-warning-line align-middle mr-1"></i>{{ $message }}</span>
                    @enderror
                </div>

                <x-ui.button type="submit" loadingText="Mengirim" wire:target="sendPasswordResetLink" color="primary"
                    icon="ri-send-plane-fill" class="w-full">
                    Kirim
                </x-ui.button>
            </form>

            <div class="mt-8 text-center">
                <a href="{{ route('login') }}" wire:navigate
                    class="text-sm font-medium text-sky-500 hover:text-sky-600 hover:underline inline-flex items-center gap-1">
                    <i class="ri-arrow-left-line"></i> Kembali ke halaman Login
                </a>
            </div>
        </div>
    </div>
</div>
