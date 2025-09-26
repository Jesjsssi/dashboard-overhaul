<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jasa extends Model
{
    use HasFactory;

    protected $table = 'jasa';
    protected $primaryKey = 'id_jasa';

    protected $fillable = [
        'id_eps',
        'kode_jasa',
        'judul_kontrak',
        'id_disiplin',
        'id_sub_disiplin',
        'planner',
        'wo',
        'pr',
        'po',
        'pemenang',
        'keterangan'
    ];

    public function eps()
    {
        return $this->belongsTo(EPS::class, 'id_eps');
    }

    public function disiplin()
    {
        return $this->belongsTo(MasterDisiplin::class, 'id_disiplin');
    }

    public function subDisiplin()
    {
        return $this->belongsTo(MasterSubDisiplin::class, 'id_sub_disiplin');
    }
}
