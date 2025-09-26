<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPS extends Model
{
    use HasFactory;

    protected $table = 'eps';
    protected $primaryKey = 'id_eps';
    public $incrementing = true;

    protected $fillable = [
        'remark',
        'execution_date',
        'tahun',
        'jenis_project',
        'default',
        'cutoff_date'
    ];

    protected $casts = [
        'execution_date' => 'date',
        'tahun' => 'integer',
        'cutoff_date' => 'date'
    ];

    // Konstanta untuk jenis project
    const JENIS_OH = 'Overhaul';
    const JENIS_TA = 'Turn Around';
    const JENIS_PIT_STOP = 'Stop Unit';
    const JENIS_COC = 'Change of Catalyst';
    const JENIS_ROUTINE = 'Routine';

    // Daftar jenis project yang tidak memerlukan execution date
    public static $jenisNoExecutionDate = [
        self::JENIS_OH,
        self::JENIS_ROUTINE
    ];

    // Daftar semua jenis project
    public static function getJenisProjectList()
    {
        return [
            self::JENIS_OH => 'Overhaul',
            self::JENIS_TA => 'Turn Around',
            self::JENIS_PIT_STOP => 'Stop Unit',
            self::JENIS_COC => 'Change of Catalyst',
            self::JENIS_ROUTINE => 'Routine',
        ];
    }
}
