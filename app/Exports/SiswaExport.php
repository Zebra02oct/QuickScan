<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// Perhatikan interfaces yang dipakai: FromQuery dan WithMapping
class SiswaExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * MAGIC 1: FromQuery
     * Jangan pakai get() atau all() di sini! 
     * Cukup return Query Builder-nya aja. Maatwebsite yang bakal ngurusin potong-potong datanya (Chunk).
     * Pakai with() untuk Eager Loading biar nggak kena N+1 Query Problem yang bikin lambat!
     */
    public function query()
    {
        return Siswa::query()->with(['user', 'kelas'])->orderBy('kelas_id');
    }

    /**
     * MAGIC 2: WithMapping
     * Di sini kita map/cocokin data dari database ke kolom Excel baris demi baris secara stream.
     */
    public function map($siswa): array
    {
        return [
            $siswa->nisn,
            $siswa->user?->name ?? 'Tanpa Nama',
            $siswa->user?->email ?? '-',
            $siswa->user?->jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan',
            $siswa->kelas?->nama_kelas ?? 'Belum Ada Kelas',
            strtoupper($siswa->user?->status ?? 'Aktif') 
        ];
    }

    /**
     * Header Kolom Excel
     */
    public function headings(): array
    {
        return [
            'NISN',
            'NAMA LENGKAP',
            'EMAIL',
            'JENIS KELAMIN',
            'KELAS',
            'STATUS'
        ];
    }

    /**
     * Style Header (Biar kelihatan profesional)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F46E5'] // Warna background header Indigo
                ]
            ],
        ];
    }
}