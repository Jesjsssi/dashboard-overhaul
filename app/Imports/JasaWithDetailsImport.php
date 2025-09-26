<?php

namespace App\Imports;

use App\Models\Jasa;
use App\Models\DetailProgress;
use App\Models\MasterTahapan;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class JasaWithDetailsImport implements WithMultipleSheets
{
    protected $idEps;

    public function __construct($idEps)
    {
        $this->idEps = $idEps;
    }

    public function sheets(): array
    {
        return [
            0 => new JasaImport($this->idEps),        // Sheet pertama (index 0)
            1 => new DetailProgressImport($this->idEps), // Sheet kedua (index 1)
        ];
    }
}
