<div>
    <x-modal-wrapper :title="$kelas_id ? 'Edit Data Kelas' : 'Tambah Kelas Baru'" :icon="$kelas_id ? 'ri-edit-box-line' : 'ri-add-circle-line'" :iconColor="$kelas_id ? 'text-amber-500' : 'text-sky-500'" maxWidth="lg"
        @open-create-kelas.window="
            isLoading = true; 
            openModal();
            $wire.loadData().then(() => isLoading = false);
        "
        @open-edit-kelas.window="
            isLoading = true; 
            openModal();
            $wire.loadData($event.detail.id).then(() => isLoading = false);
        ">

        @if ($kelas_id && ($tingkat !== $original_tingkat || $jurusan !== $original_jurusan || $rombel !== $original_rombel))
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-4 transition-all duration-300">
                <div class="flex items-start">
                    <i class="ri-alert-fill text-amber-500 mr-3 text-xl mt-0.5"></i>
                    <p class="text-sm text-amber-800">
                        <strong>PERHATIAN!</strong> Perubahan nama kelas akan berdampak pada seluruh data terkait,
                        termasuk siswa, penugasan guru mata pelajaran, serta riwayat absensi.
                    </p>
                </div>
            </div>
        @endif

 
        @if ($kelas_id && $is_active != $original_is_active)
            @if ($is_active == 0)
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-4 transition-all duration-300">
                    <div class="flex items-start">
                        <i class="ri-error-warning-fill text-rose-500 mr-3 text-xl mt-0.5"></i>
                        <p class="text-sm text-rose-800">
                            <strong>KELAS AKAN DINONAKTIFKAN!</strong> Kelas ini tidak dapat dipilih lagi sebagai opsi
                            pada manajemn data siswa maupun penugasan guru mapel
                        </p>
                    </div>
                </div>
            @endif
        @endif

        <form id="formSaveKelas"
            @submit.prevent="$dispatch('is-saving', true); $wire.save().finally(() => $dispatch('is-saving', false))">

            <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar flex flex-col gap-5">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-1">
                            Tingkat <span class="text-red-500">*</span>
                        </label>
                        <select id="tingkat" wire:model.live="tingkat"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors">
                            <option value="">Pilih</option>
                            <option value="X">Sepuluh (X)</option>
                            <option value="XI">Sebelas (XI)</option>
                            <option value="XII">Duabelas (XII)</option>
                        </select>
                        @error('tingkat')
                            <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">
                            Jurusan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="jurusan" wire:model.live="jurusan" placeholder="Contoh: RPL, TKJ"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors">
                        @error('jurusan')
                            <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="rombel" class="block text-sm font-medium text-gray-700 mb-1">
                            Grup (Optional)
                        </label>
                        <input type="text" id="rombel" wire:model.live="rombel"
                            placeholder="Contoh: 1, 2 atau A, B"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors">
                        <p class="mt-1.5 text-[11px] text-gray-400 italic">Isi dengan angka/huruf pembeda.</p>
                        @error('rombel')
                            <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                   
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">
                            Status Kelas <span class="text-red-500">*</span>
                        </label>
                        <select id="is_active" wire:model.live="is_active"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm transition-colors">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                        @error('is_active')
                            <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="guru_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Wali Kelas <span class="text-rose-500">*</span>
                    </label>

                    <select id="guru_id" wire:model="guru_id" wire:key="select-guru-{{ $kelas_id ?? 'new' }}"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2.5">
                        <option value="">Pilih Guru</option>

                        @foreach ($this->listGuru as $guru)
                            @php
                                $isNonaktif = $guru->user && $guru->user->status !== 'aktif';
                            @endphp
                            <option value="{{ $guru->id }}" @class(['text-red-500' => $isNonaktif])
                                {{ $guru_id == $guru->id ? 'selected' : '' }}>
                                {{ $guru->user->name ?? 'User Tidak Ditemukan' }}
                                {{ $isNonaktif ? ' (Nonaktif - Harap Diganti)' : '' }}
                            </option>
                        @endforeach

                    </select>

                    @if ($this->guru_id)
                        @php
                            $guruTerpilih = $this->listGuru->firstWhere('id', $this->guru_id);
                        @endphp
                        @if ($guruTerpilih && $guruTerpilih->user && $guruTerpilih->user->status !== 'aktif')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">
                                <i class="ri-alert-line"></i> Wali kelas saat ini sudah dinonaktifkan. Silakan ganti
                                dengan guru yang aktif.
                            </p>
                        @endif
                    @endif

                    @error('guru_id')
                        <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="bg-sky-50 border border-sky-100 p-4 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-sky-600 shadow-sm">
                            <i class="ri-text-spacing text-xl"></i>
                        </div>
                        <div>
                            <span class="block text-[11px] uppercase tracking-wider text-sky-600 font-bold">Hasil Nama
                                Kelas:</span>
                            <h3 class="text-lg font-extrabold text-sky-900 leading-none mt-0.5">
                                {{ strtoupper($nama_kelas ?: '...') }}
                            </h3>
                        </div>
                    </div>
                </div>
                @error('nama_kelas')
                    <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                @enderror

            </div>

            <x-slot name="footer">
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 w-full">
                    <x-ui.button @click="closeModal();" class="w-full sm:w-auto" color="ghost">
                        Batal
                    </x-ui.button>
                    <x-ui.button type="submit" form="formSaveKelas" color="primary" icon="ri-save-3-line"
                        class="w-full sm:w-auto" wire:target="save">
                        {{ $kelas_id ? 'Simpan Perubahan' : 'Tambahkan Kelas' }}
                    </x-ui.button>
                </div>
            </x-slot>

        </form>
    </x-modal-wrapper>
</div>
