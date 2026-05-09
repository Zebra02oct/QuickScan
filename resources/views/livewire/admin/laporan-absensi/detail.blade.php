<div>
    <div class="max-w-7xl mx-auto space-y-5 animate-fade-in-down">


        <a href="{{ route('admin.laporanAbsensi') }}" wire:navigate
            class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors mb-2">
            <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 shadow-sm flex items-center justify-center">
                <i class="ri-arrow-left-line"></i>
            </div>
            Kembali ke Daftar Kelas
        </a>


        <div
            class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-white p-5 rounded-2xl shadow-sm border border-slate-100 gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-2xl shadow-sm border border-indigo-100">
                    <i class="ri-building-4-fill"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-800 leading-tight">
                        Kelas {{ $kelas->nama_kelas }}
                    </h1>
                    <p class="text-sm font-bold text-indigo-600 mt-0.5">Analitik Performa & Kedisiplinan</p>
                </div>
            </div>


            <form wire:submit="filterData"
                class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto bg-slate-50 p-2 sm:p-3 rounded-xl border border-slate-200">
                <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                    <input type="date" wire:model="start_date"
                        class="w-full sm:w-auto bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    <span class="hidden sm:block text-slate-400 font-bold">-</span>
                    <input type="date" wire:model="end_date"
                        class="w-full sm:w-auto bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                </div>
                <button type="submit"
                    class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center justify-center gap-2 shrink-0">
                    <i class="ri-filter-3-line"></i> Terapkan
                </button>
            </form>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            <x-ui.card title="Kapasitas Siswa " value="{{ $this->statistikKelas['total_siswa'] }}" icon="ri-group-fill"
                color="primary" size="xs" subtitle="Di Kelas Ini" />
            <x-ui.card title="Mapel Aktif" value="{{ $this->statistikKelas['total_mapel'] }}" icon="ri-book-2-fill"
                color="info" size="xs" subtitle="Mapel" />
            <x-ui.card title="Total Sesi" value="{{ $this->statistikKelas['total_sesi'] }}"
                icon="ri-calendar-check-fill" color="success" size="xs" subtitle="Lintas Mapel" />
            <x-ui.card title="Rata-Rata Hadir" value="{{ $this->statistikKelas['rata_hadir'] }}%"
                icon="ri-pie-chart-2-fill" color="warning" size="xs" subtitle="Akumulasi Kelas" />
        </div>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <div class="lg:col-span-2 bg-white p-5 md:p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="mb-6 border-b border-slate-100 pb-4">
                    <h3 class="font-extrabold text-slate-800 text-lg"><i
                            class="ri-bar-chart-horizontal-fill text-indigo-500 mr-1"></i> Rapor Kinerja per Mata
                        Pelajaran</h3>
                    <p class="text-xs font-medium text-slate-400 mt-1">Daftar pelajaran diurutkan dari tingkat kehadiran
                        terendah (Butuh Perhatian).</p>
                </div>

                @if ($this->statistikKelas['total_mapel'] > 0)
                    <div class="space-y-5">
                        @foreach ($this->daftarMapel as $mapel)
                            @php
                                $persen = $mapel->rata_hadir;
                                $colorTheme = $persen >= 80 ? 'emerald' : ($persen >= 50 ? 'amber' : 'rose');
                            @endphp

                            <div
                                class="bg-slate-50 p-4 rounded-xl border border-slate-200 hover:border-slate-300 transition-all">
                                <div class="flex justify-between items-end mb-2">
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-sm mb-0.5">
                                            {{ Str::title($mapel->mapel->nama_mapel) }}</h4>
                                        <p class="text-[11px] font-bold text-slate-500 flex items-center gap-1">
                                            <i class="ri-user-star-line text-indigo-400"></i>
                                            {{ $mapel->guru->user->name ?? '-' }}
                                        </p>
                                    </div>
                                    <div class="text-right flex flex-col items-end gap-1.5">
                                        <span
                                            class="text-xs font-bold text-slate-600 bg-white border border-slate-200 px-2 py-1 rounded-md shadow-sm">
                                            {{ $mapel->total_sesi }} Sesi
                                        </span>

                                        <button
                                            wire:click="exportExcelMapel({{ $mapel->mapel_id }}, {{ $mapel->guru_id }}, '{{ addslashes($mapel->mapel->nama_mapel) }}')"
                                            class="text-[10px] font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-500 hover:text-white border border-emerald-200 px-2 py-1 rounded-md transition-colors flex items-center gap-1 cursor-pointer">
                                            <i class="ri-file-excel-2-line"></i> Export
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div
                                        class="flex justify-between text-[10px] font-black uppercase tracking-wider mb-1">
                                        <span class="text-slate-400">Rata-Rata Kehadiran</span>
                                        <span class="text-{{ $colorTheme }}-600">{{ $persen }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2.5">
                                        <div class="bg-{{ $colorTheme }}-500 h-2.5 rounded-full transition-all duration-1000"
                                            style="width: {{ $persen }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-2 pt-4 border-t border-slate-100">
                        {{ $this->daftarMapel->links('components.ui.custom-pagination') }}
                    </div>
                @else
                    <div
                        class="flex flex-col items-center justify-center py-16 text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <i class="ri-book-2-line text-5xl mb-3 opacity-50"></i>
                        <p class="font-bold text-slate-500">Belum Ada Aktivitas Mapel</p>
                        <p class="text-xs font-medium mt-1">Guru-guru belum membuat sesi absen di kelas ini.</p>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-5 rounded-2xl border border-rose-100 shadow-sm flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <div
                            class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center animate-pulse shrink-0">
                            <i class="ri-focus-3-line"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-rose-600 leading-tight">10 Daftar Siswa dengan Alpa Tinggi
                            </h3>
                        </div>
                    </div>
                    <p class="text-[11px] font-medium text-slate-500 mb-5 border-b border-slate-100 pb-3">Siswa dengan
                        rekor Alpa terbanyak (≥ 3 kali)</p>

                    <ul class="space-y-3">
                        @forelse($this->siswaKritisKelas as $index => $kritis)
                            <li
                                class="flex justify-between items-center bg-white border border-slate-100 p-2.5 rounded-xl shadow-sm hover:bg-slate-50 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-black text-xs group-hover:bg-rose-100 group-hover:text-rose-600 transition-colors shrink-0">
                                        {{ substr($kritis->siswa->user->name ?? 'A', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-xs text-slate-800 leading-tight">
                                            {{ $kritis->siswa->user->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <span
                                    class="{{ $index == 0 ? 'bg-rose-600' : 'bg-orange-500' }} text-white font-bold px-2 py-1 rounded-md text-[10px] shadow-sm shrink-0 uppercase tracking-wide">
                                    {{ $kritis->total_alpa }} Alpa
                                </span>
                            </li>
                        @empty
                            <div
                                class="flex flex-col items-center justify-center text-emerald-500 py-6 bg-emerald-50 rounded-xl border border-emerald-100">
                                <i class="ri-shield-check-fill text-4xl mb-2 opacity-80"></i>
                                <p class="font-bold text-sm text-center leading-tight">Aman<br><span
                                        class="text-xs font-medium opacity-80">Tidak ditemukan siswa dengan alpa >= 3x
                                        di kelas
                                        ini.</span></p>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
