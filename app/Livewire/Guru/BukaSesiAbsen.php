<?php

namespace App\Livewire\Guru;

use App\Models\GuruMapel;
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
    #[Title('Buka Sesi Absensi Mapel')]

    public $mapel_id = '';
    public $kelas_terpilih = [];

    public function mount()
    {
        $guruId = Auth::user()->guru->id;

        $sesiAktif = SesiAbsensi::with('guruMapel.mapel')
            ->whereHas('guruMapel', function ($query) use ($guruId) {
                $query->where('guru_id', $guruId);
            })
            ->where('status', 'berjalan')
            ->first();

        if ($sesiAktif && $sesiAktif->token_qr) {
            return redirect()->route('guru.absen.live', ['token' => $sesiAktif->token_qr]);
        }
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
        if (! $this->mapel_id) {
            return [];
        }

        $guruId = Auth::user()->guru->id;

        return GuruMapel::with(['kelas' => function ($query) {
            $query->withCount('siswas');
        }])
            ->where('guru_id', $guruId)
            ->where('mapel_id', $this->mapel_id)
            ->where('is_active', 1)
            ->whereHas('kelas', function ($query) {
                $query->where('is_active', 1);
            })
            ->get();
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

        if (count($kelasKosong) > 0) {
            $namaKelasString = implode(', ', $kelasKosong);
            $text = "Kelas $namaKelasString belum memiliki siswa terdaftar. Silakan hubungi Admin.";
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => $text]);
            return;
        }

        $tokenQr = Str::random(10);

        try {
            foreach ($this->kelas_terpilih as $guru_mapel_id) {
                $sesi = SesiAbsensi::create([
                    'guru_mapel_id' => $guru_mapel_id,
                    'tanggal' => now()->toDateString(),
                    'waktu_mulai' => now()->toTimeString(),
                    'status' => 'berjalan',
                    'token_qr' => $tokenQr,
                ]);

                $sesi->notifyAssignedStudents('berlangsung');
            }

            $verifikasiSesi = SesiAbsensi::where('token_qr', $tokenQr)
                ->where('status', 'berjalan')
                ->whereNotNull('token_qr')
                ->first();

            if (! $verifikasiSesi) {
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
