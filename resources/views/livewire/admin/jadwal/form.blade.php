<div>
    <x-modal-wrapper :title="$jadwal_id ? 'Edit Plotting Mengajar' : 'Plotting Guru Massal'" :icon="$jadwal_id ? 'ri-edit-box-line' : 'ri-user-shared-line'" :iconColor="$jadwal_id ? 'text-amber-500' : 'text-orange-500'" maxWidth="md"
        @open-create-jadwal.window="
            isLoading = true; 
            openModal();
            $wire.loadData().then(() => isLoading = false);
        "
        @open-edit-jadwal.window="
            isLoading = true; 
            openModal();
            $wire.loadData($event.detail.id).then(() => isLoading = false);
        ">

        <form id="formSaveJadwal"
            @submit.prevent="$dispatch('is-saving', true); $wire.save().finally(() => $dispatch('is-saving', false))">

            <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar flex flex-col gap-5">

                @if (!$has_active_ta && !$jadwal_id)
                    <div
                        class="bg-rose-50 border border-rose-200 rounded-2xl p-5 text-center flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mb-3">
                            <i class="ri-error-warning-fill text-4xl text-rose-500"></i>
                        </div>
                        <h3 class="font-bold text-rose-700 text-lg mb-1">Tahun Ajaran Belum Diset!</h3>
                        <p class="text-sm text-rose-600/80 mb-4">
                            Kamu tidak bisa membuat jadwal karena tidak ada Tahun Ajaran yang berstatus <b>Aktif</b>.
                        </p>
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tahun Ajaran (Otomatis)
                        </label>
                        <div class="relative">
                            <input type="text" wire:model="nama_ta_aktif" disabled
                                class="block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-500 font-bold sm:text-sm cursor-not-allowed shadow-inner pl-10">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ri-calendar-event-line text-gray-400"></i>
                            </div>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="ri-lock-line text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="guru_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Guru Pengajar <span class="text-rose-500">*</span>
                        </label>
                        <select id="guru_id" wire:model="guru_id"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2.5">
                            <option value="">-- Pilih Guru --</option>
                            @foreach ($list_guru as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->user->name ?? 'User Tidak Ditemukan' }}
                                </option>
                            @endforeach
                        </select>
                        @error('guru_id')
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Mata Pelajaran <span class="text-rose-500">*</span>
                        </label>

                        @if ($jadwal_id)
                            <select wire:model="mapel_id.0"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2.5">
                                <option value="">-- Pilih Mapel --</option>
                                @foreach ($list_mapel as $mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->kode_mapel }} -
                                        {{ ucwords($mapel->nama_mapel) }}</option>
                                @endforeach
                            </select>
                        @else
                            <div x-data="{ open: false }" class="relative">
                                <button type="button" @click="open = !open"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm px-4 py-2.5 text-left sm:text-sm flex justify-between items-center hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                    <span class="block truncate font-medium"
                                        :class="$wire.mapel_id.length > 0 ? 'text-orange-600' : 'text-gray-500'"
                                        x-text="$wire.mapel_id.length > 0 ? $wire.mapel_id.length + ' Mapel Dipilih' : '-- Pilih Beberapa Mapel --'">
                                    </span>
                                    <i class="ri-arrow-down-s-line text-gray-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }"></i>
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition.opacity
                                    style="display: none;"
                                    class="absolute z-50 mt-1 w-full bg-white shadow-xl rounded-xl border border-gray-100 max-h-56 overflow-y-auto py-1 custom-scrollbar">
                                    @foreach ($list_mapel as $mapel)
                                        <label
                                            class="flex items-center px-4 py-2 hover:bg-orange-50 cursor-pointer border-b border-gray-50 last:border-0 transition-colors">
                                            <input type="checkbox" wire:model="mapel_id" value="{{ $mapel->id }}"
                                                class="rounded border-gray-300 text-orange-500 focus:ring-orange-500 mr-3 h-4 w-4">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-sm font-semibold text-gray-700">{{ ucwords($mapel->nama_mapel) }}</span>
                                                <span
                                                    class="text-[10px] text-gray-400 font-mono">{{ $mapel->kode_mapel }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @error('mapel_id')
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kelas Tujuan <span class="text-rose-500">*</span>
                        </label>

                        @if ($jadwal_id)
                            <select wire:model="kelas_id.0"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2.5">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($list_kelas as $kls)
                                    <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                                @endforeach
                            </select>
                        @else
                            <div x-data="{ open: false }" class="relative">
                                <button type="button" @click="open = !open"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm px-4 py-2.5 text-left sm:text-sm flex justify-between items-center hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                    <span class="block truncate font-medium"
                                        :class="$wire.kelas_id.length > 0 ? 'text-orange-600' : 'text-gray-500'"
                                        x-text="$wire.kelas_id.length > 0 ? $wire.kelas_id.length + ' Kelas Dipilih' : '-- Pilih Beberapa Kelas --'">
                                    </span>
                                    <i class="ri-arrow-down-s-line text-gray-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }"></i>
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition.opacity
                                    style="display: none;"
                                    class="absolute z-50 mt-1 w-full bg-white shadow-xl rounded-xl border border-gray-100 max-h-56 overflow-y-auto py-1 custom-scrollbar">
                                    <div class="grid grid-cols-2 p-1 gap-1">
                                        @foreach ($list_kelas as $kls)
                                            <label
                                                class="flex items-center px-3 py-2 hover:bg-orange-50 cursor-pointer rounded-lg transition-colors">
                                                <input type="checkbox" wire:model="kelas_id"
                                                    value="{{ $kls->id }}"
                                                    class="rounded border-gray-300 text-orange-500 focus:ring-orange-500 mr-2 h-4 w-4 flex-shrink-0">
                                                <span
                                                    class="text-sm font-semibold text-gray-700 truncate">{{ $kls->nama_kelas }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                        @error('kelas_id')
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

            </div>

            @error('plotting_conflict')
                <div class="mt-2 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-sm animate-pulse mx-6 mb-2">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="ri-error-warning-fill text-rose-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-rose-800">Plotting Ditolak!</h3>
                            <div class="mt-1 text-sm text-rose-700">
                                {!! $message !!}
                            </div>
                        </div>
                    </div>
                </div>
            @enderror

            <x-slot name="footer">
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 w-full">
                    <x-ui.button @click="closeModal();" class="w-full sm:w-auto" color="ghost">
                        {{ !$has_active_ta && !$jadwal_id ? 'Tutup Modal' : 'Batal' }}
                    </x-ui.button>

                    @if ($has_active_ta || $jadwal_id)
                        <x-ui.button type="submit" form="formSaveJadwal" color="primary" icon="ri-save-3-line"
                            class="w-full sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed" wire:target="save"
                            :disabled="$errors->has('plotting_conflict')">
                            {{ $jadwal_id ? 'Simpan Perubahan' : 'Sebar Jadwal' }}
                        </x-ui.button>
                    @endif

                </div>
            </x-slot>

        </form>
    </x-modal-wrapper>
</div>
