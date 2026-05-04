<?php

namespace App\Exports\Admin;

use App\Models\GuruMapel;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\SesiAbsensi;
use App\Models\Siswa;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapAbsensiExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $kelas_id, $mapel_id, $guru_id, $export_type, $start_date, $end_date;

    public function __construct($kelas_id, $mapel_id, $guru_id, $export_type, $start_date, $end_date)
    {
        $this->kelas_id = $kelas_id;
        $this->mapel_id = $mapel_id;
        $this->guru_id = $guru_id;
        $this->export_type = $export_type;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function view(): View
    {
        $guruMapel = GuruMapel::with('guru.user')
                              ->where('guru_id', $this->guru_id)
                              ->where('mapel_id', $this->mapel_id)
                              ->where('kelas_id', $this->kelas_id) 
                              ->first();

        if (!$guruMapel) {
            $daftarSesi = collect(); 
            $daftarSiswa = collect();
        } else {
            $sesiQuery = SesiAbsensi::where('guru_mapel_id', $guruMapel->id);

            if ($this->export_type === 'custom') {
                $sesiQuery->whereBetween('tanggal', [$this->start_date, $this->end_date]);
            } else {
                $sekarang = \Carbon\Carbon::now();
                $tahunIni = $sekarang->year;
                $bulanIni = $sekarang->month;

                if ($bulanIni >= 7 && $bulanIni <= 12) {
                    $mulaiSemester = \Carbon\Carbon::create($tahunIni, 7, 1)->startOfDay();
                    $akhirSemester = \Carbon\Carbon::create($tahunIni, 12, 31)->endOfDay();
                } else {
                    $mulaiSemester = \Carbon\Carbon::create($tahunIni, 1, 1)->startOfDay();
                    $akhirSemester = \Carbon\Carbon::create($tahunIni, 6, 30)->endOfDay();
                }

                $sesiQuery->whereBetween('tanggal', [$mulaiSemester, $akhirSemester]);
            }

            $daftarSesi = $sesiQuery->orderBy('tanggal', 'asc')->get();
            $sesiIds = $daftarSesi->pluck('id')->toArray();

            if (empty($sesiIds)) {
                $daftarSiswa = collect(); 
            } else {
                $daftarSiswa = Siswa::whereHas('absensis', function($query) use ($sesiIds) {
                                        $query->whereIn('sesi_absensi_id', $sesiIds);
                                    })
                                    ->with(['user', 'absensis' => function($query) use ($sesiIds) {
                                        $query->whereIn('sesi_absensi_id', $sesiIds);
                                    }])
                                    ->get()
                                    ->sortBy(function($siswa) {
                                        return $siswa->user->name ?? 'Z'; 
                                    });
            }
        }

        $kelas = Kelas::find($this->kelas_id);
        $mapel = Mapel::find($this->mapel_id);

        return view('exports.rekap-absensi', [
            'daftarSesi'  => $daftarSesi,
            'daftarSiswa' => $daftarSiswa,
            'kelas'       => $kelas,
            'mapel'       => $mapel,
            // 🔥 LEMPAR DATA GURU BERDASARKAN HASIL QUERY, BUKAN DARI AUTH!
            'guru'        => $guruMapel ? $guruMapel->guru : null 
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 14]],
            2    => ['font' => ['bold' => true]],
            3    => ['font' => ['bold' => true]],
            4    => ['font' => ['bold' => true]],
            5    => ['font' => ['bold' => true]],
        ];
    }
}