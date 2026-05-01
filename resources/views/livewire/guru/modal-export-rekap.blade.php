<div>
    <x-modal-wrapper title="Export Rekap Absensi" icon="ri-file-excel-2-fill" iconColor="text-emerald-500" maxWidth="md"
        @open-export-modal.window="
            isLoading = true; 
            openModal();
            $wire.loadData().then(() => isLoading = false);
        ">

        <form id="formExportRekap"
            @submit.prevent="$dispatch('is-saving', true); $wire.exportExcel().finally(() => $dispatch('is-saving', false))">

            <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar flex flex-col gap-5">


                <div
                    class="bg-emerald-50 p-3.5 rounded-xl border border-emerald-100 flex gap-3 text-sm items-start shadow-sm">
                    <i class="ri-information-fill text-emerald-500 text-lg mt-0.5"></i>
                    <p class="text-emerald-800 text-xs sm:text-sm leading-relaxed">
                        Rekapitulasi kehadiran akan diunduh dalam format <b>.xlsx</b>. Silakan pilih kelas dan mata
                        pelajaran yang ingin direkap.
                    </p>
                </div>


                <div>
                    <label for="export_kelas_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Pilih Kelas <span class="text-rose-500">*</span>
                    </label>
                    <select id="export_kelas_id" wire:model="export_kelas_id"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm transition-colors">
                        <option value="">Pilih Kelas</option>
                        @foreach ($this->daftarKelas as $kelas)
                            @if ($kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('export_kelas_id')
                        <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                    @enderror
                </div>


                <div>
                    <label for="export_mapel_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Pilih Mata Pelajaran <span class="text-rose-500">*</span>
                    </label>
                    <select id="export_mapel_id" wire:model="export_mapel_id"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm transition-colors">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach ($this->daftarMapel as $mapel)
                            @if ($mapel)
                                <option value="{{ $mapel->id }}">
                                    {{ Str::title($mapel->nama_mapel) }}
                                    ({{ $mapel->kode_mapel ? $mapel->kode_mapel : '' }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('export_mapel_id')
                        <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="export_type" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Rentang Waktu <span class="text-rose-500">*</span>
                    </label>
                    <select id="export_type" wire:model.live="export_type"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm transition-colors font-semibold text-slate-700 bg-slate-50">
                        <option value="semester">Satu Semester Berjalan</option>
                        <option value="custom">Pilih Tanggal Manual</option>
                    </select>
                </div>

                @if ($export_type === 'custom')
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                        <div>
                            <label for="start_date" class="block text-xs font-bold text-gray-600 mb-1">
                                Dari Tanggal <span class="text-rose-500">*</span>
                            </label>

                            <input type="date" id="start_date" wire:model="start_date"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-xs transition-colors">
                            @error('start_date')
                                <span class="text-rose-500 text-[10px] mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-xs font-bold text-gray-600 mb-1">
                                Sampai Tanggal <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" id="end_date" wire:model="end_date"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-xs transition-colors">
                            @error('end_date')
                                <span class="text-rose-500 text-[10px] mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif

            </div>

            <x-slot name="footer">
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 w-full">
                    <x-ui.button @click="$wire.dispatch('reset-modal'); closeModal();" class="w-full sm:w-auto"
                        color="white" type="button">
                        Batal
                    </x-ui.button>
                    <button type="submit" form="formExportRekap"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl font-bold text-sm transition-all shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="exportExcel"><i
                                class="ri-download-cloud-2-line text-lg"></i> Download .xlsx</span>
                        <span wire:loading wire:target="exportExcel"><i
                                class="ri-loader-4-line animate-spin text-lg"></i> Memproses...</span>
                    </button>
                </div>
            </x-slot>

        </form>
    </x-modal-wrapper>
</div>
