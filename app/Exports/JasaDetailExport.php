<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class JasaDetailExport implements FromCollection, WithHeadings, WithEvents
{
    protected Collection $details;
    protected Collection $jasas;

    /** @var array<int,int> */
    private array $groupHeaderRowIndexes = [];

    public function __construct(Collection $details, Collection $jasas)
    {
        $this->details = $details;
        $this->jasas = $jasas->keyBy('id_jasa');
    }

    public function title(): string
    {
        $date = now()->format('d-m-Y');
        return "Detail Progress {$date}";
    }

    public function headings(): array
    {
        return [
            'Kode Jasa',
            'Judul Kontrak / Step',
            'Plan Start',
            'Plan Finish',
            'Actual Start',
            'Actual Finish',
            'Plan Progress',
            'Actual Progress',
        ];
    }

    public function collection(): Collection
    {
        $rows = [];
        $currentRowIndex = 1; // will add 1 for headings later

        // Kelompokkan detail per Jasa
        $grouped = $this->details->groupBy('id_tamu');

        foreach ($grouped as $jasaId => $detailRows) {
            $jasa = $this->jasas->get($jasaId);

            // Hitung ringkasan progress (rata-rata sederhana dari nilai yang tidak null)
            $planProgressValues = $detailRows->pluck('plan_progress')->filter(fn($v) => $v !== null && $v !== '');
            $actualProgressValues = $detailRows->pluck('actual_progress')->filter(fn($v) => $v !== null && $v !== '');

            $avgPlan = $planProgressValues->count() ? round($planProgressValues->avg(), 0) : null;
            $avgActual = $actualProgressValues->count() ? round($actualProgressValues->avg(), 0) : null;

            // Baris header per Jasa
            $rows[] = [
                optional($jasa)->kode_jasa,
                optional($jasa)->judul_kontrak,
                '', // Tanggal Plan dihapus
                '', // Tanggal Plan dihapus
                '', // Tanggal Actual dihapus
                '', // Tanggal Actual dihapus
                '', // Progress Plan dihapus
                '', // Progress Actual dihapus
            ];
            $currentRowIndex++;
            $this->groupHeaderRowIndexes[] = $currentRowIndex; 

            // Baris-baris step
            foreach ($detailRows as $d) {
                $formattedRow = [
                    optional($jasa)->kode_jasa,
                    optional($d->masterTahapan)->step
                    ?? optional($d->masterTahapan)->kategori
                    ?? optional($d->masterTahapan)->irkap,
                    $this->formatDate($d->plan_start),
                    $this->formatDate($d->plan_finish),
                    $this->formatDate($d->actual_start),
                    $this->formatDate($d->actual_finish),
                    $this->formatProgress($d->plan_progress),
                    $this->formatProgress($d->actual_progress),
                ];

                // Debug: Log data yang akan di-export
                Log::info('Export detail progress - Row data:', [
                    'jasa_id' => $jasaId,
                    'step' => optional($d->masterTahapan)->step,
                    'plan_start_raw' => $d->plan_start,
                    'plan_start_formatted' => $this->formatDate($d->plan_start),
                    'plan_finish_raw' => $d->plan_finish,
                    'plan_finish_formatted' => $this->formatDate($d->plan_finish),
                    'actual_start_raw' => $d->actual_start,
                    'actual_start_formatted' => $this->formatDate($d->actual_start),
                    'actual_finish_raw' => $d->actual_finish,
                    'actual_finish_formatted' => $this->formatDate($d->actual_finish),
                    'plan_progress_raw' => $d->plan_progress,
                    'plan_progress_formatted' => $this->formatProgress($d->plan_progress),
                    'actual_progress_raw' => $d->actual_progress,
                    'actual_progress_formatted' => $this->formatProgress($d->actual_progress),
                ]);

                $rows[] = $formattedRow;
                $currentRowIndex++;
            }
        }

        // Kembalikan sebagai koleksi; perhatikan bahwa Laravel Excel akan menambahkan headings di baris pertama
        return collect($rows);
    }

    /**
     * Format tanggal untuk export
     */
    private function formatDate($date)
    {
        if (!$date) {
            return null;
        }

        try {
            // Jika sudah berupa Carbon instance
            if ($date instanceof \Carbon\Carbon) {
                return $date->format('d/m/Y');
            }

            // Jika berupa string, coba parse
            if (is_string($date)) {
                $carbonDate = \Carbon\Carbon::parse($date);
                return $carbonDate->format('d/m/Y');
            }

            // Jika berupa DateTime
            if ($date instanceof \DateTime) {
                return $date->format('d/m/Y');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Format progress untuk export
     */
    private function formatProgress($progress)
    {
        if ($progress === null || $progress === '') {
            return null;
        }

        // Jika berupa angka, tambahkan % di akhir
        if (is_numeric($progress)) {
            return $progress . '%';
        }

        // Jika sudah berupa string dengan %, return as is
        if (is_string($progress) && str_contains($progress, '%')) {
            return $progress;
        }

        // Jika string tanpa %, tambahkan %
        if (is_string($progress)) {
            return $progress . '%';
        }

        return $progress;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Headings berada di baris 1; data mulai baris 2.
                // groupHeaderRowIndexes disimpan relatif ke data; kita offset +1 untuk headings
                $sheet = $event->sheet->getDelegate();

                // Auto-size kolom
                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Style baris header per Jasa: fill kuning dan bold
                $yellow = 'FFE701';
                $dataStartRow = 2;
                $runningRow = $dataStartRow;

                // Hitung index baris yang merupakan awal grup: setiap baris pertama untuk masing-masing jasa = baris header
                $groupStarts = [];
                $rowPointer = 2; // mulai setelah headings
                $grouped = $this->details->groupBy('id_tamu');
                foreach ($grouped as $detailRows) {
                    $groupStarts[] = $rowPointer; // baris header grup
                    $rowPointer += 1 + $detailRows->count();
                }

                foreach ($groupStarts as $rowIdx) {
                    $sheet->getStyle("A{$rowIdx}:H{$rowIdx}")->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($yellow);
                    $sheet->getStyle("A{$rowIdx}:H{$rowIdx}")->getFont()->setBold(true);
                }

                // Freeze header row
                $sheet->freezePane('A2');
            },
        ];
    }
}
