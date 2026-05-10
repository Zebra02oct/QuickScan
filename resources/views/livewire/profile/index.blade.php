<div class="max-w-5xl mx-auto space-y-6 animate-fade-in-down">

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col md:flex-row items-center gap-6">

        <div class="relative shrink-0">
            @if ($user->user_photo)
                <img src="{{ asset('storage/' . $user->user_photo) }}"
                    class="w-28 h-28 rounded-full object-cover border-4 border-sky-100">
            @else
                <div
                    class="w-28 h-28 rounded-full bg-slate-200 flex items-center justify-center border-4 border-sky-100">
                    <i class="ri-user-smile-fill text-5xl text-slate-400"></i>
                </div>
            @endif

            <div wire:loading wire:target="foto_baru"
                class="absolute inset-0 bg-white/70 rounded-full flex items-center justify-center">
                <i class="ri-loader-4-line text-2xl text-sky-500 animate-spin"></i>
            </div>
        </div>

        <div class="flex-1 text-center md:text-left">
            <h2 class="text-2xl font-bold text-slate-800">{{ $user->name }}</h2>
            <p class="text-slate-500 font-medium">{{ $user->email }}</p>

            <div
                class="mt-2 inline-block px-3 py-1 bg-sky-100 text-sky-700 font-semibold text-xs rounded-full uppercase tracking-wider">
                Role: {{ $user->role ?? 'Siswa' }}
            </div>
        </div>

        <div class="w-full md:w-auto">
            <form wire:submit="updateFoto" class="flex flex-col gap-2">
                <input type="file" wire:model="foto_baru" id="foto"
                    class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 cursor-pointer w-full"
                    accept="image/*">
                @error('foto_baru')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror

                <button type="submit"
                    class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium py-2 px-4 rounded-full transition-colors disabled:opacity-50"
                    wire:loading.attr="disabled" wire:target="foto_baru">
                    <i class="ri-upload-cloud-2-line mr-1"></i> Simpan Foto
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2"><i
                    class="ri-id-card-fill text-indigo-500 mr-2"></i>Informasi Pribadi</h3>

            <ul class="space-y-4">
                <li>
                    <span class="block text-xs font-semibold text-slate-400 uppercase">Nama Lengkap</span>
                    <span class="text-slate-800 font-medium">{{ $user->name }}</span>
                </li>

                @if ($user->siswa)
                    <li>
                        <span class="block text-xs font-semibold text-slate-400 uppercase">NISN</span>
                        <span class="text-slate-800 font-medium">{{ $user->siswa->nisn ?? '-' }}</span>
                    </li>
                    <li>
                        <span class="block text-xs font-semibold text-slate-400 uppercase">Kelas Saat Ini</span>
                        <span
                            class="text-slate-800 font-medium">{{ $user->siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</span>
                    </li>
                    <li>
                        <span class="block text-xs font-semibold text-slate-400 uppercase">Jenis Kelamin</span>
                        <span
                            class="text-slate-800 font-medium">{{ $user->siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </li>
                @elseif($user->guru)
                    <li>
                        <span class="block text-xs font-semibold text-slate-400 uppercase">NIP</span>
                        <span class="text-slate-800 font-medium">{{ $user->guru->nip ?? '-' }}</span>
                    </li>
                    <li>
                        <span class="block text-xs font-semibold text-slate-400 uppercase">Jenis Kelamin</span>
                        <span
                            class="text-slate-800 font-medium">{{ $user->guru->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </li>
                @else
                    <li>
                        <span class="block text-xs font-semibold text-slate-400 uppercase">Tipe Akun</span>
                        <span class="text-slate-800 font-medium">Administrator</span>
                    </li>
                @endif
            </ul>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2"><i
                    class="ri-lock-password-fill text-rose-500 mr-2"></i>Ganti Password</h3>

            <form wire:submit="updatePassword" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password Lama</label>
                    <input type="password" wire:model="password_lama"
                        class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                    @error('password_lama')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
                    <input type="password" wire:model="password_baru"
                        class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                    @error('password_baru')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" wire:model="password_baru_confirmation"
                        class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-2.5 px-4 rounded-xl transition-colors">
                        <i class="ri-save-3-line mr-1"></i> Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
