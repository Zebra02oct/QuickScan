<?php

namespace App\Exports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GuruExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
{
   public function query()
    {
        return Guru::query()->with(['user']);
    }

      public function map($guru): array
    {
        return [
            $guru->nip,
            $guru->user?->name ?? 'Tanpa Nama',
            $guru->user?->email ?? '-',
            $guru->user?->jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan',
            strtoupper($guru->user?->status ?? 'Aktif') 
        ];
    }

      public function headings(): array
    {
        return [
            'NIP',
            'NAMA LENGKAP',
            'EMAIL',
            'JENIS KELAMIN',
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
