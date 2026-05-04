<div>

    <div class="mb-4 px-2">
        <a href="{{ route('admin.manajemenAbsensi.index') }}" wire:navigate
            class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-sky-600 transition-colors">
            <div
                class="w-8 h-8 rounded-lg bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:bg-sky-50 hover:border-sky-100 transition-all">
                <i class="ri-arrow-left-line text-lg"></i>
            </div>
            Kembali ke Daftar Sesi
        </a>
    </div>


    <div
        class="bg-white/80 backdrop-blur-xl border border-sky-100 shadow-sm rounded-2xl overflow-hidden mb-6 transition-all duration-300">

        <div
            class="p-4 sm:p-5 border-b border-sky-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3 sm:gap-4 flex-1 w-full">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class='ri-book-read-line text-xl sm:text-2xl'></i>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-gray-800 leading-tight">
                        {{ Str::title($sesi->guruMapel->mapel->nama_mapel ?? 'Mapel Tidak Diketahui') }}
                        ({{ $sesi->guruMapel->mapel->kode_mapel ?? 'Kode Mapel Tidak Diketahui' }})
                    </h2>
                    <p class="text-[11px] sm:text-sm font-medium text-sky-600 mt-1 flex items-center gap-1.5 flex-wrap">
                        <i class="ri-user-star-line"></i>
                        {{ $sesi->guruMapel->guru->user->name ?? 'Guru Tidak Diketahui' }}
                        <span class="text-gray-300 hidden sm:inline">|</span>

                        <i class="ri-building-4-line"></i>
                        {{ $sesi->guruMapel->kelas->nama_kelas ?? 'Kelas Tidak Diketahui' }}
                        <span class="text-gray-300 hidden sm:inline">|</span>

                        <i class="ri-calendar-event-line"></i>
                        {{ \Carbon\Carbon::parse($sesi->tanggal)->translatedFormat('l, d M Y') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-5">


            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <x-ui.card title="Hadir" value="{{ $this->statistik['hadir'] }}" icon="ri-checkbox-circle-fill"
                    color="success" size="xs" subtitle="Tmsk. Terlambat" />
                <x-ui.card title="Izin" value="{{ $this->statistik['izin'] }}" icon="ri-information-fill"
                    color="primary" size="xs" />
                <x-ui.card title="Sakit" value="{{ $this->statistik['sakit'] }}" icon="ri-hospital-fill"
                    color="warning" size="xs" />
                <x-ui.card title="Alpa" value="{{ $this->statistik['alpa'] }}" icon="ri-close-circle-fill"
                    color="danger" size="xs" />
            </div>


            <div class="bg-slate-50 p-4 sm:p-5 rounded-xl border border-gray-100 shadow-inner">
                <div class="flex items-center gap-3 shrink-0 mb-4">
                    <div
                        class="w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center text-gray-500">
                        <i class="ri-group-line text-lg"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Daftar Kehadiran Siswa</span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm custom-scrollbar bg-white">
                    <table class="w-full text-left text-sm min-w-[500px] sm:min-w-[600px]">
                        <thead
                            class="bg-gray-50/50 text-gray-500 font-semibold border-b border-gray-100 uppercase text-[10px] sm:text-xs">
                            <tr>
                                <th class="px-3 sm:px-4 py-3 w-10 sm:w-12 text-center">No</th>
                                <th class="px-3 sm:px-4 py-3">Nama Siswa</th>
                                <th class="px-3 sm:px-4 py-3 text-center">Waktu Scan</th>
                                <th class="px-3 sm:px-4 py-3 w-32 sm:w-40 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($this->daftarAbsen as $index => $absen)
                                <tr class="hover:bg-sky-50/30 transition-colors" wire:key="absen-{{ $absen->id }}">
                                    <td
                                        class="px-3 sm:px-4 py-3 text-center text-gray-400 font-medium text-xs sm:text-sm">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-3 sm:px-4 py-3">
                                        <div class="font-bold text-slate-700 text-xs sm:text-sm leading-tight">
                                            {{ $absen->siswa->user->name ?? 'User Name' }}</div>
                                        <div class="text-[10px] sm:text-[11px] text-gray-400 mt-1">NISN:
                                            {{ $absen->siswa->nisn ?? '-' }}</div>
                                    </td>

                                    <td class="px-3 sm:px-4 py-3 text-center text-gray-500">
                                        @if ($absen->waktu_scan)
                                            <span
                                                class="inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded text-[10px] sm:text-xs font-medium border border-gray-200">
                                                <i class="ri-time-line text-gray-400"></i>
                                                {{ \Carbon\Carbon::parse($absen->waktu_scan)->format('H:i') }} WIB
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 bg-slate-50 text-slate-500 px-2 py-1 rounded text-[10px] sm:text-xs font-medium border border-slate-200 border-dashed"
                                                title="Tidak melakukan scan via sistem">
                                                <i class="ri-qr-scan-line text-slate-400"></i> Tidak Scan
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-3 sm:px-4 py-3 text-center">
                                        <select wire:change="ubahStatus({{ $absen->id }}, $event.target.value)"
                                            class="w-full text-[11px] sm:text-xs font-bold rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 shadow-sm py-1.5 sm:py-2 cursor-pointer hover:bg-opacity-80 border-gray-300
    {{ $absen->status == 'hadir' ? 'bg-green-50 text-green-700 border-green-200' : '' }}
    {{ $absen->status == 'terlambat' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
    {{ $absen->status == 'izin' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
    {{ $absen->status == 'sakit' ? 'bg-orange-50 text-orange-700 border-orange-200' : '' }}
    {{ $absen->status == 'alpa' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}">

                                            <option value="hadir" {{ $absen->status == 'hadir' ? 'selected' : '' }}>
                                                Hadir</option>
                                            <option value="terlambat"
                                                {{ $absen->status == 'terlambat' ? 'selected' : '' }}>Terlambat
                                            </option>
                                            <option value="izin" {{ $absen->status == 'izin' ? 'selected' : '' }}>
                                                Izin</option>
                                            <option value="sakit" {{ $absen->status == 'sakit' ? 'selected' : '' }}>
                                                Sakit</option>
                                            <option value="alpa" {{ $absen->status == 'alpa' ? 'selected' : '' }}>
                                                Alpa</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
