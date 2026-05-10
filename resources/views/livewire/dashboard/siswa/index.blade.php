<div>
    <div class="max-w-7xl mx-auto space-y-6 animate-fade-in-down">

        <div
            class="relative overflow-hidden bg-gradient-to-br from-sky-400 via-sky-500 to-blue-600 rounded-3xl p-6 md:p-10 shadow-lg border border-sky-300 text-white">
            <div class="absolute -top-24 -right-12 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 right-40 w-32 h-32 bg-sky-200 opacity-20 rounded-full blur-xl"></div>

            <div
                class="absolute right-6 md:right-16 top-1/2 transform -translate-y-1/2 text-white/10 pointer-events-none">
                <i class="ri-user-smile-line text-[120px] md:text-[150px]"></i>
            </div>

            <div class="relative z-10 w-full md:w-3/4">
                <div
                    class="flex items-center gap-2 text-sky-100 font-bold text-xs md:text-sm mb-2 bg-black/10 w-fit px-3 py-1 rounded-full backdrop-blur-sm border border-white/10">
                    <i class="ri-calendar-check-fill"></i>
                    <span>{{ $this->welcomeData['tanggal'] }}</span>
                </div>

                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-3">
                    {{ $this->welcomeData['sapaan'] }}, {{ $this->welcomeData['nama'] }}!
                </h1>

                <p class="text-sky-50 text-sm md:text-base leading-relaxed font-medium">
                    @if ($this->welcomeData['tipe_pesan'] == 'warning')
                        <span class="bg-yellow-400 text-blue-900 px-2 py-0.5 rounded font-bold">Peringatan:</span>
                    @endif
                    {{ $this->welcomeData['pesan'] }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-ui.card title="Kehadiran" value="{{ $this->statsCards['persentase'] }}%" icon="ri-bar-chart-fill"
                color="{{ $this->statsCards['persentase'] < 75 ? 'danger' : 'success' }}" size="xs"
                subtitle="Rata-rata Total" />

            <x-ui.card title="Total Alpa" value="{{ $this->statsCards['total_alpa'] }}" icon="ri-error-warning-fill"
                color="danger" size="xs" subtitle="Tanpa Keterangan" />

            <x-ui.card title="Izin & Sakit" value="{{ $this->statsCards['total_izin_sakit'] }}" icon="ri-mickey-fill"
                color="warning" size="xs" subtitle="Total " />

            <x-ui.card title="Total Absen" value="{{ $this->statsCards['total_sesi'] }}" icon="ri-book-3-fill"
                color="primary" size="xs" subtitle="Pertemuan" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8 mb-6">

            <div class="bg-white p-5 md:p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                <div class="mb-4">
                    <h3 class="font-extrabold text-slate-800 text-lg flex items-center gap-2">
                        <i class="ri-pie-chart-2-fill text-indigo-500"></i> Distribusi Kehadiranku
                    </h3>
                    <p class="text-xs font-medium text-slate-400 mt-1">Rincian seluruh status absensimu.</p>
                </div>
                <div class="relative flex-1 w-full flex items-center justify-center min-h-[250px]">
                    <canvas id="siswaDistribusiChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-5 md:p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                <div class="mb-4">
                    <h3 class="font-extrabold text-slate-800 text-lg flex items-center gap-2">
                        <i class="ri-bar-chart-horizontal-fill text-emerald-500"></i> Kehadiran Per Mapel
                    </h3>
                    <p class="text-xs font-medium text-slate-400 mt-1">Persentase kehadiranmu di tiap mata pelajaran.
                    </p>
                </div>
                <div class="relative flex-1 w-full min-h-[250px]">
                    <canvas id="siswaMapelChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    @script
        <script>
            document.addEventListener('livewire:navigated', () => {


                const elDistribusi = document.getElementById('siswaDistribusiChart');
                const elMapel = document.getElementById('siswaMapelChart');


                if (!elDistribusi || !elMapel) return;


                let oldDistChart = Chart.getChart(elDistribusi);
                if (oldDistChart) oldDistChart.destroy();

                let oldMapelChart = Chart.getChart(elMapel);
                if (oldMapelChart) oldMapelChart.destroy();


                const distribusiData = @json($this->distribusiAbsenPribadi);
                const totalData = distribusiData.data.reduce((a, b) => a + b, 0);

                if (totalData === 0) {
                    const ctxDistribusi = elDistribusi.getContext('2d');
                    ctxDistribusi.font = "14px Inter";
                    ctxDistribusi.fillStyle = "#94a3b8";
                    ctxDistribusi.textAlign = "center";
                    ctxDistribusi.fillText("Belum ada data absensi untukmu.", elDistribusi.width / 2, elDistribusi
                        .height / 2);
                } else {
                    new Chart(elDistribusi, {
                        type: 'doughnut',
                        data: {
                            labels: distribusiData.labels,
                            datasets: [{
                                data: distribusiData.data,
                                backgroundColor: [
                                    '#10b981',
                                    '#0ea5e9',
                                    '#f59e0b',
                                    '#8b5cf6',
                                    '#ef4444'
                                ],
                                borderWidth: 0,
                                hoverOffset: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 25,
                                        font: {
                                            size: 13,
                                            family: "'Inter', sans-serif"
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1e293b',
                                    padding: 12,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            return ' ' + context.label + ': ' + context.parsed + ' Kali';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }


                const mapelData = @json($this->raporMapel);

                new Chart(elMapel, {
                    type: 'bar',
                    data: {
                        labels: mapelData.labels,
                        datasets: [{
                            label: 'Kehadiran (%)',
                            data: mapelData.data,
                            backgroundColor: mapelData.colors,
                            borderRadius: 8,
                            barThickness: 20
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: v => v + '%'
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            y: {
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
                                callbacks: {
                                    label: function(context) {
                                        return ' Kehadiran: ' + context.parsed.x + '%';
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
