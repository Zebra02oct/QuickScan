<div>
    <x-modal-wrapper title="Kenaikan Kelas & Kelulusan" icon="ri-graduation-cap-line" iconColor="text-indigo-500"
        maxWidth="2xl"
        @open-mutasi-siswa.window="
        isLoading = true; 
        openModal();
        $wire.loadData().then(() => isLoading = false);">

        <form id="formMutasiSiswa"
            @submit.prevent="$dispatch('is-saving', true); $wire.save().finally(() => $dispatch('is-saving', false))">

            <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar flex flex-col gap-6">

                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-5 p-5 bg-indigo-50/50 rounded-2xl border border-indigo-100">

                    <div>
                        <label class="block text-sm font-bold text-indigo-900 mb-1.5">
                            Pilih Kelas Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model.live="kelas_asal_id"
                            class="block w-full rounded-xl border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 bg-white">
                            <option value="">Pilih Kelas</option>
                            @foreach ($this->listKelas as $kls)
                                <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                            @endforeach
                        </select>
                        @error('kelas_asal_id')
                            <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-indigo-900 mb-1.5">
                            Tujuan Kenaikan <span class="text-rose-500">*</span>
                        </label>

                        @if ($is_lulus)
                            <div
                                class=" w-full rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 font-bold sm:text-sm py-2.5 px-3 flex items-center gap-2 cursor-not-allowed">
                                <i class="ri-medal-fill text-lg"></i> Luluskan Siswa
                            </div>
                            <p class="text-[10px] text-emerald-600 mt-1 font-medium">Status siswa akan diubah menjadi
                                "Lulus".</p>
                        @else
                            <select wire:model="kelas_tujuan_id"
                                class="block w-full rounded-xl border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 bg-white"
                                {{ empty($kelas_asal_id) ? 'disabled' : '' }}>
                                <option value="">Pilih Kelas Tingkat Selanjutnya</option>
                                @foreach ($list_kelas_tujuan as $kls)
                                    <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                                @endforeach
                            </select>

                            @if ($kelas_asal_id && count($list_kelas_tujuan) == 0)
                                <p class="text-[10px] text-rose-500 mt-1 font-medium">Data kelas tingkat selanjutnya
                                    belum dibuat.</p>
                            @endif
                        @endif

                        @error('kelas_tujuan_id')
                            <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                @if ($kelas_asal_id)
                    <div class="animate-fade-in-up" x-data="{ selected: @entangle('siswa_ids') }">
                        <div class="flex justify-between items-center mb-3 px-1">
                            <h3 class="text-sm font-bold text-gray-800">
                                Daftar Siswa
                                <span
                                    class="text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md text-[11px] sm:text-xs ml-2 border border-indigo-100 shadow-sm inline-flex items-center gap-1">
                                    <span x-text="selected.length" class="font-black text-indigo-700"></span>
                                    <span class="text-indigo-400">/</span>
                                    {{ count($list_siswa) }} Terpilih
                                </span>
                            </h3>
                            <p class="text-xs text-slate-500 font-medium hidden sm:block">Hapus centang jika tinggal
                                kelas.</p>
                        </div>

                        @if (count($list_siswa) > 0)
                            <div
                                class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden max-h-[300px] overflow-y-auto custom-scrollbar">
                                <div class="divide-y divide-gray-100">
                                    @foreach ($list_siswa as $siswa)
                                        <label
                                            class="flex items-center px-4 py-3 hover:bg-indigo-50/50 cursor-pointer transition-colors group">
                                            <input type="checkbox" x-model="selected" value="{{ $siswa->id }}"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 mr-3 h-4 w-4 transition-all">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-sm font-bold text-gray-700 group-hover:text-indigo-700 transition-colors">
                                                    {{ $siswa->user->name ?? 'Tanpa Nama' }}
                                                </span>
                                                <span class="text-[11px] text-gray-400 font-mono mt-0.5">
                                                    NISN: {{ $siswa->nisn }}
                                                </span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @error('siswa_ids')
                                <span class="text-rose-500 text-xs mt-1 block font-medium px-1">{{ $message }}</span>
                            @enderror
                        @else
                            <div class="p-8 text-center border-2 border-dashed border-gray-200 rounded-xl bg-slate-50">
                                <div
                                    class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-slate-400 shadow-sm mx-auto mb-3">
                                    <i class="ri-user-unfollow-line text-2xl"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-700 mb-1">Kelas Kosong</h4>
                                <p class="text-xs text-slate-500">Tidak ada siswa berstatus aktif di kelas ini.</p>
                            </div>
                        @endif
                    </div>
                @endif

            </div>

            <x-slot name="footer">
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 w-full">
                    <x-ui.button @click="closeModal();" class="w-full sm:w-auto" color="ghost">
                        Batal
                    </x-ui.button>
                    @if ($kelas_asal_id && count($list_siswa) > 0)
                        <x-ui.button type="submit" form="formMutasiSiswa"
                            color="{{ $is_lulus ? 'success' : 'primary' }}"
                            icon="{{ $is_lulus ? 'ri-medal-line' : 'ri-arrow-right-up-line' }}"
                            class="w-full sm:w-auto" wire:target="save">
                            {{ $is_lulus ? 'Luluskan Siswa Terpilih' : 'Proses Kenaikan Kelas' }}
                        </x-ui.button>
                    @endif
                </div>
            </x-slot>

        </form>
    </x-modal-wrapper>
</div>
