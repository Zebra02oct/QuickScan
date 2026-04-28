<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SiswaImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected $kelas_id;
    public $rowCount = 0; 

    public function __construct($kelas_id)
    {
        $this->kelas_id = $kelas_id;
    }

    public function collection(Collection $rows)
    {
        $firstRow = $rows->first();
        if (!isset($firstRow['nama']) || !isset($firstRow['nisn']) || !isset($firstRow['email']) || !isset($firstRow['jenis_kelamin'])) {
            throw new \Exception("Header Excel tidak valid! Pastikan Anda menggunakan Template yang sudah disediakan.");
        }

        foreach ($rows as $index => $row) {
            $barisExcel = $index + 2;

            $nama  = trim($row['nama'] ?? '');
            $nisn  = trim($row['nisn'] ?? '');
            $email = trim($row['email'] ?? '');
            $jk    = trim($row['jenis_kelamin'] ?? '');

         
            if ($nama === '' && $nisn === '' && $email === '' && $jk === '') {
                continue; 
            }

           
            if ($nama === '' || $nisn === '' || $email === '' || $jk === '') {
                throw new \Exception("Data Ditolak! Ada kolom yang belum diisi pada baris ke-{$barisExcel}. (Nama, NISN, Email, dan Jenis Kelamin wajib diisi semua).");
            }

       
            $cekEmail = User::where('email', $email)->exists();
            $cekNisn  = Siswa::where('nisn', $nisn)->exists();

            if ($cekEmail) {
                throw new \Exception("Data Ditolak! Terdapat Email duplikat ({$email}) pada baris ke-{$barisExcel}.");
            }
            if ($cekNisn) {
                throw new \Exception("Data Ditolak! Terdapat NISN duplikat ({$nisn}) pada baris ke-{$barisExcel}.");
            }

            $user = User::create([
                'name'     => $nama,
                'email'    => $email,
                'status'   => 'aktif',
                'jenis_kelamin' => strtoupper($jk),
                'password' => Hash::make($nisn), 
            ]);

            Siswa::create([
                'user_id'       => $user->id,
                'kelas_id'      => $this->kelas_id,
                'nisn'          => $nisn,
                
            ]);

            $this->rowCount++;
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }
}