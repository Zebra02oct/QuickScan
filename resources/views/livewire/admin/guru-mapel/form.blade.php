<div>
    <x-modal-wrapper :title="$guru_mapel_id ? 'Edit Penugasan Guru' : 'Tambah Penugasan Baru'" :icon="$guru_mapel_id ? 'ri-edit-box-line' : 'ri-user-star-line'" :iconColor="$guru_mapel_id ? 'text-amber-500' : 'text-indigo-500'" maxWidth="md"
        @open-create-gurumapel.window="
            isLoading = true; 
            openModal();
            $wire.loadData().then(() => isLoading = false);
        "
        @open-edit-gurumapel.window="
            isLoading = true; 
            openModal();
            $wire.loadData($event.detail.id).then(() => isLoading = false);
        ">

        <form id="formSaveGuruMapel"
            @submit.prevent="$dispatch('is-saving', true); $wire.save().finally(() => $dispatch('is-saving', false))">

            <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar flex flex-col gap-5">

                <div>
                    <label for="guru_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Pilih Guru <span class="text-rose-500">*</span>
                    </label>
                    <select id="guru_id" wire:model="guru_id"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                        <option value="">Pilih Guru</option>
                        @foreach ($this->listGuru as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->user->name ?? 'Tanpa Nama' }}</option>
                        @endforeach
                    </select>
                    @error('guru_id')
                        <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="mapel_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Mata Pelajaran <span class="text-rose-500">*</span>
                    </label>
                    <select id="mapel_id" wire:model="mapel_id"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach ($this->listMapel as $mapel)
                            <option value="{{ $mapel->id }}">[{{ $mapel->kode_mapel }}] -
                                {{ ucwords($mapel->nama_mapel) }}</option>
                        @endforeach
                    </select>
                    @error('mapel_id')
                        <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Kelas <span class="text-rose-500">*</span>
                    </label>
                    <select id="kelas_id" wire:model="kelas_id"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                        <option value="">Pilih Kelas</option>
                        @foreach ($this->listKelas as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                    @error('kelas_id')
                        <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <x-slot name="footer">
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 w-full">
                    <x-ui.button @click="closeModal();" class="w-full sm:w-auto" color="ghost">
                        Batal
                    </x-ui.button>
                    <x-ui.button type="submit" form="formSaveGuruMapel" color="primary" icon="ri-save-3-line"
                        class="w-full sm:w-auto" wire:target="save">
                        {{ $guru_mapel_id ? 'Simpan Perubahan' : 'Tambahkan Penugasan' }}
                    </x-ui.button>
                </div>
            </x-slot>

        </form>
    </x-modal-wrapper>
</div>
