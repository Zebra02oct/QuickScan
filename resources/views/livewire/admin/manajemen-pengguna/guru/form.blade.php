<div>
    <x-modal-wrapper :title="$guru_id ? 'Edit Data Guru' : 'Tambah Guru Baru'" :icon="$guru_id ? 'ri-edit-box-line' : 'ri-user-add-line'" :iconColor="$guru_id ? 'text-amber-500' : 'text-sky-500'" maxWidth="2xl"
        @open-create-data-guru.window="
            isLoading = true; 
            openModal();
            $wire.loadData().then(() => isLoading = false);
        "
        @open-edit-data-guru.window="
            isLoading = true; 
            openModal();
            $wire.loadData($event.detail.id).then(() => isLoading = false);
        ">

        <form id="formSaveGuru"
            @submit.prevent="$dispatch('is-saving', true); $wire.save().finally(() => $dispatch('is-saving', false))">

            <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar flex flex-col gap-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nama" wire:model.live="nama" placeholder="Masukkan nama guru"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors">
                        @error('nama')
                            <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">
                            NIP <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nip" wire:model.live="nip" placeholder="Nomor Induk Pegawai"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors">
                        @error('nip')
                            <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" wire:model.live="email" placeholder="guru@sekolah.com"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors">
                        @error('email')
                            <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                            <span class="text-xs text-gray-400 font-normal ml-1">
                                {{ $guru_id ? '(Kosongkan jika tidak ganti)' : '(Kosongkan = Default nip)' }}
                            </span>
                        </label>
                        <input type="password" id="password" wire:model.live="password"
                            placeholder="{{ $guru_id ? '••••••••' : 'Buat password...' }}"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors">
                        @error('password')
                            <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


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
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            Status Akun <span class="text-rose-500">*</span>
                        </label>
                        <select id="status" wire:model.live="status"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors py-2.5">
                            <option value="">Pilih Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                            <option value="pindah">Pindah</option>
                        </select>
                        @error('status')
                            <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 w-full">
                    <x-ui.button @click="closeModal();" class="w-full sm:w-auto" color="ghost">
                        Batal
                    </x-ui.button>
                    <x-ui.button type="submit" form="formSaveGuru" color="primary" icon="ri-save-3-line"
                        class="w-full sm:w-auto" wire:target="save">
                        {{ $guru_id ? 'Simpan Perubahan' : 'Tambahkan Guru' }}
                    </x-ui.button>
                </div>
            </x-slot>

        </form>
    </x-modal-wrapper>
</div>
