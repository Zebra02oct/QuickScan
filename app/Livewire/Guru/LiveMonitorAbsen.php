<?php

namespace App\Livewire\Guru;

use App\Models\Absensi;
use App\Models\SesiAbsensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class LiveMonitorAbsen extends Component
{
    #[Layout('layouts.app')]
    #[Title('Live Monitor Absensi')]

    public $sesi_ids = [];
    public $current_qr_token;

    public $mapel_nama;
    public $kode_mapel;
    public $kelas_nama = [];

    public function mount($token = null)
    {
        // Hanya jalankan tutup sesi otomatis jika tidak ada token
        // Jika ada token, itu berarti user sedang mengakses sesi aktif
        if (!$token) {
            SesiAbsensi::tutupSesiOtomatis();
        }

        $guruId = Auth::user()->guru->id;

        // Cari sesi berdasarkan token
        if ($token) {
            // Cek apakah sesi dengan token ini pernah ada (untuk feedback yang lebih baik)
            $sesiExisted = SesiAbsensi::where('token_qr', $token)->exists();

            $sesi = SesiAbsensi::with([
                'guruMapel.mapel',
                'guruMapel.kelas',
            ])
                ->where('token_qr', $token)
                ->where('status', 'berjalan')
                ->first();

            if (!$sesi) {
                if ($sesiExisted) {
                    session()->flash('error', 'Sesi Absensi ini sudah ditutup atau telah berakhir.');
                } else {
                    session()->flash('error', 'Sesi Absensi tidak ditemukan. Kode QR mungkin tidak valid.');
                }
                return redirect()->route('guru.dashboard');
            }

            // Cek apakah guru punya akses ke sesi ini
            $hasAccess = $sesi->guru_mapel_id && $sesi->guruMapel && $sesi->guruMapel->guru_id == $guruId;

            if ($hasAccess) {
                $this->mapel_nama = $sesi->guruMapel->mapel->nama_mapel ?? 'Tidak Diketahui';
                $this->kode_mapel = $sesi->guruMapel->mapel->kode_mapel ?? '-';
                $this->kelas_nama = [$sesi->guruMapel->kelas->nama_kelas ?? '-'];
            }

            if (!$hasAccess) {
                session()->flash('error', 'Anda tidak memiliki akses ke sesi ini.');
                return redirect()->route('guru.dashboard');
            }

            // Verifikasi status sesi masih berjalan
            if ($sesi->status !== 'berjalan') {
                session()->flash('error', 'Sesi Absensi ini sudah ditutup atau telah berakhir.');
                return redirect()->route('guru.dashboard');
            }

            $this->sesi_ids = [$sesi->id];
            $this->current_qr_token = $sesi->token_qr;

            return;
        }

        // Jika tidak ada token, cari sesi aktif
        $baseQuery = SesiAbsensi::with(['guruMapel.mapel', 'guruMapel.kelas'])
            ->where('status', 'berjalan')
            ->whereHas('guruMapel', function ($query) use ($guruId) {
                $query->where('guru_id', $guruId);
            });

        $sesis = $baseQuery->get();

        if ($sesis->isEmpty()) {
            session()->flash('error', 'Sesi Absensi tidak ditemukan atau sudah ditutup.');
            return redirect()->route('guru.dashboard');
        }

        $firstSesi = $sesis->first();
        $this->sesi_ids = $sesis->pluck('id')->toArray();
        $this->current_qr_token = $firstSesi->token_qr;
        $this->mapel_nama = $firstSesi->guruMapel->mapel->nama_mapel ?? 'Tidak Diketahui';
        $this->kode_mapel = $firstSesi->guruMapel->mapel->kode_mapel ?? '-';
        $this->kelas_nama = $sesis->map(fn($s) => $s->guruMapel->kelas->nama_kelas)->toArray();
    }

    public function refreshQR()
    {
        $newToken = Str::random(12);

        SesiAbsensi::whereIn('id', $this->sesi_ids)->update(['token_qr' => $newToken]);

        $this->current_qr_token = $newToken;
    }

    #[Computed]
    public function listSiswa()
    {
        $sesiQuery = SesiAbsensi::with('guruMapel.kelas.siswas.user')->whereIn('id', $this->sesi_ids);
        $sesis = $sesiQuery->get();

        $absensis = Absensi::whereIn('sesi_absensi_id', $this->sesi_ids)->get()->keyBy('siswa_id');

        $dataSiswa = collect();

        foreach ($sesis as $sesi) {
            foreach ($sesi->guruMapel->kelas->siswas as $siswa) {
                $absen = $absensis->get($siswa->id);

                $dataSiswa->push([
                    'id' => $siswa->id,
                    'sesi_id' => $sesi->id,
                    'nama_siswa' => $siswa->user->name ?? 'User Tidak Ditemukan',
                    'kelas' => $sesi->guruMapel->kelas->nama_kelas,
                    'status' => $absen ? $absen->status : 'menunggu',
                    'waktu_scan' => $absen ? $absen->waktu_scan : null,
                ]);
            }
        }

        return $dataSiswa->sortByDesc('waktu_scan')->values();
    }

    public function ubahStatusSiswa($siswaId, $sesiId, $statusBaru)
    {
        $absen = Absensi::where('sesi_absensi_id', $sesiId)
            ->where('siswa_id', $siswaId)
            ->first();

        if ($statusBaru === 'menunggu') {
            if ($absen) {
                $absen->forceDelete();
            }
            return;
        }

        $query = Absensi::where('sesi_absensi_id', $sesiId)->where('siswa_id', $siswaId);
        $exists = $query->exists();

        $waktuScan = $exists ? \DB::raw('waktu_scan') : now()->toTimeString();

        Absensi::updateOrCreate(
            ['sesi_absensi_id' => $sesiId, 'siswa_id' => $siswaId],
            [
                'status' => $statusBaru,
                'waktu_scan' => $waktuScan,
            ]
        );
    }

    #[On('eksekusi-batal-sesi')]
    public function hapusSesi()
    {
        Absensi::whereIn('sesi_absensi_id', (array) $this->sesi_ids)->forceDelete();

        SesiAbsensi::whereIn('id', (array) $this->sesi_ids)->forceDelete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Sesi Dibatalkan.',
            'url' => route('guru.manajemenAbsensi')
        ]);
    }

    #[On('eksekusi-tutup-sesi')]
    public function tutupSesi()
    {
        try {
            DB::transaction(function () {
                $sesis = SesiAbsensi::with('guruMapel.kelas.siswas')->whereIn('id', $this->sesi_ids)->get();
                $siswaSudahAbsen = Absensi::whereIn('sesi_absensi_id', $this->sesi_ids)->pluck('siswa_id')->toArray();

                foreach ($sesis as $sesi) {
                    $sesi->update([
                        'status' => 'selesai',
                        'waktu_selesai' => now()->toTimeString(),
                        'token_qr' => null,
                    ]);

                    $semuaSiswaKelasIni = $sesi->guruMapel->kelas->siswas->pluck('id')->toArray();
                    $siswaBelumAbsen = array_diff($semuaSiswaKelasIni, $siswaSudahAbsen);

                    $dataAlpa = [];
                    foreach ($siswaBelumAbsen as $idSiswa) {
                        $dataAlpa[] = [
                            'sesi_absensi_id' => $sesi->id,
                            'siswa_id' => $idSiswa,
                            'status' => 'alpa',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    if (! empty($dataAlpa)) {
                        Absensi::insert($dataAlpa);
                    }
                }
            });

            $this->dispatch('swal:success', [
                'title' => 'Sesi Ditutup!',
                'text' => 'Siswa yang tidak scan ditandai Alpa.',
                'url' => route('guru.manajemenAbsensi')
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal Menutup Sesi',
                'text' => 'Sistem error: ' . $e->getMessage()
            ]);

            return;
        }
    }

    public function render()
    {
        return view('livewire.guru.live-monitor-absen');
    }
}
