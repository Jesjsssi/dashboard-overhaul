<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'project';
    protected $primaryKey = 'id'; // Changed from 'id_project' to match table schema
    public $timestamps = false;

    // Add valid kategori options
    public static $validKategori = ['Jasa', 'Material', 'Material dan Jasa'];

    protected $fillable = [
        'id_eps',
        'kode_rkap',
        'id_disiplin',
        'id_sub_disiplin',
        'tagno',
        'remark',
        'weight_factor',
        'step_1_date',
        'step_2_date',
        'step_3_date',
        'step_4_date',
        'step_5_date',
        'step_6_date',
        'step_7_date',
        'step_8_date',
        'step_9_date',
        'step_10_date',
        'step_11_date',
        'step_12_date',
        'step_13_date',
        'step_14_date',
        'step_15_date',
        'kategori',
    ];

    protected $casts = [
        'weight_factor' => 'float',
        'id_sub_disiplin' => 'integer',
        'step_1_date' => 'date',
        'step_2_date' => 'date',
        'step_3_date' => 'date',
        'step_4_date' => 'date',
        'step_5_date' => 'date',
        'step_6_date' => 'date',
        'step_7_date' => 'date',
        'step_8_date' => 'date',
        'step_9_date' => 'date',
        'step_10_date' => 'date',
        'step_11_date' => 'date',
        'step_12_date' => 'date',
        'step_13_date' => 'date',
        'step_14_date' => 'date',
        'step_15_date' => 'date',
        
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