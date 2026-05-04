<div>
    <x-modal-wrapper title="Export Rekap Absensi (Admin)" icon="ri-file-excel-2-fill" iconColor="text-emerald-500"
        maxWidth="md"
        @open-export-modal.window="
            isLoading = true; 
            openModal();
            $wire.loadData().then(() => isLoading = false);
        ">

        <form id="formExportRekapAdmin"
            @submit.prevent="$dispatch('is-saving', true); $wire.exportExcel().finally(() => $dispatch('is-saving', false))">

            <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar flex flex-col gap-5">

                <div
                    class="bg-emerald-50 p-3.5 rounded-xl border border-emerald-100 flex gap-3 text-sm items-start shadow-sm">
                    <i class="ri-information-fill text-emerald-500 text-lg mt-0.5"></i>
                    <p class="text-emerald-800 text-xs sm:text-sm leading-relaxed">
                        Rekapitulasi kehadiran akan diunduh dalam format <b>.xlsx</b>. Silakan pilih kelas dan mata
                        pelajaran yang ingin direkap. Data guru akan menyesuaikan secara otomatis.
                    </p>
                </div>

              
                <div>
                    <label for="export_kelas_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Pilih Kelas <span class="text-rose-500">*</span>
                    </label>
                    <select id="export_kelas_id" wire:model.live="export_kelas_id"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm transition-colors">
                        <option value="">Pilih Kelas</option>
                        @foreach ($this->daftarKelas as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
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
                    <select id="export_mapel_id" wire:model.live="export_mapel_id"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm transition-colors">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach ($this->daftarMapel as $mapel)
                            <option value="{{ $mapel->id }}">
                                {{ Str::title($mapel->nama_mapel) }} ({{ $mapel->kode_mapel }})
                            </option>
                        @endforeach
                    </select>
                    @error('export_mapel_id')
                        <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

               
                <div class="animate-fade-in-down">
                    <label for="export_guru_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Pilih Guru Pengampu <span class="text-rose-500">*</span>
                    </label>
                    <select id="export_guru_id" wire:model="export_guru_id"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed"
                        {{ !$export_kelas_id || !$export_mapel_id ? 'disabled' : '' }}>

                        @if (!$export_kelas_id || !$export_mapel_id)
                            <option value="">Pilih Kelas & Mapel dulu...</option>
                        @elseif ($this->daftarGuruPengampu->isEmpty())
                            <option value="">Tidak ada guru di kombinasi Kelas dan mapel yang dipilih</option>
                        @else
                            <option value="">Pilih Guru...</option>
                            @foreach ($this->daftarGuruPengampu as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->user->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('export_guru_id')
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
                        class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 border border-slate-200 rounded-xl mt-4 animate-fade-in-down">
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
                @else
                    <div
                        class="mt-4 p-3.5 bg-emerald-50 border border-emerald-100 rounded-xl flex gap-3 items-start shadow-sm animate-fade-in-down">
                        <div class="bg-emerald-100 text-emerald-600 p-1.5 rounded-lg shrink-0 mt-0.5">
                            <i class="ri-calendar-check-line text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-semibold text-emerald-800 leading-snug">
                                {{ $this->infoSemesterBerjalan['teks'] }}
                            </p>
                            <p class="text-[10px] sm:text-xs text-emerald-600 mt-1">
                                Sistem otomatis mendeteksi rentang waktu semester berdasarkan bulan saat ini.
                            </p>
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
                 
                    <x-ui.button type="submit" form="formExportRekapAdmin" wire:target="exportExcel"
                        class="w-full sm:w-auto" icon="ri-save-3-line" color="primary" loadingText="Menyiapkan File...">
                        Download .xlsx
                    </x-ui.button>
                </div>
            </x-slot>

        </form>
    </x-modal-wrapper>
</div>
