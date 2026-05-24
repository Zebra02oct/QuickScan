<?php

namespace App\Livewire\Guru;

use App\Models\Absensi;
use App\Models\Kelas;
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
    public $is_kelas_only = false;

    public $mapel_nama;
    public $kode_mapel;
    public $kelas_nama = [];
    public $kelas_id = null;

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
                'kelas.waliKelas.user'
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
            $hasAccess = false;

            if ($sesi->is_kelas_only) {
                // Cek apakah guru adalah wali kelas dari kelas ini
                // Bisa dicek via kelas_id langsung atau via wali_kelas_id
                $kelas = $sesi->kelas;

                // Jika guru adalah wali kelas dari kelas ini
                $isWaliKelas = $kelas && $kelas->guru_id == $guruId;

                // Atau jika wali_kelas_id cocok
                $isWaliKelasById = $sesi->wali_kelas_id == $guruId;

                if ($isWaliKelas || $isWaliKelasById) {
                    $hasAccess = true;
                    $this->is_kelas_only = true;
                    $this->kelas_id = $sesi->kelas_id;
                    $this->mapel_nama = 'Absensi Kelas';
                    $this->kode_mapel = 'KELAS';
                    $this->kelas_nama = [$kelas ? $kelas->nama_kelas : 'Kelas'];
                }
            } elseif ($sesi->guru_mapel_id && $sesi->guruMapel && $sesi->guruMapel->guru_id == $guruId) {
                $hasAccess = true;
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
        $baseQuery = SesiAbsensi::with(['guruMapel.mapel', 'guruMapel.kelas', 'kelas'])
            ->where('status', 'berjalan')
            ->where(function ($query) use ($guruId) {
                // Sesi reguler (dengan mapel)
                $query->where(function ($q) use ($guruId) {
                    $q->whereHas('guruMapel', function ($subQ) use ($guruId) {
                        $subQ->where('guru_id', $guruId);
                    })
                        ->where('is_kelas_only', false);
                });

                // Sesi kelas (wali kelas)
                $query->orWhere(function ($q) use ($guruId) {
                    $q->where('is_kelas_only', true)
                        ->where('wali_kelas_id', $guruId);
                });
            });

        $sesis = $baseQuery->get();

        if ($sesis->isEmpty()) {
            session()->flash('error', 'Sesi Absensi tidak ditemukan atau sudah ditutup.');
            return redirect()->route('guru.dashboard');
        }

        $firstSesi = $sesis->first();
        $this->sesi_ids = $sesis->pluck('id')->toArray();
        $this->current_qr_token = $firstSesi->token_qr;
        $this->is_kelas_only = $firstSesi->is_kelas_only;

        if ($this->is_kelas_only) {
            // Untuk sesi kelas saja, gunakan kelas_id dari session, bukan query dari guru
            $kelas = $firstSesi->kelas;
            if ($kelas) {
                $this->kelas_id = $kelas->id;
                $this->mapel_nama = 'Absensi Kelas';
                $this->kode_mapel = 'KELAS';
                $this->kelas_nama = [$kelas->nama_kelas];
            } else {
                session()->flash('error', 'Data kelas tidak ditemukan untuk sesi ini.');
                return redirect()->route('guru.dashboard');
            }
        } else {
            $this->mapel_nama = $firstSesi->guruMapel->mapel->nama_mapel ?? 'Tidak Diketahui';
            $this->kode_mapel = $firstSesi->guruMapel->mapel->kode_mapel ?? '-';
            $this->kelas_nama = $sesis->map(fn($s) => $s->guruMapel->kelas->nama_kelas)->toArray();
        }
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
        $guruId = Auth::user()->guru->id;

        if ($this->is_kelas_only && $this->kelas_id) {
            // Ambil siswa dari kelas_id langsung
            $kelas = Kelas::with(['siswas.user'])->where('id', $this->kelas_id)->first();

            if (!$kelas) {
                return collect();
            }

            $absensis = Absensi::whereIn('sesi_absensi_id', $this->sesi_ids)->get()->keyBy('siswa_id');

            $dataSiswa = collect();
            foreach ($kelas->siswas as $siswa) {
                $absen = $absensis->get($siswa->id);

                $dataSiswa->push([
                    'id' => $siswa->id,
                    'sesi_id' => $this->sesi_ids[0] ?? 0,
                    'nama_siswa' => $siswa->user->name ?? 'User Tidak Ditemukan',
                    'kelas' => $kelas->nama_kelas,
                    'status' => $absen ? $absen->status : 'menunggu',
                    'waktu_scan' => $absen ? $absen->waktu_scan : null,
                ]);
            }

            return $dataSiswa->sortByDesc('waktu_scan')->values();
        }

        // Sesi reguler (dengan mapel)
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
                $guruId = Auth::user()->guru->id;

                if ($this->is_kelas_only && $this->kelas_id) {
                    // Tutup sesi kelas saja
                    $kelas = Kelas::with('siswas')->where('id', $this->kelas_id)->first();

                    if ($kelas) {
                        $semuaSiswaKelas = $kelas->siswas->pluck('id')->toArray();
                        $siswaSudahAbsen = Absensi::whereIn('sesi_absensi_id', $this->sesi_ids)->pluck('siswa_id')->toArray();
                        $siswaBelumAbsen = array_diff($semuaSiswaKelas, $siswaSudahAbsen);

                        foreach ($this->sesi_ids as $sesiId) {
                            $sesi = SesiAbsensi::find($sesiId);
                            $sesi->update([
                                'status' => 'selesai',
                                'waktu_selesai' => now()->toTimeString(),
                                'token_qr' => null
                            ]);
                        }

                        $dataAlpa = [];
                        foreach ($siswaBelumAbsen as $idSiswa) {
                            $dataAlpa[] = [
                                'sesi_absensi_id' => $this->sesi_ids[0],
                                'siswa_id' => $idSiswa,
                                'status' => 'alpa',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        if (!empty($dataAlpa)) {
                            Absensi::insert($dataAlpa);
                        }
                    }
                } else {
                    // Tutup sesi reguler (dengan mapel)
                    $sesiQuery = SesiAbsensi::with('guruMapel.kelas.siswas')->whereIn('id', $this->sesi_ids);
                    $sesis = $sesiQuery->get();

                    $siswaSudahAbsen = Absensi::whereIn('sesi_absensi_id', $this->sesi_ids)->pluck('siswa_id')->toArray();

                    foreach ($sesis as $sesi) {
                        $sesi->update([
                            'status' => 'selesai',
                            'waktu_selesai' => now()->toTimeString(),
                            'token_qr' => null
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

                        if (!empty($dataAlpa)) {
                            Absensi::insert($dataAlpa);
                        }
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
