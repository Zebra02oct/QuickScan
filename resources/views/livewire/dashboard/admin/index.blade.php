<div>
    <div class="max-w-7xl mx-auto space-y-6 animate-fade-in-down">

        <div
            class="relative overflow-hidden bg-gradient-to-br from-sky-400 via-sky-500 to-blue-600 rounded-3xl p-6 md:p-10 shadow-lg border border-sky-300 text-white flex items-center">

            <div class="absolute -top-24 -right-12 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 right-40 w-32 h-32 bg-sky-200 opacity-20 rounded-full blur-xl"></div>

            <div
                class="absolute right-4 md:right-12 top-1/2 transform -translate-y-1/2 text-white/10 pointer-events-none">
                <i class="ri-radar-line text-[120px] md:text-[160px]"></i>
            </div>

            <div class="relative z-10 w-full md:w-3/4">
                <div
                    class="flex items-center gap-2 text-sky-100 font-bold text-xs md:text-sm mb-2 bg-black/10 w-fit px-3 py-1 rounded-full backdrop-blur-sm">
                    <i class="ri-calendar-event-fill"></i>
                    <span>{{ $this->welcomeData['tanggal'] }}</span>
                </div>

                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-3">
                    {{ $this->welcomeData['sapaan'] }}, Admin!
                </h1>

                <p class="text-sky-50 text-sm md:text-base leading-relaxed font-medium">
                    @if ($this->welcomeData['total_sesi'] > 0)
                        Hari ini tercatat ada <span
                            class="text-white font-black text-lg underline decoration-white/50 underline-offset-4 px-1">{{ $this->welcomeData['total_sesi'] }}
                            sesi</span> absensi kelas dengan total <span
                            class="text-white font-black text-lg underline decoration-white/50 underline-offset-4 px-1">{{ $this->welcomeData['total_siswa'] }}
                            siswa</span> yang sudah absen. Pantau terus kedisiplinan dan aktivitas KBM sekolah secara
                        real-time!
                    @else
                        Pantau kedisiplinan dan aktivitas KBM sekolah hari ini. Saat ini sistem standby dan siap merekam
                        aktivitas absensi yang akan berjalan secara <span
                            class="text-white font-bold underline decoration-sky-300 underline-offset-4">real-time</span>.
                    @endif
                </p>
            </div>

        </div>



        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">

            <x-ui.card title="Sesi Hari Ini" value="{{ $this->statsCards['sesi_hari_ini'] }}"
                icon="ri-calendar-check-fill" color="primary" size="xs" subtitle="Total Absensi" />

            <x-ui.card title="Persentase Hari Ini" value="{{ $this->statsCards['hadir_hari_ini'] }}%"
                icon="ri-user-follow-fill" color="success" size="xs" subtitle="Tingkat Kehadiran" />

            <x-ui.card title="Persentase Bulan Ini" value="{{ $this->statsCards['hadir_bulan_ini'] }}%"
                icon="ri-pie-chart-fill" color="warning" size="xs" subtitle="Tingkat Kehadiran" />

            <x-ui.card title="Alpa Hari Ini" value="{{ $this->statsCards['alpa_hari_ini'] }}"
                icon="ri-user-unfollow-fill" color="danger" size="xs" subtitle="Siswa" />

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

            <div class="lg:col-span-2 bg-white p-5 md:p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-lg flex items-center gap-2">
                        <i class="ri-line-chart-line text-sky-500"></i> Statistik Kehadiran 7 Hari Terakhir
                    </h3>
                    <p class="text-xs font-medium text-slate-400 mt-1">
                        Persentase siswa hadir dan terlambat.
                    </p>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="lg:col-span-1 bg-white p-5 md:p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                <div class="mb-4">
                    <h3 class="font-extrabold text-slate-800 text-lg flex items-center gap-2">
                        <i class="ri-donut-chart-fill text-indigo-500"></i> Distribusi Absen Bulan Ini
                    </h3>
                    <p class="text-xs font-medium text-slate-400 mt-1">Rincian Status absensi yang tercatat.</p>
                </div>
                <div class="relative flex-1 w-full flex items-center justify-center min-h-[250px]">
                    <canvas id="distribusiChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    @script
        <script>
            document.addEventListener('livewire:navigated', () => {


                const elTrend = document.getElementById('trendChart');
                const elDistribusi = document.getElementById('distribusiChart');


                if (!elTrend || !elDistribusi) return;

                let oldTrendChart = Chart.getChart(elTrend);
                if (oldTrendChart) oldTrendChart.destroy();

                let oldDistribusiChart = Chart.getChart(elDistribusi);
                if (oldDistribusiChart) oldDistribusiChart.destroy();


                const trendData = @json($this->trendKehadiran);

                new Chart(elTrend, {
                    type: 'line',
                    data: {
                        labels: trendData.labels,
                        datasets: [{
                            label: 'Kehadiran (%)',
                            data: trendData.data,
                            borderColor: '#0ea5e9',
                            backgroundColor: 'rgba(14, 165, 233, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#0ea5e9',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
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
                                    stepSize: 20,
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                },
                                grid: {
                                    color: '#f1f5f9',
                                    drawBorder: false,
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
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
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        let index = context.dataIndex;
                                        let detail = trendData.details[index];
                                        let persen = context.parsed.y;

                                        if (detail.total === 0) {
                                            return 'Tidak ada aktivitas absen';
                                        }

                                        return [
                                            'Persentase : ' + persen + '%',
                                            'Hadir/terlambat : ' + detail.hadir + ' riwayat',
                                            'Total Absen : ' + detail.total + ' riwayat'
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });


                const distribusiData = @json($this->distribusiAbsen);

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
                                        size: 12,
                                        family: "'Inter', sans-serif"
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
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
