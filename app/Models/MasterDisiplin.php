<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDisiplin extends Model
{
    use HasFactory;

    protected $table = 'master_disiplin';
    protected $primaryKey = 'id_disiplin';
    public $timestamps = false;

    protected $fillable = [
        'id_disiplin',
        'remark',
    ];

    public function project()
    {
        return $this->hasMany(Project::class, 'id_disiplin');
    }

    public function subDisiplins()
    {
        return $this->hasMany(MasterSubDisiplin::class, 'id_disiplin', 'id_disiplin');
    }
}
