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

 
    protected function getRentangTanggal()
    {
        if ($this->filter_semester == 'ganjil') {
            return [
                $this->filter_tahun . '-07-01', 
                $this->filter_tahun . '-12-31'
            ];
        } else {
            return [
                $this->filter_tahun . '-01-01', 
                $this->filter_tahun . '-06-30'
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
        
        return [
            'total_kelas' => $kartus->count(),
            'total_sesi'  => $totalSesi,
            'rata_hadir'  => 0, 
            'total_siswa' => 0 
        ];
    }

    public function render()
    {
        return view('livewire.guru.laporan-absensi.index');
    }
}