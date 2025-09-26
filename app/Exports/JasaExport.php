<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class JasaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected Collection $jasas;

    public function __construct(Collection $jasas)
    {
        $this->jasas = $jasas;
    }

    public function title(): string
    {
        $date = now()->format('d-m-Y');
        return "Data Jasa {$date}";
    }

    public function collection(): Collection
    {
        return $this->jasas;
    }

    public function headings(): array
    {
        return [
            'Kode Jasa',
            'Judul Kontrak',
            'Disiplin',
            'Planner',
            'WO',
            'PR',
            'PO',
            'Pemenang',
            'Keterangan',
        ];
    }

    public function map($jasa): array
    {
        return [
            $jasa->kode_jasa,
            $jasa->judul_kontrak,
            optional($jasa->disiplin)->remark,
            $jasa->planner,
            $jasa->wo,
            $jasa->pr,
            $jasa->po,
            $jasa->pemenang,
            $jasa->keterangan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->jasas->count() + 1;

        return [
            // Header style
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
            ],
            // Border for all data including header
            'A1:I' . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}