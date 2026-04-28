<div class="min-h-screen flex overflow-hidden bg-white">
    <div
        class="hidden lg:flex lg:w-2/5 relative flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-sky-400 via-sky-500 to-sky-700">
        <div class="relative z-10 text-center px-10">
            <div class="w-56 h-56 mx-auto mb-8 animate-float">
                <img alt="School illustration" class="w-full h-full object-contain drop-shadow-2xl"
                    src="{{ asset('img/logo.webp') }}" />
            </div>
            <h2 class="text-white font-bold text-3xl mb-3 drop-shadow">Keamanan Akun</h2>
            <p class="text-white/80 text-base leading-relaxed">Buat password yang kuat dan mudah diingat</p>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center px-6 py-12 relative">
        <div class="w-full max-w-md relative z-10">

            @if ($isExpired)
                <div class="text-center">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-red-50 rounded-full mb-6 text-red-500">
                        <i class="ri-history-line text-4xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Tautan Kedaluwarsa</h1>
                    <p class="text-gray-500 mb-8 leading-relaxed">
                        Maaf, tautan ini sudah tidak berlaku, sudah pernah digunakan, atau masa berlakunya telah habis.
                        Silakan ajukan permintaan reset password yang baru.
                    </p>
                    <a href="{{ route('password.request') }}" wire:navigate
                        class="inline-block w-full py-3.5 rounded-xl font-bold text-white transition-all duration-200 hover:brightness-110 shadow-lg bg-gradient-to-r from-sky-400 to-sky-600">
                        <i class="ri-refresh-line mr-1"></i> Minta Tautan Baru
                    </a>
                </div>
            @else
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Buat Password Baru</h1>
                    <p class="text-gray-500 text-sm">Silakan buat password baru untuk akun Anda.</p>
                </div>

                <form wire:submit="resetPassword" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email</label>
                        <div class="relative">
                            <div
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400">
                                <i class="ri-mail-line text-lg"></i>
                            </div>
                            <input wire:model="email" type="email" required readonly
                                class="w-full pl-11 pr-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 text-gray-500 cursor-not-allowed" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                        <div class="relative" x-data="{ show: false }">
                            <div
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400">
                                <i class="ri-lock-line text-lg"></i>
                            </div>

                            <input wire:model="password" :type="show ? 'text' : 'password'"
                                placeholder="Minimal 8 karakter" required autofocus
                                class="w-full pl-11 pr-12 py-3 text-sm border border-gray-200 rounded-xl outline-none transition-all duration-200 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 bg-gray-50 focus:bg-white" />

                            <button type="button" @click="show = !show"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400 hover:text-sky-500 transition-colors focus:outline-none">
                                <i :class="show ? 'ri-eye-off-line' : 'ri-eye-line'" class="text-lg"></i>
                            </button>
                        </div>

                        @error('password')
                            <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                        <div class="relative" x-data="{ show: false }">
                            <div
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400">
                                <i class="ri-shield-check-line text-lg"></i>
                            </div>
                            <input wire:model="password_confirmation" :type="show ? 'text' : 'password'"
                                placeholder="Ulangi password baru" required
                                class="w-full pl-11 pr-12 py-3 text-sm border border-gray-200 rounded-xl outline-none transition-all duration-200 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 bg-gray-50 focus:bg-white" />
                        </div>
                    </div>



                    <x-ui.button type="submit" wire:target="resetPassword" color="primary" icon="ri-lock-2-line"
                        class="w-full">
                        Reset
                    </x-ui.button>
                </form>
            @endif

            <p class="text-center text-xs text-gray-400 mt-8">
                &copy; {{ date('Y') }} AbsensiKu &mdash; SMK Katolik Santa
            </p>
        </div>
    </div>
</div>
