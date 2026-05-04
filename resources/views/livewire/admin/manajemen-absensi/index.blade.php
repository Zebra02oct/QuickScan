<div>
    <div
        class="bg-white/80 backdrop-blur-xl border border-sky-100 shadow-sm rounded-2xl overflow-hidden mb-6 transition-all duration-300">

        <div
            class="p-5 border-b border-sky-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4 flex-1">
                <div
                    class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class='ri-folder-user-line text-2xl'></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Manajemen Absensi Kelas (Admin)</h2>
                    <p class="text-sm text-gray-500">Kelola dan pantau semua data presensi sekolah.</p>
                </div>
            </div>

            <div class="shrink-0 w-full md:w-auto mt-4 md:mt-0">
                <button type="button" x-data @click="$dispatch('open-export-modal')"
                    class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-xl font-bold text-sm border border-emerald-200 hover:border-transparent transition-all shadow-sm">
                    <i class="ri-file-excel-2-line text-lg"></i> Download Rekap
                </button>
            </div>
        </div>

        <div class="p-4 sm:p-5">
            <div class="bg-slate-50 p-5 rounded-xl border border-gray-100 mb-6 shadow-inner">

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
                    <div class="flex items-center gap-3 shrink-0">
                        <div
                            class="w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center text-gray-500">
                            <i class="ri-filter-3-line text-lg"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Filter & Pencarian Sesi</span>
                    </div>

                    <div class="relative w-full md:w-80">
                        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari kelas, mapel, atau nama guru..."
                            class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm text-sm text-gray-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-gray-200/60">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 ml-1">Filter Kelas</label>
                        <select wire:model.live="filter_kelas_id"
                            class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm text-sm text-gray-600">
                            <option value="">Semua Kelas</option>
                            @foreach ($this->daftarKelas as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 ml-1">Filter Mapel</label>
                        <select wire:model.live="filter_mapel_id"
                            class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm text-sm text-gray-600">
                            <option value="">Semua Mata Pelajaran</option>
                            @foreach ($this->daftarMapel as $mapel)
                                <option value="{{ $mapel->id }}">{{ Str::title($mapel->nama_mapel) }}
                                    ({{ $mapel->kode_mapel }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 ml-1">Dari Tanggal</label>
                        <input type="date" wire:model.live="tanggal_mulai"
                            class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm text-sm text-gray-600">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 ml-1">Sampai Tanggal</label>
                        <input type="date" wire:model.live="tanggal_akhir"
                            class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm text-sm text-gray-600">
                    </div>
                </div>

                @if ($search || $filter_kelas_id || $filter_mapel_id || $tanggal_mulai || $tanggal_akhir)
                    <div class="mt-4 flex justify-end animate-fade-in-down">
                        <button type="button" wire:click="resetFilter"
                            class="w-full sm:w-auto px-5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-bold rounded-xl text-sm transition-all flex items-center justify-center gap-2 shadow-sm">
                            <i class="ri-refresh-line"></i> Reset Filter
                        </button>
                    </div>
                @endif
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm custom-scrollbar">
                <table class="w-full text-left min-w-[800px]">
                    <thead
                        class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100 whitespace-nowrap text-xs sm:text-sm">
                        <tr>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center w-10">No</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-left">Guru</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-left">Tgl & Status Sesi</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-left">Informasi Kelas</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center">Hadir/Terlambat</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center">Izin/Sakit</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center">Alpa</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($this->daftarSesi as $index => $row)
                            <tr class="hover:bg-sky-50/50 transition-colors group" wire:key="sesi-{{ $row->id }}">
                                <td
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-center text-gray-500 whitespace-nowrap text-xs sm:text-sm">
                                    {{ $this->daftarSesi->firstItem() + $index }}
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-left whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                      
                                        {{ $row->guruMapel->guru->user->name ?? 'Guru Tidak Diketahui' }}
                                    </span>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-left whitespace-nowrap">
                                    <div class="text-xs sm:text-sm font-bold text-slate-700">
                                        {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('l, d M Y') }}
                                    </div>
                                    <div class="mt-1">
                                        @if ($row->status == 'berjalan')
                                            <span
                                                class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                                <i class="ri-broadcast-line animate-pulse"></i> SEDANG JALAN
                                            </span>
                                        @else
                                            <span
                                                class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                                <i class="ri-lock-line"></i> SELESAI
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-left">
                                    <div class="text-xs sm:text-sm font-bold text-slate-700">
                                        {{ Str::title($row->guruMapel->mapel->nama_mapel ?? '-') }}
                                        ({{ $row->guruMapel->mapel->kode_mapel ?? '-' }})
                                    </div>
                                    <div class="text-[11px] sm:text-xs text-indigo-600 font-semibold mt-0.5">
                                        <i class="ri-building-4-line align-middle"></i>
                                        {{ $row->guruMapel->kelas->nama_kelas ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-center whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                        {{ $row->hadir_count }}
                                    </span>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-center whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $row->izin_sakit_count }}
                                    </span>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-center whitespace-nowrap">
                                    @if ($row->alpa_count > 0)
                                        <span
                                            class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                            {{ $row->alpa_count }}
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-50 text-gray-500 border border-gray-100">0</span>
                                    @endif
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5 sm:gap-2">

                                        <a href="{{ route('admin.manajemenAbsensi.detail', ['sesi_id' => $row->id]) }}"
                                            wire:navigate
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white transition-all shadow-sm border border-sky-100 hover:border-transparent">
                                            <i class="ri-eye-line"></i> Detail
                                        </a>

                                    
                                        <button
                                            onclick="konfirmasiHapusSesi({{ $row->id }} , @js($row->guruMapel->mapel->nama_mapel), @js(\Carbon\Carbon::parse($row->tanggal)->translatedFormat('l, d M Y')))"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-all shadow-sm border border-rose-100 hover:border-transparent"
                                            title="Hapus Sesi secara Paksa">
                                            <i class="ri-delete-bin-line"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 sm:p-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 sm:w-20 sm:h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-3 sm:mb-4 shadow-inner border border-slate-100">
                                            <i class="ri-inbox-archive-line text-3xl sm:text-4xl"></i>
                                        </div>
                                        <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-1">Belum Ada Sesi
                                        </h3>
                                        <p class="text-xs sm:text-sm text-gray-500">Data presensi tidak ditemukan
                                            berdasarkan filter saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->daftarSesi->hasPages())
                <div class="mt-4 sm:mt-5 border-t border-slate-100 pt-3 sm:pt-4">
                    {{ $this->daftarSesi->links() }}
                </div>
            @endif
        </div>
        <livewire:admin.manajemen-absensi.modal-export-rekap />
    </div>

    @push('scripts')
        <script>
            function konfirmasiHapusSesi(id, namaMapel, tanggal) {
                Swal.fire({
                    title: 'Hapus Paksa Sesi?',
                    html: `
                        <div class="text-slate-600 mb-2 text-sm leading-relaxed">
                            Apakah Anda yakin ingin menghapus data absen <b class="text-rose-600">${namaMapel}</b> pada tanggal ${tanggal}?
                        </div>
                        <div class="text-xs text-rose-500 bg-rose-50 p-2 rounded-lg mt-3 border border-rose-100">
                            <i class="ri-alert-line"></i> Peringatan Admin: Data absensi siswa akan hilang permanen!
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ri-delete-bin-line mr-1"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-[2rem] shadow-2xl border border-slate-100 p-6',
                        title: 'text-xl font-bold text-slate-800',
                        htmlContainer: 'text-base m-0 p-0',
                        actions: 'w-full flex gap-3 mt-6',
                        confirmButton: 'flex-1 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl px-5 py-3 transition-colors shadow-sm',
                        cancelButton: 'flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl px-5 py-3 transition-colors'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('hapus-data-absen', {
                            id: id
                        });
                    }
                });
            }

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
