<?php

namespace App\Livewire\Guru\LaporanAbsensi;

use App\Models\GuruMapel;
use App\Models\SesiAbsensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    #[Layout('layouts.app')]
    #[Title('Laporan Absensi')]

    public $filter_tahun;
    public $filter_semester;
    public $daftarTahun = [];

    public function mount()
    {
        $now = Carbon::now();
        $this->filter_tahun = (string) $now->year;
        
        $this->filter_semester = ($now->month >= 7) ? 'ganjil' : 'genap';

     
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
                \Carbon\Carbon::create($tahun, 7, 1)->startOfDay(),
                \Carbon\Carbon::create($tahun, 12, 31)->endOfDay()
            ];
        } else {
            return [
                \Carbon\Carbon::create($tahun, 1, 1)->startOfDay(),
                \Carbon\Carbon::create($tahun, 6, 30)->endOfDay()
            ];
        }
    }

    #[Computed]
    public function daftarKartu()
    {
        [$startDate, $endDate] = $this->getRentangTanggal();
        $guruId = Auth::user()->guru->id;

        return GuruMapel::with(['kelas', 'mapel'])
            ->where('guru_id', $guruId)
            ->whereHas('sesiAbsensis', function($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            })
      
            ->withCount(['sesiAbsensis as total_pertemuan' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }])
          
            ->withMin(['sesiAbsensis as tanggal_pertama' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }], 'tanggal')
          
            ->withMax(['sesiAbsensis as tanggal_terakhir' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }], 'tanggal')
            ->get();
    }

 #[Computed]
    public function statistikGlobal()
    {
        $kartus = $this->daftarKartu;
        $totalSesi = $kartus->sum('total_pertemuan');
        
        $guruMapelIds = $kartus->pluck('id')->toArray();
        [$startDate, $endDate] = $this->getRentangTanggal();
        
       
        $sesiIds = \App\Models\SesiAbsensi::whereIn('guru_mapel_id', $guruMapelIds)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->pluck('id')->toArray();
            
      
        if (empty($sesiIds)) {
            return [
                'total_kelas' => $kartus->count(),
                'total_sesi'  => $totalSesi,
                'rata_hadir'  => 0,
                'total_siswa' => 0 
            ];
        }

     
        $totalSiswa = \App\Models\Absensi::whereIn('sesi_absensi_id', $sesiIds)
            ->distinct('siswa_id')
            ->count('siswa_id');

    
        $totalKehadiran = \App\Models\Absensi::whereIn('sesi_absensi_id', $sesiIds)->count();
        
        $totalHadir = \App\Models\Absensi::whereIn('sesi_absensi_id', $sesiIds)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();
            
        $rataHadir = $totalKehadiran > 0 ? round(($totalHadir / $totalKehadiran) * 100) : 0;

        return [
            'total_kelas' => $kartus->count(),
            'total_sesi'  => $totalSesi,
            'rata_hadir'  => $rataHadir,
            'total_siswa' => $totalSiswa
        ];
    }

    public function render()
    {
        return view('livewire.guru.laporan-absensi.index');
    }
}