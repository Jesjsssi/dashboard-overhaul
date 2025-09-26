<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSubDisiplin extends Model
{
    use HasFactory;

    protected $table = 'master_sub_disiplin';
    protected $primaryKey = 'id_sub_disiplin';
    protected $fillable = ['remark', 'id_disiplin'];

    public function disiplin()
    {
        return $this->belongsTo(MasterDisiplin::class, 'id_disiplin', 'id_disiplin');
    }
}
