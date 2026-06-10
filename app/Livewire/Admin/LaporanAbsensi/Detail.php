<?php

namespace App\Livewire\Admin\LaporanAbsensi;

use App\Exports\Admin\RekapAbsensiExport; 
use App\Models\Absensi;
use App\Models\GuruMapel;
use App\Models\Kelas;
use App\Models\SesiAbsensi;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Detail extends Component
{
    use WithPagination;
    #[Layout('layouts.app')]
    #[Title('Detail Absensi Kelas - Admin')]

    public $kelas_id;
    public $kelas;
    public $start_date;
    public $end_date;

    public function mount($kelas_id)
    {
        $this->kelas_id = $kelas_id;
        $this->kelas = Kelas::findOrFail($kelas_id);
        $now = Carbon::now();
        $defaultStart = $now->month >= 7 ? $now->year . '-07-01' : $now->year . '-01-01';
        $defaultEnd = $now->month >= 7 ? $now->year . '-12-31' : $now->year . '-06-30';

        $this->start_date = request()->query('start', $defaultStart);
        $this->end_date = request()->query('end', $defaultEnd);
    }

    public function filterData()
    {
        $this->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);
       $this->resetPage();
    }

 #[Computed]
    public function statistikKelas()
    {
        $guruMapelIds = GuruMapel::where('kelas_id', $this->kelas_id)->pluck('id');
        
        $sesiIds = SesiAbsensi::whereIn('guru_mapel_id', $guruMapelIds)
            ->whereBetween('tanggal', [$this->start_date, $this->end_date])
            ->pluck('id')->toArray();

       
        if (empty($sesiIds)) {
            $estimasiSiswa = \App\Models\Siswa::where('kelas_id', $this->kelas_id)->count();
            return [
                'total_siswa' => $estimasiSiswa, 
                'total_mapel' => 0, 
                'total_sesi'  => 0, 
                'rata_hadir'  => 0,
            ];
        }

        $totalSiswa = \App\Models\Absensi::whereIn('sesi_absensi_id', $sesiIds)
            ->distinct('siswa_id')
            ->count('siswa_id');

       
        $totalMapel = GuruMapel::where('kelas_id', $this->kelas_id)
            ->whereHas('sesiAbsensis', function($q) {
                $q->whereBetween('tanggal', [$this->start_date, $this->end_date]);
            })->count();

    
        $totalKehadiran = Absensi::whereIn('sesi_absensi_id', $sesiIds)->count();
        $totalHadir = Absensi::whereIn('sesi_absensi_id', $sesiIds)
            ->whereIn('status', ['hadir', 'terlambat'])->count();

        $rataHadir = $totalKehadiran > 0 ? round(($totalHadir / $totalKehadiran) * 100) : 0;

        return [
            'total_siswa' => $totalSiswa,
            'total_mapel' => $totalMapel,
            'total_sesi'  => count($sesiIds),
            'rata_hadir'  => $rataHadir,
        ];
        }

 
 #[Computed]
    public function daftarMapel()
    {
        $mapels = GuruMapel::with(['mapel', 'guru.user'])
            ->where('kelas_id', $this->kelas_id)
            ->whereHas('sesiAbsensis', function($q) {
                $q->whereBetween('tanggal', [$this->start_date, $this->end_date]);
            })->get();

        foreach ($mapels as $mapel) {
            $sesiIds = SesiAbsensi::where('guru_mapel_id', $mapel->id)
                ->whereBetween('tanggal', [$this->start_date, $this->end_date])
                ->pluck('id')->toArray();

            $mapel->total_sesi = count($sesiIds);

            if (empty($sesiIds)) {
                $mapel->rata_hadir = 0;
            } else {
                $total = Absensi::whereIn('sesi_absensi_id', $sesiIds)->count();
                $hadir = Absensi::whereIn('sesi_absensi_id', $sesiIds)->whereIn('status', ['hadir', 'terlambat'])->count();
                $mapel->rata_hadir = $total > 0 ? round(($hadir / $total) * 100) : 0;
            }
        }

        $sortedMapels = $mapels->sortBy('rata_hadir')->values(); 

        $perPage = 6;
        $page = $this->getPage();

        return new LengthAwarePaginator(
            $sortedMapels->forPage($page, $perPage), 
            $sortedMapels->count(), 
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

   
    #[Computed]
    public function siswaKritisKelas()
    {
        $guruMapelIds = GuruMapel::where('kelas_id', $this->kelas_id)->pluck('id');
        $sesiIds = SesiAbsensi::whereIn('guru_mapel_id', $guruMapelIds)
            ->whereBetween('tanggal', [$this->start_date, $this->end_date])
            ->pluck('id');

        if ($sesiIds->isEmpty()) {
            return collect();
        }

        return Absensi::with(['siswa.user'])
            ->whereIn('sesi_absensi_id', $sesiIds)
            ->where('status', 'alpa')
            ->select('siswa_id', DB::raw('count(*) as total_alpa'))
            ->groupBy('siswa_id')
            ->having('total_alpa', '>=', 3) 
            ->orderBy('total_alpa', 'desc')
            ->take(10) 
            ->get();
    }

    public function exportExcelMapel($mapel_id, $guru_id, $nama_mapel)
    {
        $namaKelas = Str::slug($this->kelas->nama_kelas);
        $namaMapelSlug = Str::slug($nama_mapel);
        $namaFile = "Rekap_{$namaMapelSlug}_{$namaKelas}.xlsx";

   
        return Excel::download(
            new RekapAbsensiExport($this->kelas_id, $mapel_id, $guru_id, 'custom', $this->start_date, $this->end_date), 
            $namaFile
        );
    }

    public function render()
    {
        return view('livewire.admin.laporan-absensi.detail');
    }
}