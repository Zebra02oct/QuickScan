<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{

    public function headings(): array
    {
        return [
            'nama',
            'nisn',
            'email',
            'jenis_kelamin'
        ];
    }

    
    public function array(): array
    {
        return [
            ['Budi Santoso', '0012345678', 'budi@sekolah.com', 'L'],
            ['Siti Aminah', '0012345679', 'siti@sekolah.com', 'P'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}