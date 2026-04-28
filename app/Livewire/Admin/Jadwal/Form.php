<?php

namespace App\Livewire\Admin\Jadwal;

use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Guru;
use Livewire\Component;

class Form extends Component
{
    public $jadwal_id = null;
    public $tahun_ajaran_id = '';
    public $nama_ta_aktif = ''; 
    public $has_active_ta = true; 
    
    public $kelas_id = []; 
    public $mapel_id = []; 
    public $guru_id = '';

    protected function rules()
    {
        return [
            'tahun_ajaran_id' => 'required',
            'kelas_id'        => 'required|array|min:1',
            'mapel_id'        => 'required|array|min:1',
            'guru_id'         => 'required',
        ];
    }

    protected $messages = [
        'required'      => 'Kolom ini wajib dipilih.',
        'kelas_id.min'  => 'Pilih minimal satu kelas.',
        'mapel_id.min'  => 'Pilih minimal satu mata pelajaran.',
    ];

    // MAGIC REAL-TIME VALIDATION: Dijalankan otomatis tiap kali ada input yang berubah
    public function updated($property)
    {
        $this->cekBentrokRealtime();
    }

    // Fungsi khusus buat ngecek bentrok
    public function cekBentrokRealtime()
    {
        // Bersihkan error bentrok sebelumnya
        $this->resetErrorBag('plotting_conflict');

        // Hanya jalankan pengecekan kalau semua data minimal sudah terisi
        if ($this->tahun_ajaran_id && $this->guru_id && !empty($this->mapel_id) && !empty($this->kelas_id)) {
            
            foreach ($this->mapel_id as $mpl_id) {
                foreach ($this->kelas_id as $kls_id) {
                    
                    // Cek ke database apakah kombinasi TA, Kelas, dan Mapel ini sudah ada yang punya?
                    $existingPlot = JadwalPelajaran::with(['guru.user', 'mapel', 'kelas'])
                        ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
                        ->where('kelas_id', $kls_id)
                        ->where('mapel_id', $mpl_id);

                    // Kecualikan ID yang sedang diedit (kalau lagi mode Edit)
                    if ($this->jadwal_id) {
                        $existingPlot->where('id', '!=', $this->jadwal_id);
                    }

                    $existingPlot = $existingPlot->first();

                    // Kalau ternyata ada datanya, eksekusi validasi
                    if ($existingPlot) {
                        $namaKelas = $existingPlot->kelas->nama_kelas ?? 'Kelas';
                        $namaMapel = $existingPlot->mapel->nama_mapel ?? 'Mapel';
                        $namaGuru  = $existingPlot->guru->user->name ?? 'Guru Lain';

                        if ($existingPlot->guru_id == $this->guru_id) {
                            // KONDISI 1: Guru yang sama udah diplot di mapel dan kelas ini
                            $this->addError('plotting_conflict', "Guru ini sudah diplot untuk mapel <b>{$namaMapel}</b> di kelas <b>{$namaKelas}</b>.");
                        } else {
                            // KONDISI 2: Mapel di kelas ini udah diambil guru lain (Ini yang kamu maksud)
                            $this->addError('plotting_conflict', "Waduh! Mapel <b>{$namaMapel}</b> di <b>{$namaKelas}</b> sudah diambil oleh <b>{$namaGuru}</b>.");
                        }
                        
                        // Stop looping setelah nemu 1 error biar sistem nggak berat
                        return; 
                    }
                }
            }
        }
    }

    public function loadData($id = null)
    {
        $this->reset();
        $this->resetValidation();

        $ta_aktif = TahunAjaran::where('status', 'aktif')->first();

        if (!$ta_aktif && !$id) {
            $this->has_active_ta = false;
            $this->nama_ta_aktif = 'Tidak ada Tahun Ajaran aktif!';
        } else {
            $this->has_active_ta = true;
        }

        if ($id) {
            $data = JadwalPelajaran::with('tahunAjaran')->find($id);
            if ($data) {
                $this->jadwal_id       = $data->id;
                $this->tahun_ajaran_id = $data->tahun_ajaran_id;
                $this->nama_ta_aktif   = $data->tahunAjaran->nama_ta;
                
                $this->kelas_id        = [(string) $data->kelas_id]; 
                $this->mapel_id        = [(string) $data->mapel_id]; 
                $this->guru_id         = $data->guru_id;
            }
        } else if ($ta_aktif) {
            $this->tahun_ajaran_id = $ta_aktif->id;
            $this->nama_ta_aktif   = $ta_aktif->nama_ta;
        }
    }

    public function save()
    {
        if (!$this->has_active_ta && !$this->jadwal_id) {
            return;
        }

        $this->validate();

        // Panggil lagi fungsi cek bentrok pas tombol simpan ditekan (Buat pengamanan ekstra)
        $this->cekBentrokRealtime();
        
        // Kalau ternyata ada error 'plotting_conflict', hentikan proses save!
        if ($this->getErrorBag()->has('plotting_conflict')) {
            return;
        }

        if ($this->jadwal_id) {
            // MODE EDIT
            JadwalPelajaran::where('id', $this->jadwal_id)->update([
                'tahun_ajaran_id' => $this->tahun_ajaran_id,
                'kelas_id'        => $this->kelas_id[0],
                'mapel_id'        => $this->mapel_id[0],
                'guru_id'         => $this->guru_id,
            ]);
        } else {
            // MODE CREATE
            foreach ($this->mapel_id as $mpl_id) {
                foreach ($this->kelas_id as $kls_id) {
                    JadwalPelajaran::create([
                        'tahun_ajaran_id' => $this->tahun_ajaran_id,
                        'kelas_id'        => $kls_id,
                        'mapel_id'        => $mpl_id,
                        'guru_id'         => $this->guru_id,
                    ]);
                }
            }
        }

        $this->dispatch('close-modal');
        $this->dispatch('refresh-jadwal');
        $this->dispatch('swal:success', [
            'title' => 'Mantap!',
            'text'  => 'Data plotting mengajar berhasil diperbarui.'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.jadwal.form', [
            'list_kelas' => Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get(),
            'list_mapel' => Mapel::orderBy('nama_mapel')->get(),
            'list_guru'  => Guru::with('user')->get()->sortBy('user.name'), 
        ]);
    }
}