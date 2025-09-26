<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterTahapan;

class MasterTahapanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahapan = [
            [
                'kategori' => 'jasa',
                'irkap' => 1,
                'step' => 'Crtd & Rel. Notif',
                'weight_factor' => 7.3,
                'urutan' => 1,
            ],
            [
                'kategori' => 'jasa',
                'irkap' => 2,
                'step' => 'Rekomendasi',
                'weight_factor' => 7.3,
                'urutan' => 2,
            ],
            [
                'kategori' => 'jasa',
                'irkap' => 3,
                'step' => 'Crtd & Detail Job Plan',
                'weight_factor' => 7.3,
                'urutan' => 3,
            ],

            [
                'kategori' => 'jasa',
                'irkap' => 4,
                'step' => 'Permit & Rel. Order (WO)',
                'weight_factor' => 7.3,
                'urutan' => 4,
            ],
            [
                'kategori' => 'jasa',
                'irkap' => 6,
                'step' => 'Crtd & Rel. PR',
                'weight_factor' => 7.3,
                'urutan' => 5,
            ],

            [
                'kategori' => 'jasa',
                'irkap' => 7,
                'step' => 'Prebid',
                'weight_factor' => 13.6,
                'urutan' => 6,
            ],
            [
                'kategori' => 'jasa',
                'irkap' => 7,
                'step' => 'Bid Open',
                'weight_factor' => 9.1,
                'urutan' => 7,
            ],
            [
                'kategori' => 'jasa',
                'irkap' => 7,
                'step' => 'LHPP',
                'weight_factor' => 9.1,
                'urutan' => 8,
            ],
            [
                'kategori' => 'jasa',
                'irkap' => 8,
                'step' => 'SP3MK/SPB/PO',
                'weight_factor' => 18.2,
                'urutan' => 9,
            ],
            // material
            [
                'kategori' => 'material',
                'irkap' => 1,
                'step' => 'Crtd & Rel. Notif',
                'weight_factor' => 7.3,
                'urutan' => 1,
            ],
            [
                'kategori' => 'material',
                'irkap' => 2,
                'step' => 'Rekomendasi',
                'weight_factor' => 7.3,
                'urutan' => 2,
            ],
            [
                'kategori' => 'material',
                'irkap' => 3,
                'step' => 'Crtd & Detail Job Plan(KIMAP)',
                'weight_factor' => 7.3,
                'urutan' => 3,
            ],

            [
                'kategori' => 'material',
                'irkap' => 4,
                'step' => 'Permit & Rel. Order (WO)',
                'weight_factor' => 7.3,
                'urutan' => 4,
            ],
            [
                'kategori' => 'material',
                'irkap' => 5,
                'step' => 'Material Reservation',
                'weight_factor' => 7.3,
                'urutan' => 5,
            ],

            [
                'kategori' => 'material',
                'irkap' => 6,
                'step' => 'Crtd & Rel. PR',
                'weight_factor' => 13.6,
                'urutan' => 6,
            ],
            [
                'kategori' => 'material',
                'irkap' => 7,
                'step' => 'Pre Bid',
                'weight_factor' => 9.1,
                'urutan' => 7,
            ],
            [
                'kategori' => 'material',
                'irkap' => 7,
                'step' => 'Bid Open',
                'weight_factor' => 9.1,
                'urutan' => 8,
            ],
            [
                'kategori' => 'material',
                'irkap' => 8,
                'step' => 'Crtd & Rel. PO',
                'weight_factor' => 18.2,
                'urutan' => 9,
            ],
            [
                'kategori' => 'material',
                'irkap' => 8,
                'step' => 'Manufacture',
                'weight_factor' => 13.6,
                'urutan' => 10,
            ],
            [
                'kategori' => 'material',
                'irkap' => 8,
                'step' => 'Delivery',
                'weight_factor' => 13.6,
                'urutan' => 11,
            ],
            [
                'kategori' => 'material',
                'irkap' => 9,
                'step' => 'Good Issued',
                'weight_factor' => 13.6,
                'urutan' => 12,
            ],
            [
                'kategori' => 'material',
                'irkap' => 10,
                'step' => 'Good Receive Material',
                'weight_factor' => 13.6,
                'urutan' => 13,
            ],

            //eksekusi
            [
                'kategori' => 'eksekusi',
                'irkap' => 11,
                'step' => 'Job Execution',
                'weight_factor' => 13.6,
                'urutan' => 1,
            ],
            [
                'kategori' => 'eksekusi',
                'irkap' => 12,
                'step' => 'Test Performance',
                'weight_factor' => 13.6,
                'urutan' => 2,
            ],
            [
                'kategori' => 'eksekusi',
                'irkap' => 13,
                'step' => 'Prepare& Apprvd SA',
                'weight_factor' => 13.6,
                'urutan' => 3,
            ],
            [
                'kategori' => 'eksekusi',
                'irkap' => 14,
                'step' => 'Tech.Fbck&Upd Order',
                'weight_factor' => 13.6,
                'urutan' => 4,
            ],
            [
                'kategori' => 'eksekusi',
                'irkap' => 15,
                'step' => 'Settle.&Order Closed',
                'weight_factor' => 13.6,
                'urutan' => 5,
            ]

        ];

        foreach ($tahapan as $item) {
            MasterTahapan::create($item);
        }
    }
}
