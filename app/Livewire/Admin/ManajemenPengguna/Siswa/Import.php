<?php

namespace App\Livewire\Admin\ManajemenPengguna\Siswa;

use App\Exports\SiswaTemplateExport;
use App\Imports\SiswaImport;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class Import extends Component
{
    use WithFileUploads; 

    #[Layout('layouts.app')]
    #[Title('Import Data Siswa')]

    public $kelas_id = '';
    public $file_excel;

    protected $rules = [
        'kelas_id'   => 'required|exists:kelas,id',
        'file_excel' => 'required|mimes:xlsx,xls|max:5120',
    ];

    protected $messages = [
        'kelas_id.required'   => 'Pilih kelas tujuan terlebih dahulu.',
        'file_excel.required' => 'File Excel wajib diunggah.',
        'file_excel.mimes'    => 'Format file wajib berupa .xlsx atau .xls.',
        'file_excel.max'      => 'Ukuran file tidak boleh lebih dari 5MB.',
    ];

 public function importData()
    {
        set_time_limit(120);
        $this->validate();

        DB::beginTransaction();

        try {
           
            $importObject = new SiswaImport($this->kelas_id);
            
            Excel::import($importObject, $this->file_excel);

            DB::commit();

            $namaKelas = Kelas::find($this->kelas_id)->nama_kelas;

            $this->dispatch('swal:success', [
                'title' => 'Import Sukses!',
                'text'  => "Berhasil menambahkan {$importObject->rowCount} siswa baru ke kelas {$namaKelas}."
            ]);

            $this->reset(['kelas_id', 'file_excel']);

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('swal:error', [
                'title' => 'Import Dibatalkan!',
                'text'  => $e->getMessage()
            ]);
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new SiswaTemplateExport, 'Template_Import_Siswa.xlsx');
    }

    public function render()
    {
        return view('livewire.admin.manajemen-pengguna.siswa.import', [
            'list_kelas' => Kelas::where('is_active', true)->orderBy('tingkat')->orderBy('nama_kelas')->get()
        ]);
    }
}