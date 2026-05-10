<?php

namespace App\Livewire\Dashboard\Siswa;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
        #[Layout('layouts.app')]
    #[Title('Dashboard Siswa')]

    protected function getSiswaId()
    {
        return Auth::user()->siswa->id;
    }

    #[Computed]
    public function welcomeData()
    {
        $siswaId = $this->getSiswaId();
        $namaPanggilan = explode(' ', Auth::user()->name)[0];
        
        
        $alpaBulanIni = Absensi::where('siswa_id', $siswaId)
            ->where('status', 'alpa')
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $jam = Carbon::now()->format('H');
        $sapaan = ($jam < 11) ? 'Selamat Pagi' : (($jam < 15) ? 'Selamat Siang' : (($jam < 18) ? 'Selamat Sore' : 'Selamat Malam'));

        
        if ($alpaBulanIni >= 3) {
          $pesan = "Hati-hati, kamu sudah punya $alpaBulanIni catatan Alpa bulan ini. Jangan sampai bolos yah " 
        . (Auth::user()->jenis_kelamin === "L" ? 'Bro' : 'Manis');

        $tipePesan = 'warning';
        } else {
            $pesan = "Kehadiranmu bulan ini terpantau stabil. Pertahankan terus, semangat!";
            $tipePesan = 'normal';
        }

        return [
            'sapaan' => $sapaan,
            'nama' => $namaPanggilan,
            'tanggal' => Carbon::now()->translatedFormat('l, d F Y'),
            'pesan' => $pesan,
            'tipe_pesan' => $tipePesan
        ];
    }

    #[Computed]
    public function statsCards()
    {
        $siswaId = $this->getSiswaId();
        
    
        $query = Absensi::where('siswa_id', $siswaId);
        $totalRecord = $query->count();
        
        $hadir = (clone $query)->whereIn('status', ['hadir', 'terlambat'])->count();
        $alpa = (clone $query)->where('status', 'alpa')->count();
        $izinSakit = (clone $query)->whereIn('status', ['izin', 'sakit'])->count();

        $persentase = $totalRecord > 0 ? round(($hadir / $totalRecord) * 100) : 0;

        return [
            'persentase' => $persentase,
            'total_alpa' => $alpa,
            'total_izin_sakit' => $izinSakit,
            'total_sesi' => $totalRecord,
        ];
    }

    #[Computed]
    public function distribusiAbsenPribadi()
    {
        $siswaId = $this->getSiswaId();

       
        $counts = Absensi::where('siswa_id', $siswaId)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $data = [
            'hadir'     => $counts['hadir'] ?? 0,
            'terlambat' => $counts['terlambat'] ?? 0,
            'sakit'     => $counts['sakit'] ?? 0,
            'izin'      => $counts['izin'] ?? 0,
            'alpa'      => $counts['alpa'] ?? 0,
        ];

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

   #[Computed]
    public function raporMapel()
    {
        $siswa = Auth::user()->siswa;
        $kelasId = $siswa->kelas_id;

        $now = \Carbon\Carbon::now();
        $tahunSekarang = $now->year;
        $bulanSekarang = $now->month;

        if ($bulanSekarang >= 1 && $bulanSekarang <= 7) {
            $startDate = \Carbon\Carbon::createFromDate($tahunSekarang, 1, 1)->startOfDay();
            $endDate   = \Carbon\Carbon::createFromDate($tahunSekarang, 7, 31)->endOfDay();
        } else {
            $startDate = \Carbon\Carbon::createFromDate($tahunSekarang, 8, 1)->startOfDay();
            $endDate   = \Carbon\Carbon::createFromDate($tahunSekarang, 12, 31)->endOfDay();
        }

        $labels = [];
        $datasets = [];
        $colors = [];

        $daftarMapel = \App\Models\GuruMapel::with('mapel')
            ->where('kelas_id', $kelasId)
            ->get();

        foreach ($daftarMapel as $gm) {
            $labels[] = $gm->mapel->nama_mapel;

            $queryAbsen = Absensi::where('siswa_id', $siswa->id)
                ->whereHas('sesiAbsensi', function($q) use ($gm, $startDate, $endDate) {
                    $q->where('guru_mapel_id', $gm->id)
                      ->whereBetween('tanggal', [$startDate, $endDate]);
                });

            $totalSesi = (clone $queryAbsen)->count();
            $hadir = (clone $queryAbsen)->whereIn('status', ['hadir', 'terlambat'])->count();

            $persen = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100) : 0;
            $datasets[] = $persen;

            $colors[] = $persen < 75 ? '#ef4444' : '#10b981'; 
        }

        return [
            'labels' => $labels,
            'data'   => $datasets,
            'colors' => $colors,
        ];
    }
    public function render()
    {
        return view('livewire.dashboard.siswa.index');
    }
}
