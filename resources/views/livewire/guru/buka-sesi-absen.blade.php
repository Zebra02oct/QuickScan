<div>
    <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-10 transition-all duration-300">
        <div
            class="bg-white/90 backdrop-blur-xl border border-sky-100 shadow-xl rounded-2xl sm:rounded-3xl overflow-hidden">

            <div
                class="p-5 sm:p-8 border-b border-sky-50 bg-gradient-to-r from-sky-50 to-white flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-5">
                <div
                    class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 shadow-inner border border-sky-200/50">
                    <i class='ri-slideshow-line text-2xl sm:text-4xl'></i>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-gray-800 tracking-tight">Buka Sesi Absensi Baru</h2>
                    <p class="text-sm sm:text-base text-gray-500 mt-1">Pilih mata pelajaran dan kelas yang akan diajar
                        pada sesi ini.</p>
                </div>
            </div>

            <div class="p-5 sm:p-8 space-y-6 sm:space-y-8">
                <div>
                    <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <i class="ri-book-2-line text-sky-600"></i>
                        Buka Sesi Absensi Mata Pelajaran
                    </h3>

                    <div>
                        <label class="block text-sm sm:text-base font-bold text-gray-700 mb-2">
                            Mata Pelajaran <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select wire:model.live="mapel_id"
                                class="w-full pl-4 pr-10 py-3 sm:py-4 text-sm sm:text-base border border-gray-200 rounded-xl sm:rounded-2xl focus:ring-4 focus:ring-sky-500/20 focus:border-sky-500 outline-none bg-slate-50 transition-all cursor-pointer appearance-none shadow-sm">
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach ($this->listMapel as $gm)
                                    <option value="{{ $gm->mapel_id }}">{{ $gm->mapel->kode_mapel }} -
                                        {{ ucwords($gm->mapel->nama_mapel) }}</option>
                                @endforeach
                            </select>

                        </div>
                        @error('mapel_id')
                            <span class="text-rose-500 text-sm font-medium mt-1.5 block">{{ $message }}</span>
                        @enderror
                    </div>

                    @if ($mapel_id)
                        <div class="animate-fade-in-down mt-6">
                            <label class="block text-sm sm:text-base font-bold text-gray-700 mb-3">
                                Pilih Kelas yang Diajar (Bisa digabung) <span class="text-rose-500">*</span>
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                                @forelse ($this->listKelas as $gm)
                                    <label
                                        class="flex items-start p-4 sm:p-5 border-2 rounded-xl sm:rounded-2xl cursor-pointer transition-all duration-200
                                        {{ in_array($gm->id, $kelas_terpilih) ? 'border-sky-500 bg-sky-50/50 shadow-md transform scale-[1.01]' : 'border-gray-100 bg-white hover:border-gray-300 hover:bg-slate-50 shadow-sm' }}">

                                        <input type="checkbox" wire:model="kelas_terpilih" value="{{ $gm->id }}"
                                            class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600 rounded-md border-gray-300 focus:ring-sky-500 mt-0.5 sm:mt-0 transition-colors">

                                        <div class="ml-3 sm:ml-4 flex flex-col w-full">
                                            <span
                                                class="font-bold text-base sm:text-lg leading-tight {{ in_array($gm->id, $kelas_terpilih) ? 'text-sky-900' : 'text-gray-800' }}">
                                                {{ $gm->kelas->nama_kelas }}
                                            </span>
                                            <span
                                                class="text-xs sm:text-sm font-semibold mt-1 sm:mt-1.5 {{ in_array($gm->id, $kelas_terpilih) ? 'text-sky-600' : 'text-slate-400' }} flex items-center gap-1.5">
                                                <i class="ri-group-line text-sm sm:text-base"></i>
                                                {{ $gm->kelas->siswas_count ?? 0 }} Siswa Terdaftar
                                            </span>
                                        </div>
                                    </label>
                                @empty
                                    <div
                                        class="col-span-full p-5 bg-amber-50 text-amber-700 rounded-2xl border border-amber-200 flex items-start gap-3 shadow-sm">
                                        <i class="ri-error-warning-fill text-2xl mt-0.5"></i>
                                        <div>
                                            <h4 class="font-bold text-amber-800">Tidak ada kelas yang ditugaskan</h4>
                                            <p class="text-sm mt-1">Anda belum memiliki jadwal mengajar untuk mata
                                                pelajaran
                                                ini. Silakan hubungi Admin.</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            @error('kelas_terpilih')
                                <span class="text-rose-500 text-sm font-medium mt-1.5 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @else
                        <div
                            class="p-8 sm:p-12 border-2 border-dashed border-gray-200 rounded-2xl sm:rounded-3xl bg-slate-50 text-center text-slate-400 flex flex-col items-center justify-center transition-all mt-6">
                            <div
                                class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mb-4">
                                <i class="ri-checkbox-multiple-line text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-gray-600">Pilih Mata Pelajaran</h3>
                            <p class="text-sm sm:text-base font-medium mt-1">Daftar kelas akan muncul setelah mapel
                                dipilih.
                            </p>
                        </div>
                    @endif
                </div>

            </div>

            <div
                class="p-5 sm:p-6 bg-slate-50 border-t border-gray-100 flex flex-col sm:flex-row justify-end items-center gap-3">

                <x-ui.button onclick="history.back()" size="lg" class="w-full sm:w-auto" color="white">
                    Batal
                </x-ui.button>
                <x-ui.button wire:click="mulaiSesi" color="primary" icon="ri-rocket-2-fill" class="w-full sm:w-auto">
                    Mulai Live Absen
                </x-ui.button>

            </div>

        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {

                Livewire.on('swal:success', (data) => {
                    let info = data[0];
                    Swal.fire({
                        title: info.title,
                        text: info.text,
                        icon: 'success',
                        confirmButtonColor: '#22c55e',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'rounded-2xl'
                        }
                    });
                });

                Livewire.on('swal:error', (data) => {
                    let info = data[0];
                    Swal.fire({
                        title: info.title,
                        text: info.text,
                        icon: 'error',
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'Mengerti',
                        customClass: {
                            popup: 'rounded-2xl'
                        }
                    });
                });

            });
        </script>
    @endpush
</div>
