<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\DetailProgress;
use App\Models\MasterTahapan;
use App\Models\Jasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailProgressController extends Controller
{
    public function show($id_jasa)
    {
        $jasa = Jasa::findOrFail($id_jasa);

        // Filter tahapans by the 'jasa' category
        $tahapans = MasterTahapan::where('kategori', 'jasa')
            ->orderBy('urutan')
            ->get();

        $details = DetailProgress::where('id_tamu', $id_jasa)->get();
        return view('Superadmin.jasa.show', compact('jasa', 'tahapans', 'details'));
    }

    public function updateActual(Request $request, $id_jasa)
    {
        try {
            DB::beginTransaction();
            $actual_progress = $request->input('actual_progress', []);
            $actual_start = $request->input('actual_start', []);
            $actual_finish = $request->input('actual_finish', []);

            foreach ($actual_progress as $id_tahapan => $progress) {
                DetailProgress::updateOrCreate(
                    [
                        'id_tamu' => $id_jasa,
                        'id_kategori' => $id_tahapan
                    ],
                    [
                        'actual_progress' => $progress,
                        'actual_start' => $actual_start[$id_tahapan] ?? null,
                        'actual_finish' => $actual_finish[$id_tahapan] ?? null
                    ]
                );
            }
            DB::commit();
            return redirect()->back()->with('success', 'Progress actual berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updatePlan(Request $request, $id_jasa)
    {
        try {
            DB::beginTransaction();

            $plan_progress = $request->input('plan_progress', []);
            $plan_start = $request->input('plan_start', []);
            $plan_finish = $request->input('plan_finish', []);

            foreach ($plan_progress as $id_tahapan => $progress) {
                DetailProgress::updateOrCreate(
                    [
                        'id_tamu' => $id_jasa,
                        'id_kategori' => $id_tahapan
                    ],
                    [
                        'plan_progress' => $progress,
                        'plan_start' => $plan_start[$id_tahapan] ?? null,
                        'plan_finish' => $plan_finish[$id_tahapan] ?? null
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Progress plan berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
