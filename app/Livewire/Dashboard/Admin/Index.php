<?php

namespace App\Livewire\Dashboard\Admin;

use App\Models\Absensi;
use App\Models\SesiAbsensi;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    #[Layout('layouts.app')]
    #[Title('Dashboard Admin')]

    #[Computed]
    public function welcomeData()
    {
        $today = Carbon::today();

        $sesiIds = SesiAbsensi::whereDate('tanggal', $today)->pluck('id');
        $totalSesi = $sesiIds->count();

        $totalSiswaDiabsen = 0;
        if ($totalSesi > 0) {
            $totalSiswaDiabsen = Absensi::whereIn('sesi_absensi_id', $sesiIds)
                ->distinct('siswa_id')
                ->count('siswa_id');
        }

        $jam = Carbon::now()->format('H');
        if ($jam >= 5 && $jam < 11) {
            $sapaan = 'Selamat Pagi';
        } elseif ($jam >= 11 && $jam < 15) {
            $sapaan = 'Selamat Siang';
        } elseif ($jam >= 15 && $jam < 18) {
            $sapaan = 'Selamat Sore';
        } else {
            $sapaan = 'Selamat Malam';
        }

        return [
            'sapaan'      => $sapaan,
            'tanggal'     => Carbon::now()->translatedFormat('l, d F Y'),
            'total_sesi'  => $totalSesi,
            'total_siswa' => $totalSiswaDiabsen,
        ];
    }

    #[Computed]
    public function statsCards()
    {
        $today = \Carbon\Carbon::today();
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
        $endOfMonth = \Carbon\Carbon::now()->endOfMonth();


        $sesiHariIni = SesiAbsensi::whereDate('tanggal', $today)->pluck('id');
        
        $rataHadirHariIni = 0;
        if ($sesiHariIni->isNotEmpty()) {
            $totalAbsenHariIni = Absensi::whereIn('sesi_absensi_id', $sesiHariIni)->count();
            $hadirHariIni = Absensi::whereIn('sesi_absensi_id', $sesiHariIni)
                ->whereIn('status', ['hadir', 'terlambat'])->count();
            $rataHadirHariIni = $totalAbsenHariIni > 0 ? round(($hadirHariIni / $totalAbsenHariIni) * 100) : 0;
        }


        $sesiBulanIni = SesiAbsensi::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->pluck('id');
        $rataHadirBulanIni = 0;
        if ($sesiBulanIni->isNotEmpty()) {
            $totalAbsenBulanIni = Absensi::whereIn('sesi_absensi_id', $sesiBulanIni)->count();
            $hadirBulanIni = Absensi::whereIn('sesi_absensi_id', $sesiBulanIni)
                ->whereIn('status', ['hadir', 'terlambat'])->count();
            $rataHadirBulanIni = $totalAbsenBulanIni > 0 ? round(($hadirBulanIni / $totalAbsenBulanIni) * 100) : 0;
        }

        $totalAlpaHariIni = 0;
        if ($sesiHariIni->isNotEmpty()) {
            $totalAlpaHariIni = Absensi::whereIn('sesi_absensi_id', $sesiHariIni)
                ->where('status', 'alpa')
                ->count();
        }

        return [
            'sesi_hari_ini'      => $sesiHariIni->count(),
            'hadir_hari_ini'     => $rataHadirHariIni,
            'hadir_bulan_ini'    => $rataHadirBulanIni,
            'alpa_hari_ini'      => $totalAlpaHariIni, 
        ];
    }

    #[Computed]
    public function trendKehadiran()
    {
        $labels = [];
        $data = [];
        $details = []; 

     
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            
            $labels[] = $date->translatedFormat('d M'); 

            $sesiIds = SesiAbsensi::whereDate('tanggal', $date)->pluck('id');
            
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

        return [
            'labels'  => $labels,
            'data'    => $data,
            'details' => $details, 
        ];
    }

    #[Computed]
    public function distribusiAbsen()
    {
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
        $endOfMonth = \Carbon\Carbon::now()->endOfMonth();

        $sesiBulanIni = SesiAbsensi::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->pluck('id');

       
        $data = [
            'hadir'     => 0,
            'terlambat' => 0,
            'sakit'     => 0,
            'izin'      => 0,
            'alpa'      => 0
        ];

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
        return view('livewire.dashboard.admin.index');
    }
}
