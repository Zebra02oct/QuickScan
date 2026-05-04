<div>
    <x-modal-wrapper :title="$guru_mapel_id ? 'Edit Penugasan' : 'Tambah Penugasan Baru'" :icon="$guru_mapel_id ? 'ri-edit-box-line' : 'ri-user-star-line'" :iconColor="$guru_mapel_id ? 'text-amber-500' : 'text-indigo-500'" maxWidth="md"
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

        @if ($guru_mapel_id && $has_history)
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-2 mx-6 mt-4 transition-all duration-300">
                <div class="flex items-start">
                    <i class="ri-lock-2-fill text-rose-500 mr-3 text-xl mt-0.5"></i>
                    <p class="text-xs text-rose-800 leading-relaxed font-medium">
                        <strong>DATA TERKUNCI!</strong><br>
                        Penugasan ini sudah memiliki histori transaksi (Sesi) Absensi.
                        Mapel dan Kelas tidak dapat diubah untuk menjaga data.
                        Untuk "menghapus" dari daftar, ubah Status menjadi <b>Nonaktif</b>.
                    </p>
                </div>
            </div>
        @endif

        <form id="formSaveGuruMapel"
            @submit.prevent="$dispatch('is-saving', true); $wire.save().finally(() => $dispatch('is-saving', false))">

            <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar flex flex-col gap-5 pt-4">



                <div>
                    <label for="guru_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Pilih Guru <span class="text-rose-500">*</span>
                    </label>

                    <select id="guru_id" wire:model="guru_id" {{ $guru_mapel_id ? 'disabled' : '' }}
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors disabled:bg-gray-100 disabled:text-gray-500">
                        <option value="">Pilih Guru</option>

                        @foreach ($this->listGuru as $guru)
                            <option value="{{ $guru->id }}" wire:key="guru-{{ $guru->id }}"
                                @class(['text-red-500' => $guru->user?->status != 'aktif']) {{ $guru_id == $guru->id ? 'selected' : '' }}>
                                {{ $guru->user->name ?? 'Tanpa Nama' }}
                                {{ $guru->user?->status != 'aktif' ? '(' . ucfirst($guru->user?->status) . ')' : '' }}
                            </option>
                        @endforeach

                    </select>

                    @if ($guru_mapel_id)
                        <p class="mt-1 text-[10px] text-gray-500 italic">Guru tidak dapat diganti saat mode edit.</p>
                    @endif

                    @error('guru_id')
                        <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="mapel_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Mata Pelajaran <span class="text-rose-500">*</span>
                    </label>
                    <!-- 🔥 DISABLED KETIKA ADA HISTORI -->
                    <select id="mapel_id" wire:model="mapel_id" {{ $has_history ? 'disabled' : '' }}
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors disabled:bg-gray-100 disabled:text-gray-500">
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
                    <select id="kelas_id" wire:model="kelas_id" {{ $has_history ? 'disabled' : '' }}
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors disabled:bg-gray-100 disabled:text-gray-500">
                        <option value="">Pilih Kelas</option>
                        @foreach ($this->listKelas as $kelas)
                            <option value="{{ $kelas->id }}" @class(['text-red-500' => !$kelas->is_active])>
                                {{ $kelas->nama_kelas }} {{ !$kelas->is_active ? '(Nonaktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('kelas_id')
                        <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                    @enderror
                </div>


                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">
                        Status Penugasan <span class="text-rose-500">*</span>
                    </label>
                    <select id="is_active" wire:model="is_active"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                    @error('is_active')
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
