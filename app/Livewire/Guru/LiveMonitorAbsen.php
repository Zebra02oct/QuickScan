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
        SesiAbsensi::tutupSesiOtomatis();
   
        $guruId = Auth::user()->guru->id;

     
        $sesis = SesiAbsensi::with('guruMapel.mapel', 'guruMapel.kelas')
            ->whereHas('guruMapel', function($query) use ($guruId) {
                $query->where('guru_id', $guruId);
            })
            ->where('status', 'berjalan')
            ->get();

        if ($sesis->isEmpty()) {
            session()->flash('error', 'Sesi Absensi tidak ditemukan atau sudah ditutup.');
            return redirect()->route('guru.dashboard');
        }

        $this->sesi_ids = $sesis->pluck('id')->toArray();
        
        $this->current_qr_token = $sesis->first()->token_qr;

        $firstSesi = $sesis->first();
        $this->mapel_nama = $firstSesi->guruMapel->mapel->nama_mapel;
        $this->kode_mapel = $firstSesi->guruMapel->mapel->kode_mapel;
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
        $sesis = SesiAbsensi::with('guruMapel.kelas.siswas.user')->whereIn('id', $this->sesi_ids)->get();
        $absensis = Absensi::whereIn('sesi_absensi_id', $this->sesi_ids)->get()->keyBy('siswa_id');

        $dataSiswa = collect();

        foreach ($sesis as $sesi) {
            foreach ($sesi->guruMapel->kelas->siswas as $siswa) {
                $absen = $absensis->get($siswa->id);

                $dataSiswa->push([
                    'id'         => $siswa->id,
                    'sesi_id'    => $sesi->id, 
                    'nama_siswa' => $siswa->user->name ?? 'User Tidak Ditemukan',
                    'kelas'      => $sesi->guruMapel->kelas->nama_kelas,
                    'status'     => $absen ? $absen->status : 'menunggu',
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

    Absensi::updateOrCreate(
        ['sesi_absensi_id' => $sesiId, 'siswa_id' => $siswaId],
        

        [
            'status' => $statusBaru, 
            'waktu_scan' => Absensi::where('sesi_absensi_id', $sesiId)->where('siswa_id', $siswaId)->exists() 
                            ? \DB::raw('waktu_scan') 
                            : now()->toTimeString()  
        ]
    );
}

#[On('eksekusi-batal-sesi')]
public function hapusSesi()
{
    \App\Models\Absensi::whereIn('sesi_absensi_id', (array) $this->sesi_ids)->forceDelete();

    \App\Models\SesiAbsensi::whereIn('id', (array) $this->sesi_ids)->forceDelete();

     $this->dispatch('swal:success', [
                'title' => 'Behasil!',
                'text'  => 'Sesi Dibatalkan.',
                'url'   => route('guru.manajemenAbsensi') 
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
                        'status'        => 'selesai',
                        'waktu_selesai' => now()->toTimeString(),
                        'token_qr'      => null 
                    ]);

                    $semuaSiswaKelasIni = $sesi->guruMapel->kelas->siswas->pluck('id')->toArray();
                    $siswaBelumAbsen = array_diff($semuaSiswaKelasIni, $siswaSudahAbsen);

                    $dataAlpa = [];
                    foreach ($siswaBelumAbsen as $siswaId) {
                        $dataAlpa[] = [
                            'sesi_absensi_id' => $sesi->id,
                            'siswa_id'        => $siswaId,
                            'status'          => 'alpa',
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }

                   
                    if (!empty($dataAlpa)) {
                        Absensi::insert($dataAlpa);
                    }
                }
            });

     $this->dispatch('swal:success', [
                'title' => 'Sesi Ditutup!',
                'text'  => 'Siswa yang tidak scan ditandai Alpa.',
                'url'   => route('guru.manajemenAbsensi') 
            ]);

        } catch (\Exception $e) {
           
            
         $this->dispatch('swal:error', [
                'title' => 'Gagal Menutup Sesi',
                'text'  => 'Sistem error: ' . $e->getMessage()
            ]);
         
            return; 
        }
    }

    public function render()
    {
        return view('livewire.guru.live-monitor-absen');
    }
}