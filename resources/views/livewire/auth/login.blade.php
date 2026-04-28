<div class="min-h-screen flex overflow-hidden bg-white">
    <div
        class="hidden lg:flex lg:w-2/5 relative flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-sky-400 via-sky-500 to-sky-700">
        <div class="absolute rounded-full bg-white opacity-20 animate-bounce"
            style="left: 50%; top: 24%; width: 8px; height: 8px; animation-delay: 3.1s; animation-duration: 6.1s;"></div>
        <div class="absolute rounded-full bg-white opacity-20 animate-bounce"
            style="left: 85%; top: 91%; width: 17px; height: 17px; animation-delay: 2.6s; animation-duration: 5.6s;">
        </div>
        <div class="absolute rounded-full bg-white opacity-20 animate-bounce"
            style="left: 3%; top: 48%; width: 18px; height: 18px; animation-delay: 0.1s; animation-duration: 3.1s;">
        </div>
        <div class="absolute rounded-full bg-white opacity-20 animate-bounce"
            style="left: 53%; top: 87%; width: 15px; height: 15px; animation-delay: 1s; animation-duration: 4s;"></div>
        <div class="absolute rounded-full bg-white opacity-20 animate-bounce"
            style="left: 97%; top: 63%; width: 22px; height: 22px; animation-delay: 0.9s; animation-duration: 3.9s;">
        </div>
        <div class="absolute rounded-full bg-white opacity-20 animate-bounce"
            style="left: 16%; top: 63%; width: 20px; height: 20px; animation-delay: 2.6s; animation-duration: 5.6s;">
        </div>
        <div class="absolute rounded-full bg-white opacity-20 animate-bounce"
            style="left: 7%; top: 41%; width: 19px; height: 19px; animation-delay: 0.9s; animation-duration: 3.9s;">
        </div>
        <div class="absolute rounded-full bg-white opacity-20 animate-bounce"
            style="left: 88%; top: 73%; width: 13px; height: 13px; animation-delay: 0s; animation-duration: 3s;"></div>
        <div class="absolute rounded-full bg-white opacity-20 animate-bounce"
            style="left: 77%; top: 22%; width: 23px; height: 23px; animation-delay: 2.6s; animation-duration: 5.6s;">
        </div>
        <div class="absolute rounded-full bg-white opacity-20 animate-bounce"
            style="left: 32%; top: 22%; width: 17px; height: 17px; animation-delay: 3.7s; animation-duration: 6.7s;">
        </div>

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

            <h2 class="text-white font-bold text-3xl mb-3 drop-shadow">
                Sistem Absensi Digital
            </h2>
            <p class="text-white/80 text-base leading-relaxed">
                Kelola kehadiran siswa dengan mudah
            </p>

            <div class="flex gap-4 justify-center mt-8">
                <div
                    class="bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 text-white text-sm font-medium shadow-sm">
                    <i class="ri-group-line mr-1"></i>847 Siswa
                </div>
                <div
                    class="bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 text-white text-sm font-medium shadow-sm">
                    <i class="ri-building-line mr-1"></i>24 Kelas
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center px-6 py-12 relative">
        <div class="w-full max-w-md relative z-10">

            <div class="flex lg:hidden items-center justify-center mb-8">
                <div
                    class="w-14 h-14 bg-white border border-gray-100 rounded-xl flex items-center justify-center mr-3 shadow-sm p-1">
                    <img src="{{ asset('img/logo.webp') }}" alt="Logo Sekolah" class="w-full h-full object-contain" />
                </div>
                <span class="font-bold text-2xl text-gray-800">AbsensiKu</span>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    Selamat Datang!
                </h1>
                <p class="text-gray-500 text-base">
                    Masuk ke akun Anda untuk melanjutkan
                </p>
            </div>

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="login" class="space-y-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <div
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400">
                            <i class="ri-mail-line text-lg"></i>
                        </div>
                        <input wire:model="email" type="email" placeholder="nama@email.com" required autofocus
                            autocomplete="username"
                            class="w-full pl-11 pr-4 py-3 text-sm border border-gray-200 rounded-xl outline-none transition-all duration-200 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 bg-gray-50 focus:bg-white" />
                    </div>
                    @error('email')
                        <span class="text-red-500 text-xs mt-1.5 block font-medium"><i
                                class="ri-error-warning-line align-middle mr-1"></i>{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <div
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400">
                            <i class="ri-lock-line text-lg"></i>
                        </div>
                        <input wire:model="password" :type="show ? 'text' : 'password'" placeholder="Masukkan password"
                            required autocomplete="current-password"
                            class="w-full pl-11 pr-12 py-3 text-sm border border-gray-200 rounded-xl outline-none transition-all duration-200 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 bg-gray-50 focus:bg-white" />
                        <button type="button" @click="show = !show"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400 hover:text-sky-500 transition-colors focus:outline-none">
                            <i :class="show ? 'ri-eye-off-line' : 'ri-eye-line'" class="text-lg"></i>
                        </button>
                    </div>

                </div>

                @if (Route::has('password.request'))
                    <div class="flex items-center justify-end mt-2">
                        <a href="{{ route('password.request') }}" wire:navigate
                            class="text-sm font-medium text-sky-500 hover:text-sky-600 hover:underline transition-colors">
                            Lupa Password?
                        </a>
                    </div>
                @endif

                @error('auth_failed')
                    <div class="mt-5 p-4 rounded-xl bg-red-50 border border-red-100 flex items-start gap-3">
                        <i class="ri-error-warning-fill text-red-500 text-xl"></i>
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-red-800">Login Gagal</h3>
                            <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p>
                        </div>
                    </div>
                @enderror


                <x-ui.button type="submit" wire:target="login" color="primary" icon="ri-login-box-line" class="w-full">
                    Masuk Sekarang
                </x-ui.button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-8">
                &copy; {{ date('Y') }} Absensi &mdash; SMK Katolik Santa
            </p>
        </div>
    </div>
</div>
