<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class JasaWithDetailsExport implements WithMultipleSheets
{
    protected Collection $jasas;
    protected Collection $details;

    public function __construct(Collection $jasas, Collection $details)
    {
        $this->jasas = $jasas;
        $this->details = $details;
    }

    public function sheets(): array
    {
        $date = now()->format('d-m-Y');

        return [
            "Jasa {$date}" => new JasaExport($this->jasas),
            "Detail {$date}" => new JasaDetailExport($this->details, $this->jasas),
        ];
    }
}