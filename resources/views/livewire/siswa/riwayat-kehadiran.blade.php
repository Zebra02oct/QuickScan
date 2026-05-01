<div>
    <div
        class="bg-white/80 backdrop-blur-xl border border-sky-100 shadow-sm rounded-2xl overflow-hidden mb-6 transition-all duration-300">

        <div
            class="p-5 border-b border-sky-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4 flex-1">
                <div
                    class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class='ri-history-line text-2xl'></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Riwayat Kehadiran</h2>
                    <p class="text-sm text-gray-500">Pantau log presensi dan rekap persentase kehadiranmu.</p>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-5">

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <x-ui.card title="Hadir" value="{{ $this->statistik['hadir'] }}" icon="ri-checkbox-circle-fill"
                    color="success" size="sm" subtitle="Termasuk Terlambat" />

                <x-ui.card title="Izin" value="{{ $this->statistik['izin'] }}" icon="ri-information-fill"
                    color="primary" size="sm" subtitle="kali pertemuan" />

                <x-ui.card title="Sakit" value="{{ $this->statistik['sakit'] }}" icon="ri-hospital-fill"
                    color="warning" size="sm" subtitle="kali pertemuan" />

                <x-ui.card title="Alpa" value="{{ $this->statistik['alpa'] }}" icon="ri-close-circle-fill"
                    color="danger" size="sm" subtitle="kali pertemuan" />
            </div>

            <div class="bg-slate-50 p-5 rounded-xl border border-gray-100 mb-6 shadow-inner">
                <div class="flex items-center gap-3 shrink-0 mb-4">
                    <div
                        class="w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center text-gray-500">
                        <i class="ri-filter-3-line text-lg"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Filter & Pencarian</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <select wire:model.live="filter_kelas_id"
                            class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm text-sm text-gray-600">
                            <option value="">Semua Kelas</option>
                            @foreach ($this->daftarKelas as $kelas)
                                @if ($kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select wire:model.live="filter_mapel_id"
                            class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm text-sm text-gray-600">
                            <option value="">Semua Mata Pelajaran</option>
                            @foreach ($this->daftarMapel as $mapel)
                                @if ($mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="relative w-full">
                        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" wire:model.live.debounce.300ms="search_guru"
                            placeholder="Cari Nama Guru..."
                            class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm text-sm text-gray-600">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm custom-scrollbar">
                <table class="w-full text-left min-w-[700px]">
                    <thead
                        class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100 whitespace-nowrap text-xs sm:text-sm">
                        <tr>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center w-10 sm:w-12">No</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-left">Tanggal & Jam</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-left">Mata Pelajaran</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-left">Guru Pengajar</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center">Kelas</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($this->riwayatAbsen as $index => $row)
                            <tr class="hover:bg-sky-50/50 transition-colors group" wire:key="absen-{{ $row->id }}">
                                <td
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-center text-gray-500 whitespace-nowrap text-xs sm:text-sm">
                                    {{ $this->riwayatAbsen->firstItem() + $index }}
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-left whitespace-nowrap">
                                    <div class="text-xs sm:text-sm font-bold text-slate-700">
                                        {{ \Carbon\Carbon::parse($row->sesiAbsensi->tanggal)->translatedFormat('l, d M Y') }}
                                    </div>
                                    <div class="text-[11px] sm:text-xs text-gray-500 mt-0.5">
                                        <i class="ri-time-line align-middle"></i>
                                        {{ $row->waktu_scan ? \Carbon\Carbon::parse($row->waktu_scan)->format('H:i') : '-' }}
                                        WIB
                                    </div>
                                </td>

                                <td
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">
                                    {{ $row->sesiAbsensi->guruMapel->mapel->nama_mapel ?? '-' }}
                                </td>

                                <td
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-left whitespace-nowrap text-xs sm:text-sm text-gray-600">
                                    <i class="ri-user-3-line mr-1 text-gray-400"></i>

                                    {{ $row->sesiAbsensi->guruMapel->guru->user->name ?? '-' }}
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-center whitespace-nowrap">
                                    <span
                                        class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-lg text-[11px] sm:text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm">
                                        {{ $row->sesiAbsensi->guruMapel->kelas->nama_kelas ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-center whitespace-nowrap">
                                    @if ($row->status == 'hadir')
                                        <span
                                            class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-lg text-[11px] sm:text-xs font-bold bg-green-50 text-green-700 border border-green-100 shadow-sm">
                                            <i class="ri-check-line mr-1"></i> Hadir
                                        </span>
                                    @elseif($row->status == 'terlambat')
                                        <span
                                            class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-lg text-[11px] sm:text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100 shadow-sm">
                                            <i class="ri-timer-line mr-1"></i> Hadir (Terlambat)
                                        </span>
                                    @elseif($row->status == 'izin')
                                        <span
                                            class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-lg text-[11px] sm:text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 shadow-sm">
                                            <i class="ri-information-line mr-1"></i> Izin
                                        </span>
                                    @elseif($row->status == 'sakit')
                                        <span
                                            class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-lg text-[11px] sm:text-xs font-bold bg-orange-50 text-orange-700 border border-orange-100 shadow-sm">
                                            <i class="ri-hospital-line mr-1"></i> Sakit
                                        </span>
                                    @elseif($row->status == 'alpa')
                                        <span
                                            class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-lg text-[11px] sm:text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100 shadow-sm">
                                            <i class="ri-close-line mr-1"></i> Alpa
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 sm:p-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 sm:w-20 sm:h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-3 sm:mb-4 shadow-inner border border-slate-100">
                                            <i class="ri-file-list-3-line text-3xl sm:text-4xl"></i>
                                        </div>
                                        <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-1">
                                            Tidak Ada Data Absensi
                                        </h3>
                                        <p class="text-xs sm:text-sm text-gray-500">Coba ubah filter atau pencarian
                                            Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->riwayatAbsen->hasPages())
                <div class="w-full">
                    {{ $this->riwayatAbsen->links('components.ui.custom-pagination') }}
                </div>
            @endif



        </div>
    </div>
</div>
