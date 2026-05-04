<div>
    <x-modal-wrapper :title="$siswa_id ? 'Edit Data Siswa' : 'Tambah Siswa Baru'" :icon="$siswa_id ? 'ri-edit-box-line' : 'ri-user-add-line'" :iconColor="$siswa_id ? 'text-amber-500' : 'text-sky-500'" maxWidth="2xl"
        @open-create-data-siswa.window="
            isLoading = true; 
            openModal();
            $wire.loadData().then(() => isLoading = false);
        "
        @open-edit-data-siswa.window="
            isLoading = true; 
            openModal();
            $wire.loadData($event.detail.id).then(() => isLoading = false);
        ">

        <form id="formSaveSiswa"
            @submit.prevent="$dispatch('is-saving', true); $wire.save().finally(() => $dispatch('is-saving', false))">

            <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar flex flex-col gap-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nama" wire:model.live="nama" placeholder="Masukkan nama siswa"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors">
                        @error('nama')
                            <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="nisn" class="block text-sm font-medium text-gray-700 mb-1">
                            NISN <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nisn" wire:model.live="nisn"
                            placeholder="Nomor Induk Siswa Nasional"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors">
                        @error('nisn')
                            <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" wire:model.live="email" placeholder="siswa@sekolah.com"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors">
                        @error('email')
                            <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                            <span class="text-xs text-gray-400 font-normal ml-1">
                                {{ $siswa_id ? '(Kosongkan jika tidak ganti)' : '(Kosongkan = Default NISN)' }}
                            </span>
                        </label>
                        <input type="password" id="password" wire:model.live="password"
                            placeholder="{{ $siswa_id ? '••••••••' : 'Buat password...' }}"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors">
                        @error('password')
                            <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <select id="jenis_kelamin" wire:model.live="jenis_kelamin"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors py-2.5">
                            <option value=""> Pilih</option>
                            <option value="L">Laki - Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">
                            Kelas Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <select id="kelas" wire:model="kelas"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors py-2.5">
                            <option value="">Pilih Kelas</option>

                            @foreach ($this->listKelas as $kls)
                                <option value="{{ $kls->id }}" @class(['text-red-500' => !$kls->is_active])
                                    {{ $kelas == $kls->id ? 'selected' : '' }}>

                                    {{ $kls->nama_kelas }}
                                    {{ !$kls->is_active ? '(Nonaktif)' : '' }}

                                </option>
                            @endforeach
                        </select>
                        @error('kelas')
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status Akun <span class="text-rose-500">*</span>
                    </label>
                    <select id="status" wire:model.live="status"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors py-2.5">
                        <option value="">Pilih Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="lulus">Lulus</option>
                        <option value="nonaktif">Nonaktif/Keluar</option>
                        <option value="pindah">Pindah</option>
                    </select>
                    @error('status')
                        <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <x-slot name="footer">
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 w-full">
                    <x-ui.button @click="closeModal();" class="w-full sm:w-auto" color="ghost">
                        Batal
                    </x-ui.button>
                    <x-ui.button type="submit" form="formSaveSiswa" color="primary" icon="ri-save-3-line"
                        class="w-full sm:w-auto" wire:target="save">
                        {{ $siswa_id ? 'Simpan Perubahan' : 'Tambahkan Siswa' }}
                    </x-ui.button>
                </div>
            </x-slot>

        </form>
    </x-modal-wrapper>
</div>
