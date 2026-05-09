<div>
    <div class="max-w-7xl mx-auto space-y-5 animate-fade-in-down">


        <a href="{{ route('guru.laporanAbsensi') }}" wire:navigate
            class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-sky-600 transition-colors mb-2">
            <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 shadow-sm flex items-center justify-center">
                <i class="ri-arrow-left-line"></i>
            </div>
            Kembali ke Daftar Kelas
        </a>


        <div
            class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-white p-5 rounded-2xl shadow-sm border border-slate-100 gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-2xl shadow-sm border border-sky-100">
                    <i class="ri-dashboard-line"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-800 leading-tight">
                        {{ Str::title($guruMapel->mapel->nama_mapel) }}
                        @if ($guruMapel->mapel->kode_mapel)
                            <span
                                class="text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200 align-middle ml-1">{{ $guruMapel->mapel->kode_mapel }}</span>
                        @endif
                    </h1>
                    <p class="text-sm font-bold text-sky-600 mt-0.5">Kelas {{ $guruMapel->kelas->nama_kelas }}</p>
                </div>
            </div>

            <!-- Form Filter Tanggal & Export -->
            <form wire:submit="filterData"
                class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto bg-slate-50 p-3 rounded-xl border border-slate-200">

                <!-- Wrapper Input Tanggal -->
                <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                    <input type="date" wire:model="start_date"
                        class="w-full sm:w-auto bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 outline-none focus:ring-2 focus:ring-sky-500 shadow-sm">

                    <span class="hidden sm:block text-slate-400 font-bold">-</span>

                    <input type="date" wire:model="end_date"
                        class="w-full sm:w-auto bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 outline-none focus:ring-2 focus:ring-sky-500 shadow-sm">
                </div>


                <div class="flex items-center gap-2 w-full sm:w-auto">

                    <x-ui.button type="submit" class="w-full sm:w-auto" icon="ri-filter-3-line" color="white">
                        Terapkan
                    </x-ui.button>

                    <x-ui.button wire:click="exportExcel" wire:target="exportExcel" class="w-full sm:w-auto"
                        icon="ri-file-excel-2-line" color="primary" loadingText="Menyiapkan File ">
                        Export
                    </x-ui.button>
                </div>
            </form>
        </div>


        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            <x-ui.card title="Rata-Rata Hadir" value="{{ $this->statistik['rata_hadir'] }}%" icon="ri-line-chart-fill"
                color="primary" size="xs" subtitle="Dari {{ $this->statistik['sesi'] }} Sesi" />
            <x-ui.card title="Total Izin" value="{{ $this->statistik['izin'] }}" icon="ri-mail-send-fill" color="info"
                size="xs" subtitle="Kasus" />
            <x-ui.card title="Total Sakit" value="{{ $this->statistik['sakit'] }}" icon="ri-hospital-fill"
                color="warning" size="xs" subtitle="Kasus" />
            <x-ui.card title="Total Alpa" value="{{ $this->statistik['alpa'] }}" icon="ri-close-circle-fill"
                color="danger" size="xs" subtitle="Kasus" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">


            <div class="lg:col-span-2 bg-white p-5 md:p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="mb-6 flex justify-between items-end border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-lg"><i
                                class="ri-history-line text-sky-500 mr-1"></i> Riwayat Pertemuan</h3>
                        <p class="text-xs font-medium text-slate-400 mt-1">Daftar sesi absensi diurutkan dari yang
                            terbaru.</p>
                    </div>
                    <div class="text-right hidden sm:block">
                        <span
                            class="bg-slate-100 text-slate-600 font-bold text-xs px-3 py-1.5 rounded-lg">{{ $this->statistik['sesi'] }}
                            Sesi Ditemukan</span>
                    </div>
                </div>

                @if ($this->statistik['sesi'] > 0)

                    <div class="relative border-l-2 border-slate-200 ml-3 md:ml-4 space-y-6 pb-4">
                        @foreach ($this->dataSesiPaginated as $index => $sesi)
                            @php
                                $totalSiswa = $sesi->absensis->count();
                                $hadir = $sesi->absensis->whereIn('status', ['hadir', 'terlambat'])->count();
                                $persen = $totalSiswa > 0 ? round(($hadir / $totalSiswa) * 100) : 0;

                                $colorTheme = $persen >= 80 ? 'emerald' : ($persen >= 50 ? 'amber' : 'rose');

                                $nomorPertemuan =
                                    $this->statistik['sesi'] - ($this->dataSesiPaginated->firstItem() + $index) + 1;
                            @endphp

                            <div class="relative pl-6 sm:pl-8 group">

                                <div
                                    class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full border-2 border-white bg-{{ $colorTheme }}-500 shadow-sm group-hover:scale-125 transition-transform">
                                </div>


                                <div
                                    class="bg-slate-50 hover:bg-white rounded-xl p-4 border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Pertemuan
                                                {{ $nomorPertemuan }}</span>

                                            @if ($this->dataSesiPaginated->currentPage() == 1 && $index == 0)
                                                <span
                                                    class="bg-sky-100 text-sky-600 text-[9px] font-bold px-1.5 py-0.5 rounded">TERBARU</span>
                                            @endif
                                        </div>
                                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                                            <i class="ri-calendar-2-fill text-slate-400"></i>
                                            {{ \Carbon\Carbon::parse($sesi->tanggal)->translatedFormat('l, d M Y') }}
                                        </h4>
                                    </div>

                                    <div
                                        class="flex items-center gap-4 bg-white sm:bg-transparent p-2 sm:p-0 rounded-lg border border-slate-100 sm:border-none">
                                        <div class="text-right">
                                            <p class="text-[10px] text-slate-400 font-bold uppercase mb-0.5">Siswa Hadir
                                            </p>
                                            <p class="font-bold text-slate-700 text-xs">{{ $hadir }} /
                                                {{ $totalSiswa }}</p>
                                        </div>
                                        <div class="w-px h-8 bg-slate-200 hidden sm:block"></div>
                                        <div class="text-right min-w-[60px]">
                                            <p class="text-[10px] text-slate-400 font-bold uppercase mb-0.5">Rasio</p>
                                            <p class="font-black text-lg text-{{ $colorTheme }}-500 leading-none">
                                                {{ $persen }}%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-2 pt-4 border-t border-slate-100">
                        {{ $this->dataSesiPaginated->links('components.ui.custom-pagination') }}
                    </div>
                @else
                    <div
                        class="flex flex-col items-center justify-center py-16 text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <i class="ri-history-line text-5xl mb-3 opacity-50"></i>
                        <p class="font-bold text-slate-500">Belum Ada Sesi</p>
                        <p class="text-xs font-medium mt-1">Tidak ada riwayat pertemuan di rentang tanggal ini.</p>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-1 space-y-6">

                <div
                    class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center text-center">
                    <h3 class="font-extrabold text-slate-800 mb-1">Skor Kehadiran Kelas</h3>
                    <p class="text-xs font-medium text-slate-500 mb-2">Rata-rata kehadiran semester ini</p>

                    <div wire:ignore class="w-full flex justify-center -my-4">
                        <div id="radial-chart"></div>
                    </div>

                    <p
                        class="text-xs font-semibold text-slate-400 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                        <i class="ri-information-fill text-sky-500"></i> Target minimal sekolah: 80%
                    </p>
                </div>


                <div class="bg-white p-5 rounded-2xl border border-rose-100 shadow-sm flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <div
                            class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center animate-pulse shrink-0">
                            <i class="ri-alarm-warning-fill"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-rose-600">5 Daftar Siswa dengan Alpa Tinggi</h3>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-slate-500 mb-5">Siswa dengan rekor Alpa terbanyak (≥ 3 kali).
                    </p>

                    <ul class="space-y-3">
                        @forelse($this->siswaKritis as $index => $kritis)
                            <li
                                class="flex justify-between items-center {{ $index == 0 ? 'bg-rose-50 border-rose-200' : 'bg-orange-50 border-orange-100' }} p-3 rounded-xl border shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-white {{ $index == 0 ? 'text-rose-600' : 'text-orange-500' }} flex items-center justify-center font-black text-xs shadow-sm shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-slate-800 leading-tight">
                                            {{ $kritis->siswa->user->name ?? 'Anonim' }}</p>
                                        <p class="text-[10px] font-semibold text-slate-500">NISN:
                                            {{ $kritis->siswa->nisn ?? '-' }}</p>
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
                                <p class="font-bold text-sm">Aman</p>
                            </div>
                        @endforelse
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('livewire:initialized', () => {
        let persentase = @js($this->statistik['rata_hadir']);

        const getColor = (val) => val >= 80 ? '#10b981' : (val >= 50 ? '#f59e0b' : '#f43f5e');

        let options = {
            series: [persentase],
            chart: {
                type: 'radialBar',
                height: 280,
                fontFamily: 'inherit',
            },
            plotOptions: {
                radialBar: {
                    hollow: {
                        size: '65%'
                    },
                    track: {
                        background: '#f1f5f9',
                        strokeWidth: '100%',
                        margin: 0
                    },
                    dataLabels: {
                        name: {
                            show: false
                        },
                        value: {
                            fontSize: '36px',
                            fontWeight: 900,
                            color: '#1e293b',
                            offsetY: 12,
                            formatter: function(val) {
                                return val + "%"
                            }
                        }
                    }
                }
            },
            colors: [getColor(persentase)],
            stroke: {
                lineCap: 'round'
            }
        };

        let chartElement = document.querySelector("#radial-chart");
        let chart;

        if (chartElement) {
            chart = new ApexCharts(chartElement, options);
            chart.render();
        }


        Livewire.on('update-chart', (event) => {
            let newPersentase = event.rata_hadir;

            if (chart) {
                chart.updateOptions({
                    colors: [getColor(newPersentase)]
                });
                chart.updateSeries([newPersentase]);
            }
        });
    });
</script>
