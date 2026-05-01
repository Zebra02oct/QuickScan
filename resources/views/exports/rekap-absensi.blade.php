<table>
    <!-- KOP EXCEL -->
    <tr>
        <td colspan="5"><b>REKAPITULASI KEHADIRAN SISWA</b></td>
    </tr>
    <tr>
        <td colspan="5">Mata Pelajaran : {{ $mapel->nama_mapel ?? '-' }}</td>
    </tr>
    <tr>
        <td colspan="5">Kelas : {{ $kelas->nama_kelas ?? '-' }}</td>
    </tr>
    <tr>
        <td colspan="5">Guru Pengampu : {{ $guru->user->name ?? 'Bapak/Ibu Guru' }}</td>
    </tr>
    <tr>
        <td colspan="5">Tanggal Cetak : {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
    </tr>
    <tr></tr> <!-- Baris kosong sebagai spasi -->

    <!-- HEADER TABEL -->
    <thead>
        <tr>
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center;">No</th>
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center;">NIS/NISN</th>
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center;">Nama Lengkap Siswa
            </th>

            <!-- KOLOM DINAMIS (TANGGAL PERTEMUAN) -->
            @if ($daftarSesi->count() > 0)
                <th colspan="{{ $daftarSesi->count() }}"
                    style="border: 1px solid #000; font-weight: bold; text-align: center;">Pertemuan (Tanggal)</th>
            @else
                <th rowspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center;">Belum ada
                    pertemuan</th>
            @endif

            <th colspan="5" style="border: 1px solid #000; font-weight: bold; text-align: center;">Rekapitulasi</th>
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center;">% Hadir</th>
        </tr>
        <tr>
            <!-- Sub-Header Tanggal -->
            @foreach ($daftarSesi as $sesi)
                <th style="border: 1px solid #000; font-weight: bold; text-align: center;">
                    {{ \Carbon\Carbon::parse($sesi->tanggal)->format('d/m') }}
                </th>
            @endforeach

            <!-- Sub-Header Rekap -->
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #dcfce7;">H</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #fef08a;">T</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #dbeafe;">I</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #ffedd5;">S</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #ffe4e6;">A</th>
        </tr>
    </thead>

    <!-- ISI DATA (LOOPING SISWA) -->
    <tbody>
        @foreach ($daftarSiswa as $index => $siswa)
            @php
                // Hitung total buat siswa ini
                $totalH = 0;
                $totalT = 0;
                $totalI = 0;
                $totalS = 0;
                $totalA = 0;
            @endphp
            <tr>
                <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000;">{{ $siswa->nisn ?? '-' }}</td>
                <td style="border: 1px solid #000;">{{ $siswa->user->name }}</td>

                <!-- Looping Absensi Sesuai Kolom Sesi -->
                <!-- Looping Absensi Sesuai Kolom Sesi -->
                @foreach ($daftarSesi as $sesi)
                    @php
                        // BUNGKUS PAKE collect() BIAR KEBAL DARI NULL BRAY!
                        $riwayatAbsen = collect($siswa->absensis ?? []);
                        $absen = $riwayatAbsen->where('sesi_absensi_id', $sesi->id)->first();

                        $statusKode = '-';

                        if ($absen) {
                            // Karena kadang data object/array perlakuannya beda pas di-collect
                            // Kita pakai bantuan optional() atau jadi array standar
                            $status = is_array($absen) ? $absen['status'] : $absen->status;

                            if ($status == 'hadir') {
                                $statusKode = 'H';
                                $totalH++;
                            } elseif ($status == 'terlambat') {
                                $statusKode = 'T';
                                $totalT++;
                            } elseif ($status == 'izin') {
                                $statusKode = 'I';
                                $totalI++;
                            } elseif ($status == 'sakit') {
                                $statusKode = 'S';
                                $totalS++;
                            } elseif ($status == 'alpa') {
                                $statusKode = 'A';
                                $totalA++;
                            }
                        } else {
                            // Kalau nggak ada data absen sama sekali, default Alpa
                            $statusKode = 'A';
                            $totalA++;
                        }
                    @endphp
                    <td style="border: 1px solid #000; text-align: center;">{{ $statusKode }}</td>
                @endforeach

                <!-- Cetak Total -->
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $totalH }}</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $totalT }}</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $totalI }}</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $totalS }}</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $totalA }}</td>

                <!-- Hitung Persentase Hadir -->
                @php
                    $totalPertemuan = $daftarSesi->count();
                    $persentase = $totalPertemuan > 0 ? round(($totalH / $totalPertemuan) * 100) : 0;
                @endphp
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $persentase }}%</td>
            </tr>
        @endforeach
    </tbody>
</table>
