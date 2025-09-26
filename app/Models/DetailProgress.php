<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailProgress extends Model
{
    use HasFactory;

    protected $table = 'detail_progress';
    protected $primaryKey = 'id_detail_progress';

    protected $fillable = [
        'id_tamu',
        'id_kategori',
        'plan_start',
        'plan_finish',
        'actual_start',
        'actual_finish',
        'plan_progress',
        'actual_progress',
        'early_start',
        'early_finish'
    ];

    protected $dates = [
        'plan_start',
        'plan_finish',
        'actual_start',
        'actual_finish',
        'early_start',
        'early_finish'
    ];

    public function masterTahapan()
    {
        return $this->belongsTo(MasterTahapan::class, 'id_kategori');
    }
}