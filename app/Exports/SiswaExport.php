<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class SiswaExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
{
    
    public function query()
    {
        return Siswa::query()->with(['user', 'kelas'])->orderBy('kelas_id');
    }

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

  
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F46E5']
                ]
            ],
        ];
    }
}