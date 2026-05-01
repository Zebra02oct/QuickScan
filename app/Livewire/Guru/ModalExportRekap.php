<?php

namespace App\Livewire\Guru;

use App\Exports\Guru\RekapAbsensiExport;
use App\Models\GuruMapel;
use App\Models\Kelas;
use App\Models\SesiAbsensi;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ModalExportRekap extends Component
{
    public $guruId; 
 
    public $export_kelas_id = '';
    public $export_mapel_id = '';
    public $export_type = 'semester'; 
    public $start_date = '';
    public $end_date = '';


   public function loadData()
    {
        $this->reset(['export_kelas_id', 'export_mapel_id', 'start_date', 'end_date']);
        $this->export_type = 'semester'; 
        
        $this->resetValidation();
    }

#[Computed]
public function daftarKelas()
    {
        $guruMapelAktif = SesiAbsensi::select('guru_mapel_id')
                                                 ->distinct()
                                                 ->pluck('guru_mapel_id');

        return GuruMapel::where('guru_id', $this->guruId)
            ->whereIn('id', $guruMapelAktif) 
            ->with('kelas')
            ->get()
            ->pluck('kelas')
            ->filter()
            ->unique('id')
            ->values();
    }

 #[Computed]
    public function daftarMapel()
    {
      
        $guruMapelAktif = SesiAbsensi::select('guru_mapel_id')
                                                 ->distinct()
                                                 ->pluck('guru_mapel_id');

        return GuruMapel::where('guru_id', $this->guruId)
            ->whereIn('id', $guruMapelAktif)
            ->with('mapel')
            ->get()
            ->pluck('mapel')
            ->filter() 
            ->unique('id')
            ->values();
    }
  

    #[On('reset-modal')]
    public function resetForm()
    {
        $this->reset(['export_kelas_id', 'export_mapel_id', 'start_date', 'end_date']);
        $this->export_type = 'semester';
        $this->resetValidation();
    }
    

  public function exportExcel()
    {
        $this->validate([
            'export_kelas_id' => 'required',
            'export_mapel_id' => 'required',
            'start_date' => $this->export_type === 'custom' ? 'required|date' : 'nullable',
            'end_date' => $this->export_type === 'custom' ? 'required|date|after_or_equal:start_date' : 'nullable',
        ], [
            'export_kelas_id.required' => 'Kelas harus dipilih.',
            'export_mapel_id.required' => 'Mata pelajaran harus dipilih.',
            'start_date.required' => 'Tanggal awal harus diisi.',
            'end_date.required' => 'Tanggal akhir harus diisi.',
            'end_date.after_or_equal' => 'Tanggal akhir tidak boleh mendahului tanggal awal.'
        ]);

        $namaKelas = Kelas::find($this->export_kelas_id)->nama_kelas ?? 'Kelas';
        $namaFile = 'Rekap_Absensi_' . str_replace(' ', '_', $namaKelas) . '_' . date('d-M-Y') . '.xlsx';

        return Excel::download(new RekapAbsensiExport(
            $this->export_kelas_id,
            $this->export_mapel_id,
            $this->export_type,
            $this->start_date,
            $this->end_date
        ), $namaFile);
    }

    public function render()
    {
        return view('livewire.guru.modal-export-rekap');
    }
}