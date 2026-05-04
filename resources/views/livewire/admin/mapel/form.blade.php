<div>
    <x-modal-wrapper :title="$mapel_id ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran'" :icon="$mapel_id ? 'ri-edit-box-line' : 'ri-book-read-line'" :iconColor="$mapel_id ? 'text-amber-500' : 'text-indigo-500'" maxWidth="lg"
        @open-create-mapel.window="
            isLoading = true; 
            openModal();
            $wire.loadData().then(() => isLoading = false);
        "
        @open-edit-mapel.window="
            isLoading = true; 
            openModal();
            $wire.loadData($event.detail.id).then(() => isLoading = false);
        ">

        
        @if ($mapel_id && $has_attendance_history && strtolower(trim($nama_mapel)) !== strtolower(trim($original_nama_mapel)))
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-4 mx-6 mt-4 transition-all duration-300">
                <div class="flex items-start">
                    <i class="ri-alert-fill text-rose-500 mr-3 text-xl mt-0.5"></i>
                    <p class="text-sm text-rose-800 leading-relaxed">
                        <strong>PERHATIAN!</strong> Mata pelajaran ini sudah memiliki riwayat absensi.
                        Mengubah nama Mapel akan ikut merubah label nama pada laporan histori absensi di masa lalu.
                    </p>
                </div>
            </div>
        @endif

        <form id="formSaveMapel"
            @submit.prevent="$dispatch('is-saving', true); $wire.save().finally(() => $dispatch('is-saving', false))">

            <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar flex flex-col gap-5 pt-2">

                <div>
                    <label for="nama_mapel" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Mata Pelajaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nama_mapel" wire:model.live="nama_mapel"
                        placeholder="Contoh: Pemrograman Web"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">

                    @error('nama_mapel')
                        <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                    @enderror

                    <!-- 🔥 ALERT 2: WARNING NAMA DUPLIKAT -->
                    @if ($is_duplicate_name)
                        <div
                            class="mt-2 flex items-start gap-2 bg-amber-50 text-amber-700 p-2.5 rounded-lg border border-amber-200 animate-fade-in-up">
                            <i class="ri-error-warning-fill mt-0.5"></i>
                            <p class="text-xs font-medium leading-relaxed">
                                Nama Mapel ini sudah terdaftar di database. Sistem tetap mengizinkan penyimpanan karena
                                Kode Mapel akan dibedakan secara otomatis. Yakin ingin membuat duplikat?
                            </p>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="kode_mapel" class="block text-sm font-medium text-gray-700 mb-1">
                            Kode Mapel (Otomatis)
                        </label>
                        <div class="relative">
                            <input type="text" id="kode_mapel" wire:model="kode_mapel" disabled
                                class="block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-500 font-bold font-mono sm:text-sm cursor-not-allowed shadow-inner"
                                placeholder="---">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="ri-lock-line text-gray-400"></i>
                            </div>
                        </div>
                        <p class="mt-1.5 text-[10px] text-gray-400 italic">Kode ini diatur otomatis oleh sistem
                            berdasarkan nama.</p>
                    </div>

                    <div>
                        <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">
                            Kategori <span class="text-rose-500">*</span>
                        </label>
                        <select id="kategori" wire:model.live="kategori"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                            <option value="umum">Umum</option>
                            <option value="kejuruan">Kejuruan</option>
                        </select>
                        @error('kategori')
                            <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>

            <x-slot name="footer">
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 w-full">
                    <x-ui.button @click="closeModal();" class="w-full sm:w-auto" color="white">
                        Batal
                    </x-ui.button>
                    <x-ui.button type="submit" form="formSaveMapel" color="primary" icon="ri-save-3-line"
                        class="w-full sm:w-auto" wire:target="save">
                        {{ $mapel_id ? 'Simpan Perubahan' : 'Tambahkan Mapel' }}
                    </x-ui.button>
                </div>
            </x-slot>

        </form>
    </x-modal-wrapper>
</div>
