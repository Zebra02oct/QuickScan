<?php

namespace App\Livewire\Guru\LaporanAbsensi;

use App\Models\GuruMapel;
use App\Models\SesiAbsensi;
use App\Models\Absensi;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Guru\RekapAbsensiExport;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Detail extends Component
{
    use WithPagination;

    #[Layout('layouts.app')]
    #[Title('Analitik Kelas')]

    public $guru_mapel_id;
    public $guruMapel;

    public $start_date;
    public $end_date;

    public function mount($id)
    {

        $this->guru_mapel_id = $id;

        $this->guruMapel = GuruMapel::with(['kelas', 'mapel'])->findOrFail($id);

        if ($this->guruMapel->guru_id !== Auth::user()->guru->id) {
            abort(403, 'Anda tidak memiliki akses ke laporan kelas ini.');
        }

        $now = Carbon::now();
        if ($now->month >= 7) {
            $this->start_date = request()->query('start', $now->year . '-07-01');
            $this->end_date = request()->query('end', $now->year . '-12-31');
        } else {
            $this->start_date = request()->query('start', $now->year . '-01-01');
            $this->end_date = request()->query('end', $now->year . '-06-30');
        }
    }

    #[Computed]
    public function dataSesi()
    {
        return SesiAbsensi::with('absensis')
            ->where('guru_mapel_id', $this->guru_mapel_id)
            ->whereBetween('tanggal', [$this->start_date, $this->end_date])
            ->orderBy('tanggal', 'asc')
            ->get();
    }

    #[Computed]
    public function statistik()
    {
        $sesi = $this->dataSesi;
        $totalHadir = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAlpa = 0;
        $totalKehadiran = 0;

        foreach ($sesi as $s) {
            foreach ($s->absensis as $k) {
                if (in_array($k->status, ['hadir', 'terlambat'])) {
                    $totalHadir++;
                } elseif ($k->status == 'izin') {
                    $totalIzin++;
                } elseif ($k->status == 'sakit') {
                    $totalSakit++;
                } elseif ($k->status == 'alpa') {
                    $totalAlpa++;
                }
                $totalKehadiran++;
            }
        }

        $rataHadir = $totalKehadiran > 0 ? round(($totalHadir / $totalKehadiran) * 100) : 0;

        return [
            'rata_hadir' => $rataHadir,
            'izin'       => $totalIzin,
            'sakit'      => $totalSakit,
            'alpa'       => $totalAlpa,
            'sesi'       => $sesi->count()
        ];
    }

    #[Computed]
    public function siswaKritis()
    {
        $sesiIds = $this->dataSesi->pluck('id');

        if ($sesiIds->isEmpty()) {
            return collect();
        }


        return Absensi::with('siswa.user')
            ->whereIn('sesi_absensi_id', $sesiIds)
            ->where('status', 'alpa')
            ->select('siswa_id', DB::raw('count(*) as total_alpa'))
            ->groupBy('siswa_id')
            ->having('total_alpa', '>=', 3)
            ->orderBy('total_alpa', 'desc')
            ->take(5)
            ->get();
    }

    #[Computed]
    public function dataSesiPaginated()
    {
        return SesiAbsensi::with('absensis')
            ->where('guru_mapel_id', $this->guru_mapel_id)
            ->whereBetween('tanggal', [$this->start_date, $this->end_date])
            ->orderBy('tanggal', 'desc')
            ->paginate(6);
    }

    public function filterData()
    {
        $this->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $this->resetPage();

        $this->dispatch('update-chart', rata_hadir: $this->statistik['rata_hadir']);
    }

    public function exportExcel()
    {

        $kelas_id = $this->guruMapel->kelas_id;
        $mapel_id = $this->guruMapel->mapel_id;


        $namaMapel = Str::slug($this->guruMapel->mapel->nama_mapel);
        $namaKelas = Str::slug($this->guruMapel->kelas->nama_kelas);
        $namaFile = "Rekap_{$namaMapel}_{$namaKelas}.xlsx";


        return Excel::download(
            new RekapAbsensiExport($kelas_id, $mapel_id, 'custom', $this->start_date, $this->end_date),
            $namaFile
        );
    }

    public function render()
    {
        return view('livewire.guru.laporan-absensi.detail');
    }
}