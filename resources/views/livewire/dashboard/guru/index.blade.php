<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 mt-6">

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.500ms
                class="p-4 mb-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3 text-rose-700">
                    <i class="ri-error-warning-fill text-2xl"></i>
                    <p class="text-sm font-bold">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-rose-400 hover:text-rose-600 transition-colors">
                    <i class="ri-close-line text-xl font-bold"></i>
                </button>
            </div>
        @endif

        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.500ms
                class="p-4 mb-4 bg-green-50 border border-green-200 rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3 text-green-700">
                    <i class="ri-checkbox-circle-fill text-2xl"></i>
                    <p class="text-sm font-bold">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-400 hover:text-green-600 transition-colors">
                    <i class="ri-close-line text-xl font-bold"></i>
                </button>
            </div>
        @endif

    </div>

    <div class="max-w-7xl mx-auto space-y-6 animate-fade-in-down">

        <div
            class="relative overflow-hidden bg-gradient-to-br from-sky-400 via-sky-500 to-blue-600 rounded-3xl p-6 md:p-10 shadow-lg border border-sky-300 text-white flex items-center">
            <div class="absolute -top-24 -right-12 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 right-40 w-32 h-32 bg-sky-200 opacity-20 rounded-full blur-xl"></div>

            <div
                class="absolute right-4 md:right-12 top-1/2 transform -translate-y-1/2 text-white/10 pointer-events-none">
                <i class="ri-mickey-line text-[120px] md:text-[160px]"></i>
            </div>

            <div class="relative z-10 w-full md:w-3/4">
                <div
                    class="flex items-center gap-2 text-sky-100 font-bold text-xs md:text-sm mb-2 bg-black/10 w-fit px-3 py-1 rounded-full backdrop-blur-sm border border-white/10">
                    <i class="ri-calendar-check-fill"></i>
                    <span>{{ $this->welcomeData['tanggal'] }}</span>
                </div>

                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-3 italic">
                    {{ $this->welcomeData['sapaan'] }}, {{ Str::title(Auth::user()->name) }}
                </h1>

                <p class="text-sky-50 text-sm md:text-base leading-relaxed font-medium">
                    Bulan ini Anda telah menyelesaikan <span
                        class="text-white font-black text-lg underline decoration-white/50 underline-offset-4 px-1">{{ $this->welcomeData['total_sesi_bulan_ini'] }}
                        sesi</span> absensi.
                    Terakhir Anda mengajar di kelas <span
                        class="text-white font-black italic">{{ $this->welcomeData['sesi_terakhir'] }}</span>.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-ui.card title="Sesi Absensi" value="{{ $this->statsCards['sesi_hari_ini'] }}" icon="ri-book-open-fill"
                color="primary" size="xs" subtitle="Hari Ini" />

            <x-ui.card title="Sesi Absensi" value="{{ $this->statsCards['total_sesi_bulan'] }}" icon="ri-history-fill"
                color="info" size="xs" subtitle="Bulan Ini" />

            <x-ui.card title="Persentase Bulan Ini" value="{{ $this->statsCards['rata_hadir'] }}%"
                icon="ri-line-chart-fill" color="success" size="xs" subtitle="Tingkat Kehadiran" />

            <x-ui.card title="Alpa Hari Ini" value="{{ $this->statsCards['alpa_hari_ini'] }}"
                icon="ri-error-warning-fill" color="danger" size="xs" subtitle="Siswa" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

            <div class="bg-white p-5 md:p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="mb-4">
                    <h3 class="font-extrabold text-slate-800 text-lg flex items-center gap-2">
                        <i class="ri-line-chart-line text-indigo-500"></i> Statistik Kehadiran 7 Hari Terakhir
                    </h3>
                    <p class="text-xs font-medium text-slate-400 mt-1">Persentase siswa hadir dan terlambat.</p>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="guruTrendChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-5 md:p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                <div class="mb-4">
                    <h3 class="font-extrabold text-slate-800 text-lg flex items-center gap-2">
                        <i class="ri-pie-chart-2-fill text-rose-500"></i> Distribusi Status Kelas Anda
                    </h3>
                    <p class="text-xs font-medium text-slate-400 mt-1">Rincian Status absensi bulan ini.</p>
                </div>
                <div class="relative flex-1 w-full flex items-center justify-center min-h-[250px]">
                    <canvas id="guruDistribusiChart"></canvas>
                </div>
            </div>

        </div>
    </div>
    @script
        <script>
            document.addEventListener('livewire:navigated', () => {


                const elTrend = document.getElementById('guruTrendChart');
                const elDistribusiGuru = document.getElementById('guruDistribusiChart');


                if (!elTrend || !elDistribusiGuru) return;


                let oldTrendChart = Chart.getChart(elTrend);
                if (oldTrendChart) oldTrendChart.destroy();

                let oldDistribusiChart = Chart.getChart(elDistribusiGuru);
                if (oldDistribusiChart) oldDistribusiChart.destroy();


                const trendData = @json($this->trendKehadiran);

                new Chart(elTrend, {
                    type: 'line',
                    data: {
                        labels: trendData.labels,
                        datasets: [{
                            label: 'Kehadiran (%)',
                            data: trendData.data,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#6366f1',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: v => v + '%'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 12,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        let det = trendData.details[context.dataIndex];
                                        if (det.total === 0) return 'Tidak ada aktivitas absen';
                                        return [
                                            'Persentase: ' + context.parsed.y + '%',
                                            'Hadir/Terlambat: ' + det.hadir + ' Riwayat',
                                            'Total Absen: ' + det.total + ' Riwayat'
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });


                const distribusiGuruData = @json($this->distribusiAbsenGuru);

                new Chart(elDistribusiGuru, {
                    type: 'doughnut',
                    data: {
                        labels: distribusiGuruData.labels,
                        datasets: [{
                            data: distribusiGuruData.data,
                            backgroundColor: [
                                '#10b981',
                                '#0ea5e9',
                                '#f59e0b',
                                '#8b5cf6',
                                '#ef4444'
                            ],
                            borderWidth: 0,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 12,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + context.label + ': ' + context.parsed;
                                    }
                                }
                            }
                        }
                    }
                });

            });
        </script>
    @endscript
</div>
