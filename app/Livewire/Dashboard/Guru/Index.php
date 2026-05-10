<?php

namespace App\Livewire\Dashboard\Guru;

use App\Models\Absensi;
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
    #[Title('Dashboard Guru')]

    protected function getGuruId()
    {
        return Auth::user()->guru->id;
    }

    #[Computed]
    public function welcomeData()
    {
        $guruId = $this->getGuruId();
        $startOfMonth = Carbon::now()->startOfMonth();

    
        $sesiTerakhir = SesiAbsensi::whereHas('guruMapel', function($q) use ($guruId) {
            $q->where('guru_id', $guruId);
        })->latest('created_at')->first();

    
        $totalSesiBulanIni = SesiAbsensi::whereHas('guruMapel', function($q) use ($guruId) {
            $q->where('guru_id', $guruId);
        })->whereBetween('tanggal', [$startOfMonth, Carbon::now()])->count();

       
        $jam = Carbon::now()->format('H');
        $sapaan = ($jam < 11) ? 'Selamat Pagi' : (($jam < 15) ? 'Selamat Siang' : (($jam < 18) ? 'Selamat Sore' : 'Selamat Malam'));

        return [
            'sapaan' => $sapaan,
            'tanggal' => Carbon::now()->translatedFormat('l, d F Y'),
            'total_sesi_bulan_ini' => $totalSesiBulanIni,
            'sesi_terakhir' => $sesiTerakhir ? $sesiTerakhir->guruMapel->kelas->nama_kelas . ' (' . $sesiTerakhir->guruMapel->mapel->nama_mapel . ')' : 'Belum ada sesi',
        ];
    }

   #[Computed]
    public function statsCards()
    {
        $guruId = $this->getGuruId();
        $today = \Carbon\Carbon::today();
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();

      
        $guruMapelIds = GuruMapel::where('guru_id', $guruId)->pluck('id');


        $sesiHariIniIds = SesiAbsensi::whereIn('guru_mapel_id', $guruMapelIds)
            ->whereDate('tanggal', $today)->pluck('id');

   
        $totalSesiHariIni = $sesiHariIniIds->count();

        $totalSesiBulanIni = SesiAbsensi::whereIn('guru_mapel_id', $guruMapelIds)
            ->whereBetween('tanggal', [$startOfMonth, \Carbon\Carbon::now()])->count();

     
        $sesiBulanIniIds = SesiAbsensi::whereIn('guru_mapel_id', $guruMapelIds)
            ->whereBetween('tanggal', [$startOfMonth, \Carbon\Carbon::now()])->pluck('id');

        $rataHadir = 0;
        if ($sesiBulanIniIds->isNotEmpty()) {
            $totalAbsen = Absensi::whereIn('sesi_absensi_id', $sesiBulanIniIds)->count();
            $hadir = Absensi::whereIn('sesi_absensi_id', $sesiBulanIniIds)
                ->whereIn('status', ['hadir', 'terlambat'])->count();
            $rataHadir = $totalAbsen > 0 ? round(($hadir / $totalAbsen) * 100) : 0;
        }

       
        $alpaHariIni = 0;
        if ($sesiHariIniIds->isNotEmpty()) {
            $alpaHariIni = Absensi::whereIn('sesi_absensi_id', $sesiHariIniIds)
                ->where('status', 'alpa')->count();
        }

        return [
            'sesi_hari_ini'    => $totalSesiHariIni,
            'total_sesi_bulan' => $totalSesiBulanIni,
            'rata_hadir'       => $rataHadir,
            'alpa_hari_ini'    => $alpaHariIni,
        ];
    }

    #[Computed]
    public function trendKehadiran()
    {
        $guruId = $this->getGuruId();
        $guruMapelIds = GuruMapel::where('guru_id', $guruId)->pluck('id');
        
        $labels = [];
        $data = [];
        $details = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            $labels[] = $date->translatedFormat('d M'); 

            $sesiIds = SesiAbsensi::whereIn('guru_mapel_id', $guruMapelIds)
                ->whereDate('tanggal', $date)->pluck('id');
            
            if ($sesiIds->isEmpty()) {
                $data[] = 0; 
                $details[] = ['hadir' => 0, 'total' => 0];
            } else {
                $totalAbsen = Absensi::whereIn('sesi_absensi_id', $sesiIds)->count();
                $hadir = Absensi::whereIn('sesi_absensi_id', $sesiIds)
                    ->whereIn('status', ['hadir', 'terlambat'])->count();

                $persen = $totalAbsen > 0 ? round(($hadir / $totalAbsen) * 100) : 0;
                
                $data[] = $persen;
                $details[] = ['hadir' => $hadir, 'total' => $totalAbsen];
            }
        }

        return ['labels' => $labels, 'data' => $data, 'details' => $details];
    }

   #[Computed]
    public function distribusiAbsenGuru()
    {
        $guruId = $this->getGuruId();
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();

        $guruMapelIds = GuruMapel::where('guru_id', $guruId)->pluck('id');

        $sesiBulanIni = SesiAbsensi::whereIn('guru_mapel_id', $guruMapelIds)
            ->whereBetween('tanggal', [$startOfMonth, \Carbon\Carbon::now()])
            ->pluck('id');

        $data = ['hadir' => 0, 'terlambat' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0];

        if ($sesiBulanIni->isNotEmpty()) {
          
            $counts = Absensi::whereIn('sesi_absensi_id', $sesiBulanIni)
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $data['hadir']     = $counts['hadir'] ?? 0;
            $data['terlambat'] = $counts['terlambat'] ?? 0;
            $data['sakit']     = $counts['sakit'] ?? 0;
            $data['izin']      = $counts['izin'] ?? 0;
            $data['alpa']      = $counts['alpa'] ?? 0;
        }

        return [
            'labels' => ['Hadir', 'Terlambat', 'Sakit', 'Izin', 'Alpa'],
            'data'   => [
                $data['hadir'], 
                $data['terlambat'], 
                $data['sakit'], 
                $data['izin'], 
                $data['alpa']
            ],
        ];
    }
    
    public function render()
    {
        return view('livewire.dashboard.guru.index');
    }
}
