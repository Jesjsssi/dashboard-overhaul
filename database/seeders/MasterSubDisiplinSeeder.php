<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterSubDisiplin;

class MasterSubDisiplinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subDisiplins = [
            ['remark' => 'Cleaning', 'id_disiplin' => 1],
            ['remark' => 'Overhaul', 'id_disiplin' => 1],
            ['remark' => 'Minor Boiler', 'id_disiplin' => 2],
            ['remark' => 'Major Boiler', 'id_disiplin' => 2],
            ['remark' => 'Minor SWD', 'id_disiplin' => 3],
            ['remark' => 'Major SWD', 'id_disiplin' => 3],
            ['remark' => 'Minor Jetty', 'id_disiplin' => 4],
            ['remark' => 'Major Jetty', 'id_disiplin' => 4],
            ['remark' => 'Refurbish', 'id_disiplin' => 5],
            ['remark' => 'Overhaul', 'id_disiplin' => 5],
            ['remark' => 'Overhaul LLDI', 'id_disiplin' => 5],
            ['remark' => 'Overhaul Motor', 'id_disiplin' => 6],
            ['remark' => 'Overhaul Generator', 'id_disiplin' => 6],
            ['remark' => 'Instrument Equipment', 'id_disiplin' => 6],
            ['remark' => 'Electrical Equipment', 'id_disiplin' => 6],
            
       
        ];

        foreach ($subDisiplins as $subDisiplin) {
            MasterSubDisiplin::create($subDisiplin);
        }
    }
}
