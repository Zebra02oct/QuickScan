<div>
    <div class="max-w-7xl mx-auto space-y-6 animate-fade-in-down">

        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-slate-100 gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-2xl shadow-sm border border-indigo-100">
                    <i class="ri-global-fill"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-800">Laporan Absensi Sekolah</h1>
                    <p class="text-sm text-slate-500 font-medium">Pantau kedisiplinan seluruh siswa dan kelas secara
                        keseluruhan.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <select wire:model.live="filter_tahun"
                    class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 w-full md:w-auto outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer shadow-sm transition-all hover:bg-slate-100">
                    @foreach ($daftarTahun as $tahun)
                        <option value="{{ $tahun }}">Tahun {{ $tahun }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filter_semester"
                    class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 w-full md:w-auto outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer shadow-sm transition-all hover:bg-slate-100">
                    <option value="genap">Semester Genap (Jan-Jun)</option>
                    <option value="ganjil">Semester Ganjil (Jul-Des)</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            <x-ui.card title="Total Kelas" value="{{ $this->statistikGlobal['total_kelas'] }}" icon="ri-building-4-fill"
                color="primary" size="xs" subtitle="Aktif Semester Ini" />
            <x-ui.card title="Sesi Terlaksana" value="{{ $this->statistikGlobal['total_sesi'] }}"
                icon="ri-calendar-check-fill" color="success" size="xs" subtitle="Absensi" />
            <x-ui.card title="Rata-Rata Sekolah" value="{{ $this->statistikGlobal['rata_hadir'] }}%"
                icon="ri-line-chart-fill" color="warning" size="xs" subtitle="Tingkat Kehadiran" />
            <x-ui.card title="Total Mapel Aktif" value="{{ $this->statistikGlobal['total_mapel'] }}"
                icon="ri-book-3-fill" color="info" size="xs" subtitle="Semester Ini" />
        </div>


        <div class="flex flex-col sm:flex-row justify-between items-end gap-4 mt-8 mb-4 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Daftar Kelas Sekolah</h2>
                <p class="text-sm text-slate-500">Semester {{ ucfirst($filter_semester) }} Tahun {{ $filter_tahun }}
                </p>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($this->daftarKelas as $kelas)
                <div wire:key="kelas-{{ $kelas->id }}"
                    class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-lg hover:border-indigo-300 transition-all group flex flex-col h-full">

                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-start gap-3 w-full">

                            <div
                                class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-2xl shrink-0 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                                <i class="ri-building-4-fill"></i>
                            </div>
                            <div class="flex-grow">

                                <h3
                                    class="font-extrabold text-lg text-slate-800 leading-tight flex flex-wrap items-center gap-1.5">
                                    Kelas {{ $kelas->nama_kelas }}
                                </h3>


                                <div class="mt-1.5 flex flex-wrap gap-1.5">
                                    <span
                                        class="text-[11px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200 flex items-center gap-1">
                                        <i class="ri-book-2-line text-[10px]"></i> {{ $kelas->total_mapel ?? 0 }} Mapel
                                        Diajarkan
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-3 mb-5 border border-slate-100 flex-grow">
                        <ul class="space-y-2 text-xs font-medium text-slate-600">
                            <li class="flex justify-between items-center">
                                <span class="flex items-center gap-1.5"><i class="ri-list-check-2 text-slate-400"></i>
                                    Total Sesi </span>
                                <span class="font-bold text-slate-800">{{ $kelas->total_sesi ?? 0 }} Sesi
                                    Absensi</span>
                            </li>


                            <li class="flex justify-between items-center pt-2 border-t border-slate-200 mt-2">
                                <span class="flex items-center gap-1.5"><i class="ri-group-line text-indigo-500"></i>
                                    Jumlah Siswa</span>
                                <span class="font-bold text-indigo-600 text-[11px]">
                                    Lihat Detail
                                </span>
                            </li>
                        </ul>
                    </div>


                    <x-ui.button
                        href="{{ route('admin.laporanAbsensi.detail', [
                            'kelas_id' => $kelas->id,
                            'start' => $this->getRentangTanggal()[0],
                            'end' => $this->getRentangTanggal()[1],
                        ]) }}">
                        Lihat Detail Kelas
                    </x-ui.button>

                </div>
            @empty

                <div
                    class="col-span-1 md:col-span-2 lg:col-span-3 bg-white p-12 rounded-2xl border border-dashed border-slate-300 text-center flex flex-col items-center justify-center">
                    <div
                        class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center text-3xl mb-4">
                        <i class="ri-inbox-archive-line"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">Belum Ada Data Kelas Aktif</h3>
                    <p class="text-slate-500 text-sm mt-1">Tidak ada aktivitas absensi di semester ini.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
