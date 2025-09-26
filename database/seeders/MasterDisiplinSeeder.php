<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterDisiplin;

class MasterDisiplinSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk master disiplin.
     */
    public function run(): void
    {
        $disiplins = [
            ['remark' => 'Tanki'],
            ['remark' => 'Boiler'],
            ['remark' => 'SWD'],
            ['remark' => 'Jetty'],
            ['remark' => 'Rotating'],
            ['remark' => 'Elec & Inst'],
            ['remark' => 'Supporting'],

        ];

        foreach ($disiplins as $disiplin) {
            MasterDisiplin::create($disiplin);
        }
    }
}
