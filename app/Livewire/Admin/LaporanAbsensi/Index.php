<?php

namespace App\Livewire\Admin\LaporanAbsensi;

use App\Models\Absensi;
use App\Models\GuruMapel;
use App\Models\Kelas;
use App\Models\SesiAbsensi;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    #[Layout('layouts.app')]
    #[Title('Dashboard Laporan Admin')]

    public $filter_tahun;
    public $filter_semester;
    public $daftarTahun = [];

    public function mount()
    {
        $now = Carbon::now();
        $this->filter_tahun = $now->year;
        $this->filter_semester = $now->month >= 7 ? 'ganjil' : 'genap';

         $this->daftarTahun = SesiAbsensi::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

             if (empty($this->daftarTahun)) {
            $this->daftarTahun = [$now->year];
        }

        
    }

    public function getRentangTanggal()
    {
        $tahun = $this->filter_tahun;

        if ($this->filter_semester == 'ganjil') {
            return [
                Carbon::create($tahun, 7, 1)->startOfDay(),
                Carbon::create($tahun, 12, 31)->endOfDay()
            ];
        } else {
            return [
                Carbon::create($tahun, 1, 1)->startOfDay(),
                Carbon::create($tahun, 6, 30)->endOfDay()
            ];
        }
    }

 
    #[Computed]
    public function daftarKelas()
    {
        [$startDate, $endDate] = $this->getRentangTanggal();

        $kelasAktif = Kelas::whereHas('guruMapel.sesiAbsensis', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        })->get();

        foreach ($kelasAktif as $kelas) {
            $guruMapelIds = GuruMapel::where('kelas_id', $kelas->id)->pluck('id');
            
            $kelas->total_sesi = SesiAbsensi::whereIn('guru_mapel_id', $guruMapelIds)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->count();

            $kelas->total_mapel = GuruMapel::where('kelas_id', $kelas->id)
                ->whereHas('sesiAbsensis', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                })->count();
        }

        return $kelasAktif->sortByDesc('total_sesi')->values();
    }

  
#[Computed]
    public function statistikGlobal()
    {
        [$startDate, $endDate] = $this->getRentangTanggal();

        $sesiIds = SesiAbsensi::whereBetween('tanggal', [$startDate, $endDate])->pluck('id')->toArray();

      
        if (empty($sesiIds)) {
            return [
                'total_kelas' => 0,
                'total_sesi'  => 0,
                'rata_hadir'  => 0,
                'total_mapel' => 0, 
            ];
        }

     
        $totalMapel = GuruMapel::whereHas('sesiAbsensis', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        })->distinct('mapel_id')->count('mapel_id');

    
        $totalKehadiran = Absensi::whereIn('sesi_absensi_id', $sesiIds)->count();
        $totalHadir = Absensi::whereIn('sesi_absensi_id', $sesiIds)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();
            
        $rataHadir = $totalKehadiran > 0 ? round(($totalHadir / $totalKehadiran) * 100) : 0;

        return [
            'total_kelas' => $this->daftarKelas->count(), 
            'total_sesi'  => count($sesiIds),
            'rata_hadir'  => $rataHadir,
            'total_mapel' => $totalMapel,
        ];
    }

    public function render()
    {
        return view('livewire.admin.laporan-absensi.index');
    }
}