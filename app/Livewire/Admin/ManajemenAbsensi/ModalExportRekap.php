<?php

namespace App\Livewire\Admin\ManajemenAbsensi;

use App\Exports\Admin\RekapAbsensiExport as adminRekap;
use App\Models\GuruMapel;
use App\Models\Kelas;
use App\Models\Mapel;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ModalExportRekap extends Component
{
    public $export_kelas_id = '';
    public $export_mapel_id = '';
    public $export_guru_id = ''; 
    public $export_type = 'semester'; 
    public $start_date = '';
    public $end_date = '';

   
    public function updated($property)
    {
        if ($property === 'export_kelas_id' || $property === 'export_mapel_id') {
            $this->export_guru_id = '';
        }
    }

    public function loadData()
    {
        $this->reset(['export_kelas_id', 'export_mapel_id', 'export_guru_id', 'start_date', 'end_date']);
        $this->export_type = 'semester'; 
        $this->resetValidation();
    }

    #[Computed]
    public function daftarKelas()
    {
        return Kelas::orderBy('nama_kelas', 'asc')->get();
    }

    #[Computed]
    public function daftarMapel()
    {
        return Mapel::orderBy('nama_mapel', 'asc')->get();
    }

    #[Computed]
    public function daftarGuruPengampu()
    {
        if ($this->export_kelas_id && $this->export_mapel_id) {
            return GuruMapel::with('guru.user')
                ->where('kelas_id', $this->export_kelas_id)
                ->where('mapel_id', $this->export_mapel_id)
                ->get()
                ->pluck('guru')
                ->filter()
                ->unique('id')
                ->values();
        }
        return collect();
    }

    #[Computed]
    public function infoSemesterBerjalan()
    {
        $sekarang = \Carbon\Carbon::now();
        $tahunIni = $sekarang->year;
        $bulanIni = $sekarang->month;

        if ($bulanIni >= 7 && $bulanIni <= 12) {
            $mulai = \Carbon\Carbon::create($tahunIni, 7, 1)->translatedFormat('d F Y');
            $akhir = \Carbon\Carbon::create($tahunIni, 12, 31)->translatedFormat('d F Y');
            $namaSemester = "Ganjil";
        } else {
            $mulai = \Carbon\Carbon::create($tahunIni, 1, 1)->translatedFormat('d F Y');
            $akhir = \Carbon\Carbon::create($tahunIni, 6, 30)->translatedFormat('d F Y');
            $namaSemester = "Genap";
        }

        return [
            'teks' => "Data yang diexport mencakup Semester $namaSemester ($mulai - $akhir).",
            'mulai' => $mulai,
            'akhir' => $akhir
        ];
    }

    #[On('reset-modal')]
    public function resetForm()
    {
        $this->reset(['export_kelas_id', 'export_mapel_id', 'export_guru_id', 'start_date', 'end_date']);
        $this->export_type = 'semester';
        $this->resetValidation();
    }

   public function exportExcel()
    {
       
        $this->validate([
            'export_kelas_id' => 'required',
            'export_mapel_id' => 'required',
            'export_guru_id'  => 'required', 
            'start_date'      => $this->export_type === 'custom' ? 'required|date' : 'nullable',
            'end_date'        => $this->export_type === 'custom' ? 'required|date|after_or_equal:start_date' : 'nullable',
        ], [
            'export_kelas_id.required'  => 'Kelas harus dipilih.',
            'export_mapel_id.required'  => 'Mata pelajaran harus dipilih.',
            'export_guru_id.required'   => 'Guru pengampu harus dipilih.',
            'start_date.required'       => 'Tanggal awal harus diisi.',
            'start_date.date'           => 'Format tanggal awal tidak valid.',
            'end_date.required'         => 'Tanggal akhir harus diisi.',
            'end_date.date'             => 'Format tanggal akhir tidak valid.',
            'end_date.after_or_equal'   => 'Tanggal akhir tidak boleh mendahului tanggal awal.'
        ]);

        $namaKelas = Kelas::find($this->export_kelas_id)->nama_kelas ?? 'Kelas';
        $namaFile = 'Rekap_Absensi_Admin_' . str_replace(' ', '_', $namaKelas) . '_' . date('d-M-Y') . '.xlsx';

        return Excel::download(new adminRekap(
            $this->export_kelas_id,
            $this->export_mapel_id,
            $this->export_guru_id, 
            $this->export_type,
            $this->start_date,
            $this->end_date
        ), $namaFile);
    }

    public function render()
    {
     
        return view('livewire.admin.manajemen-absensi.modal-export-rekap'); 
    }
}