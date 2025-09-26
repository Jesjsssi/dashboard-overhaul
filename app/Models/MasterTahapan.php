<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTahapan extends Model
{
    use HasFactory;

    protected $table = 'master_tahapan';
    protected $fillable = ['kategori', 'step', 'weight_factor', 'irkap', 'urutan'];


    //Option IRKAP
    const NOTIF = '1';
    const REKOMENDASI = '2';
    const DETAIL_JOB_PLAN = '3';
    const ORDER = '4';
    const PR = '5';
    const PREBID = '6';
    const TENDER = '7';
    const PO = '8';
    const GR = '9';
    const GI = '10';
    const EXECUTION = '11';

    const TEST_PERFORMANCE = '12';
    const SA = '13';
    const TECO = '14';
    const CLOSE_ORDER = '15';



    // Daftar semua tahapan
    public static function getTahapanList()
    {
        return [
            self::NOTIF => 'Notif',
            self::REKOMENDASI => 'Rekomendasi',
            self::DETAIL_JOB_PLAN => 'Detail Job Plan',
            self::ORDER => 'Order',
            self::PR => 'PR',
            self::PREBID => 'Prebid',
            self::TENDER => 'Tender',
            self::PO => 'PO',
            self::GR => 'GR',
            self::GI => 'GI',
            self::EXECUTION => 'Execution',
            self::TEST_PERFORMANCE => 'Test Performance',
            self::SA => 'SA',
            self::TECO => 'Tech.Fbck&Upd Order',
            self::CLOSE_ORDER => 'Close Order',
        ];
    }

}
