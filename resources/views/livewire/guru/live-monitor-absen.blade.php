<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 transition-all duration-500">

    <div
        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-200 gap-4">

        <!-- INFO SESI LIVE -->
        <div class="flex items-center gap-4">
            <div class="w-4 h-4 bg-red-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(239,68,68,0.6)]"></div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-800 tracking-tight">
                    LIVE: {{ Str::title($mapel_nama) }} ({{ $kode_mapel }})
                </h1>
                <p class="text-sm sm:text-base font-bold text-sky-600 mt-0.5">
                    Kelas: {{ implode(', ', $kelas_nama) }}
                </p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto mt-2 md:mt-0">

            <!-- TOMBOL 1: BATALKAN SESI (HAPUS) -->

            <x-ui.button loadingText="Membatalkan Sesi" onclick="konfirmasiBatalSesi()" color="danger"
                icon="ri-delete-bin-line" class="w-full sm:w-auto">
                Batalkan Sesi
            </x-ui.button>

            <x-ui.button loadingText="Membatalkan Sesi" onclick="konfirmasiAkhiriSesi()" color="primary"
                icon="ri-stop-circle-line" class="w-full sm:w-auto">
                Akhiri Sesi
            </x-ui.button>


        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <div x-data="{
            waktu: sessionStorage.getItem('sisaWaktuQR') ? parseInt(sessionStorage.getItem('sisaWaktuQR')) : 10,
        
            init() {
                if (this.waktu <= 0) this.waktu = 10;
        
                setInterval(() => {
                    this.waktu--;
                    sessionStorage.setItem('sisaWaktuQR', this.waktu);
        
                    if (this.waktu <= 0) {
                        $wire.refreshQR();
                        this.waktu = 10;
                        sessionStorage.setItem('sisaWaktuQR', this.waktu); // Simpan ulang memori
                    }
                }, 1000);
            }
        }"
            class="lg:col-span-5 bg-white rounded-3xl shadow-md border border-gray-200 flex flex-col items-center justify-center p-8 text-center min-h-[500px] relative overflow-hidden">

            <div class="absolute top-0 right-0 w-32 h-32 bg-sky-50 rounded-bl-full -z-10"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-orange-50 rounded-tr-full -z-10"></div>

            <h2 class="text-3xl font-black text-slate-800 mb-2">Scan Hadir</h2>
            <p class="text-slate-500 font-medium mb-8">Buka aplikasi AbsensiKu di HP</p>

            <div class="p-4 bg-white border-4 border-slate-100 rounded-3xl shadow-xl mb-8 relative">

                <div wire:key="qr-code-{{ $current_qr_token }}"
                    class="p-2 bg-white rounded-xl shadow-sm transition-opacity duration-300">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->margin(1)->generate($current_qr_token) !!}
                </div>

                <div
                    class="absolute -bottom-6 left-0 right-0 h-2 bg-gray-100 rounded-full overflow-hidden shadow-inner">
                    <div class="h-full bg-sky-500 transition-all duration-1000 ease-linear rounded-full"
                        :style="`width: ${waktu * 10}%`"></div>
                </div>
            </div>

            <div
                class="flex items-center justify-center gap-2 text-slate-600 bg-slate-50 px-6 py-2.5 rounded-full border border-slate-200 shadow-sm">
                <i class="ri-refresh-line text-lg" :class="waktu <= 2 ? 'animate-spin text-sky-500' : ''"></i>
                <span class="font-semibold text-sm">Diperbarui dalam <strong class="text-sky-600 text-base"
                        x-text="waktu"></strong> detik</span>
            </div>
        </div>

        <div class="lg:col-span-7 bg-white rounded-3xl shadow-md border border-gray-200 overflow-hidden flex flex-col">
            <div class="p-5 border-b border-gray-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 flex items-center gap-2">
                    <i class="ri-live-line text-rose-500 text-xl animate-pulse"></i> Siswa Real-Time
                </h3>
                <div
                    class="text-xs font-bold text-slate-500 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
                    Hadir: <span
                        class="text-green-600 text-sm">{{ collect($this->listSiswa)->where('status', 'hadir')->count() }}</span>
                </div>
            </div>

            <div wire:poll.3s class="p-0 overflow-y-auto max-h-[500px] custom-scrollbar">
                <table class="w-full text-left">
                    <thead
                        class="bg-white sticky top-0 border-b border-gray-100 shadow-sm text-xs text-slate-500 uppercase font-bold z-10">
                        <tr>
                            <th class="px-5 py-4">Nama Siswa</th>
                            <th class="px-5 py-4 text-center">Kelas</th>
                            <th class="px-5 py-4 text-center">Waktu Scan</th>
                            <th class="px-5 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @foreach ($this->listSiswa as $siswa)
                            <tr wire:key="siswa-{{ $siswa['id'] }}"
                                class="transition-colors duration-300 {{ in_array($siswa['status'], ['hadir', 'terlambat']) ? 'bg-green-50/50 hover:bg-green-50' : 'hover:bg-slate-50' }}">

                                <td
                                    class="px-5 py-3 font-bold {{ in_array($siswa['status'], ['hadir', 'terlambat']) ? 'text-gray-800' : 'text-gray-500' }} flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ in_array($siswa['status'], ['hadir', 'terlambat']) ? 'bg-sky-100 text-sky-600 border border-sky-200' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                                        {{ strtoupper(substr($siswa['nama_siswa'], 0, 1)) }}
                                    </div>
                                    {{ $siswa['nama_siswa'] }}
                                </td>

                                <td class="px-5 py-3 text-center font-semibold text-slate-500 text-xs">
                                    {{ $siswa['kelas'] }}
                                </td>

                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    @if ($siswa['waktu_scan'])
                                        <!-- JIKA SISWA SUDAH SCAN -->
                                        <span
                                            class="inline-flex items-center gap-1 bg-gray-100 px-2.5 py-1.5 rounded-lg text-[10px] sm:text-xs font-medium border border-gray-200 font-mono text-slate-600 shadow-sm">
                                            <i class="ri-time-line text-gray-400"></i>
                                            {{ $siswa['waktu_scan'] }}
                                        </span>
                                    @else
                                        <!-- BADGE KHUSUS BUAT YANG NGGAK SCAN / BELUM SCAN -->
                                        <span
                                            class="inline-flex items-center gap-1 bg-slate-50 text-slate-500 px-2 py-1.5 rounded-lg text-[10px] sm:text-xs font-medium border border-slate-200 border-dashed"
                                            title="Tidak melakukan scan via sistem">
                                            <i class="ri-qr-scan-line text-slate-400"></i> Belum Scan
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-3 text-center">
                                    <select
                                        wire:change="ubahStatusSiswa({{ $siswa['id'] }}, {{ $siswa['sesi_id'] }}, $event.target.value)"
                                        class="px-2 py-1.5 rounded-md font-bold text-[10px] sm:text-xs border tracking-wide uppercase cursor-pointer outline-none transition-colors appearance-none text-center shadow-sm
                    {{ $siswa['status'] === 'menunggu' ? 'bg-slate-100 text-slate-500 border-slate-200' : '' }}
                    {{ $siswa['status'] === 'hadir' ? 'bg-green-100 text-green-700 border-green-200' : '' }}
                    {{ $siswa['status'] === 'terlambat' ? 'bg-lime-100 text-lime-700 border-lime-200' : '' }}
                    {{ $siswa['status'] === 'sakit' ? 'bg-amber-100 text-amber-700 border-amber-200' : '' }}
                    {{ $siswa['status'] === 'izin' ? 'bg-blue-100 text-blue-700 border-blue-200' : '' }}
                    {{ $siswa['status'] === 'alpa' ? 'bg-rose-100 text-rose-700 border-rose-200' : '' }}">
                                        <option value="menunggu"
                                            {{ $siswa['status'] === 'menunggu' ? 'selected' : '' }}>
                                            Menunggu</option>
                                        <option value="hadir" {{ $siswa['status'] === 'hadir' ? 'selected' : '' }}>
                                            Hadir</option>
                                        <option value="terlambat"
                                            {{ $siswa['status'] === 'terlambat' ? 'selected' : '' }}>
                                            Terlambat</option>
                                        <option value="sakit" {{ $siswa['status'] === 'sakit' ? 'selected' : '' }}>
                                            Sakit</option>
                                        <option value="izin" {{ $siswa['status'] === 'izin' ? 'selected' : '' }}>Izin
                                        </option>
                                        <option value="alpa" {{ $siswa['status'] === 'alpa' ? 'selected' : '' }}>Alpa
                                        </option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function konfirmasiBatalSesi() {
                Swal.fire({
                    title: 'Batalkan & Hapus Sesi?',
                    html: '<div class="text-sm text-slate-600">Sesi ini dan <b>semua data absen siswa</b> yang sudah masuk akan dihapus permanen.<br><br>Gunakan ini hanya jika Anda <b>salah membuat kelas/sesi</b>.</div>',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: '<i class="ri-delete-bin-line mr-1"></i> Ya, Batal & Hapus',
                    cancelButtonText: 'Kembali',
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {

                        Livewire.dispatch('eksekusi-batal-sesi');
                    }
                });
            }

            function konfirmasiAkhiriSesi() {
                Swal.fire({
                    title: 'Yakin ingin menutup sesi?',
                    text: 'Siswa yang belum absen otomatis Alpa.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: '<i class="ri-lock-2-line mr-1"></i> Ya, Kunci & Akhiri!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {

                        Livewire.dispatch('eksekusi-tutup-sesi');
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
                    }).then(() => {

                        if (info.url) {
                            window.location.href = info.url;
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
