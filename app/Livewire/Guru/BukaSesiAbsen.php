<?php

namespace App\Livewire\Guru;

use App\Models\GuruMapel;
use App\Models\Kelas;
use App\Models\SesiAbsensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class BukaSesiAbsen extends Component
{
    #[Layout('layouts.app')]
    #[Title('Buka Sesi Kelas')]

    public $mapel_id = '';
    public $kelas_terpilih = [];

    public function mount()
    {
        // Jangan panggil tutupSesiOtomatis di sini - biarkan hanya dipanggil
        // ketika benar-benar perlu (misalnya di LiveMonitorAbsen saat tidak ada token)
        
        $guruId = Auth::user()->guru->id;

        // Cek sesi reguler (dengan mapel)
        $sesiAktif = SesiAbsensi::with('guruMapel.mapel')
            ->where(function ($query) use ($guruId) {
                $query->whereHas('guruMapel', function ($q) use ($guruId) {
                    $q->where('guru_id', $guruId);
                })
                    ->where('is_kelas_only', false);
            })
            ->where('status', 'berjalan')
            ->first();

        // Cek sesi kelas saja (berdasarkan kelas_id atau wali_kelas_id)
        $kelasQuery = SesiAbsensi::with('kelas')
            ->where(function ($query) use ($guruId) {
                $query->where('wali_kelas_id', $guruId)
                    ->orWhereHas('kelas', function ($q) use ($guruId) {
                        $q->where('guru_id', $guruId);
                    });
            })
            ->where('is_kelas_only', true)
            ->where('status', 'berjalan');

        if (!$sesiAktif) {
            $sesiAktif = $kelasQuery->first();
        }

        if ($sesiAktif && $sesiAktif->token_qr) {
            return redirect()->route('guru.absen.live', ['token' => $sesiAktif->token_qr]);
        }
    }

    #[Computed]
    public function isWaliKelas()
    {
        $guruId = Auth::user()->guru->id;
        return Kelas::where('guru_id', $guruId)->where('is_active', 1)->exists();
    }

    #[Computed]
    public function kelasWali()
    {
        $guruId = Auth::user()->guru->id;
        return Kelas::with(['siswas.user'])
            ->where('guru_id', $guruId)
            ->where('is_active', 1)
            ->first();
    }

    #[Computed]
    public function sesiKelasHariIni()
    {
        $kelas = $this->kelasWali;
        if (!$kelas) return null;

        $guruId = Auth::user()->guru->id;

        return SesiAbsensi::where('tanggal', now()->toDateString())
            ->where('kelas_id', $kelas->id)
            ->where('is_kelas_only', true)
            ->first();
    }

    #[Computed]
    public function sudahAdaSesiKelas()
    {
        return $this->sesiKelasHariIni() !== null;
    }

    public function updatedMapelId()
    {
        $this->kelas_terpilih = [];
    }

    #[Computed]
    public function listMapel()
    {
        $guruId = Auth::user()->guru->id;

        return GuruMapel::with('mapel')
            ->where('guru_id', $guruId)
            ->where('is_active', 1)
            ->whereHas('mapel')
            ->get()
            ->unique('mapel_id');
    }

    #[Computed]
    public function listKelas()
    {
        if (!$this->mapel_id) {
            return [];
        }

        $guruId = Auth::user()->guru->id;

        $guruMapelQuery = GuruMapel::with(['kelas' => function ($query) {
            $query->withCount('siswas');
        }])
            ->where('guru_id', $guruId)
            ->where('mapel_id', $this->mapel_id)
            ->where('is_active', 1)
            ->whereHas('kelas', function ($q) {
                $q->where('is_active', 1);
            });

        return $guruMapelQuery->get();
    }

    public function bukaSesiKelas()
    {
        $kelas = $this->kelasWali;
        if (!$kelas) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Anda bukan wali kelas dari kelas manapun.'
            ]);
            return;
        }

        $guruId = Auth::user()->guru->id;

        // Cek apakah sudah ada sesi kelas hari ini
        $sesiHariIni = SesiAbsensi::where('tanggal', now()->toDateString())
            ->where('kelas_id', $kelas->id)
            ->where('is_kelas_only', true)
            ->where('status', 'berjalan')
            ->first();

        if ($sesiHariIni) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Sesi absensi kelas sudah dibuka hari ini.'
            ]);
            return;
        }

        // Cek apakah ada siswa di kelas
        if ($kelas->siswas->isEmpty()) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Kelas ini belum memiliki siswa terdaftar.'
            ]);
            return;
        }

        // Buat sesi absensi kelas tanpa mapel (menggunakan wali_kelas_id)
        $tokenQr = Str::random(10);

        try {
            SesiAbsensi::create([
                'guru_mapel_id' => null,
                'kelas_id' => $kelas->id,
                'tanggal' => now()->toDateString(),
                'waktu_mulai' => now()->toTimeString(),
                'status' => 'berjalan',
                'token_qr' => $tokenQr,
                'is_kelas_only' => true,
                'wali_kelas_id' => $guruId,
            ]);

            // Verifikasi sesi berhasil dibuat dengan token yang valid
            $verifikasiSesi = SesiAbsensi::where('token_qr', $tokenQr)
                ->where('status', 'berjalan')
                ->where('is_kelas_only', true)
                ->whereNotNull('token_qr')
                ->first();
            if (!$verifikasiSesi) {
                throw new \Exception('Gagal membuat sesi absensi kelas.');
            }

            return redirect()->route('guru.absen.live', ['token' => $tokenQr]);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat membuka sesi. Silakan coba lagi.'
            ]);
        }
    }

    public function mulaiSesi()
    {
        $this->validate([
            'mapel_id' => 'required',
            'kelas_terpilih' => 'required|array|min:1',
        ], [
            'mapel_id.required' => 'Mata Pelajaran wajib dipilih.',
            'kelas_terpilih.required' => 'Pilih minimal satu kelas untuk memulai sesi.',
        ]);

        $guruId = Auth::user()->guru->id;

        $sesiAktif = SesiAbsensi::with('guruMapel.mapel')
            ->whereHas('guruMapel', function ($query) use ($guruId) {
                $query->where('guru_id', $guruId);
            })
            ->where('status', 'berjalan')
            ->where('is_kelas_only', false)
            ->first();

        if ($sesiAktif && $sesiAktif->token_qr) {
            return redirect()->route('guru.absen.live', ['token' => $sesiAktif->token_qr]);
        }

        $kelasKosong = [];

        $cekSiswa = GuruMapel::with(['kelas' => function ($query) {
            $query->withCount('siswas');
        }])->whereIn('id', $this->kelas_terpilih)->get();

        foreach ($cekSiswa as $gm) {
            if ($gm->kelas->siswas_count == 0) {
                $kelasKosong[] = $gm->kelas->nama_kelas;
            }
        }

        if (\count($kelasKosong) > 0) {
            $namaKelasString = implode(', ', $kelasKosong);
            $text = "Kelas $namaKelasString belum memiliki siswa terdaftar. Silakan hubungi Admin.";
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => $text]);
            return;
        }

        $tokenQr = Str::random(10);

        try {
            foreach ($this->kelas_terpilih as $guru_mapel_id) {
                SesiAbsensi::create([
                    'guru_mapel_id' => $guru_mapel_id,
                    'tanggal' => now()->toDateString(),
                    'waktu_mulai' => now()->toTimeString(),
                    'status' => 'berjalan',
                    'token_qr' => $tokenQr,
                ]);
            }

            // Verifikasi sesi berhasil dibuat dengan token yang valid
            $verifikasiSesi = SesiAbsensi::where('token_qr', $tokenQr)
                ->where('status', 'berjalan')
                ->whereNotNull('token_qr')
                ->first();
            if (!$verifikasiSesi) {
                throw new \Exception('Gagal membuat sesi absensi.');
            }

            return redirect()->route('guru.absen.live', ['token' => $tokenQr]);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat membuat sesi. Silakan coba lagi.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.guru.buka-sesi-absen');
    }
}