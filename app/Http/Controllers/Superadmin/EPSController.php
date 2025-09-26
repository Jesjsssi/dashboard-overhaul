<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EPS;
use App\Models\Project;
use App\Models\Histori;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EPSController extends Controller
{
    public function index()
    {
        $epsList = EPS::orderBy('id_eps', 'desc')->get();
        return view('superadmin.eps.index', compact('epsList'));
    }

    public function create()
    {
        return view('superadmin.eps.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'remark' => 'nullable',
            'tahun' => 'required|integer|min:2000|max:2099',
            'jenis_project' => 'required|in:' . implode(',', array_keys(EPS::getJenisProjectList())),
            'cutoff_date' => 'required|date'
        ];

        // Tambahkan validasi execution_date hanya jika jenis project bukan OH atau Routine
        if (!in_array($request->jenis_project, EPS::$jenisNoExecutionDate)) {
            $rules['execution_date'] = 'required|date';
        }

        $validator = Validator::make($request->all(), $rules, [
            'execution_date.required' => 'Tanggal eksekusi harus diisi',
            'execution_date.date' => 'Format tanggal eksekusi tidak valid',
            'tahun.required' => 'Tahun harus diisi',
            'tahun.integer' => 'Tahun harus berupa angka',
            'tahun.min' => 'Tahun minimal 2000',
            'tahun.max' => 'Tahun maksimal 2099',
            'jenis_project.required' => 'Jenis project harus dipilih',
            'jenis_project.in' => 'Jenis project tidak valid',
            'cutoff_date.required' => 'Cut Off Date harus diisi',
            'cutoff_date.date' => 'Format Cut Off Date tidak valid'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('superadmin.eps.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Jika jenis project adalah OH atau Routine, set execution_date ke null
        if (in_array($request->jenis_project, EPS::$jenisNoExecutionDate)) {
            if ($request->jenis_project === EPS::JENIS_OH) {
                // Set tanggal eksekusi ke 1 Januari tahun yang diinput
                $request->merge(['execution_date' => $request->tahun . '-01-01']);
            } else {
                // Untuk jenis Routine, tetap set null
                $request->merge(['execution_date' => null]);
            }
        }

        EPS::create($request->all());

        return redirect()
            ->route('superadmin.eps')
            ->with('success', 'Data EPS berhasil ditambahkan');
    }

    public function edit($id)
    {
        $eps = EPS::findOrFail($id);
        return view('superadmin.eps.edit', compact('eps'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'remark' => 'nullable',
            'tahun' => 'required|integer|min:2000|max:2099',
            'jenis_project' => 'required|in:' . implode(',', array_keys(EPS::getJenisProjectList())),
            'cutoff_date' => 'required|date'
        ];

        // Tambahkan validasi execution_date hanya jika jenis project bukan OH atau Routine
        if (!in_array($request->jenis_project, EPS::$jenisNoExecutionDate)) {
            $rules['execution_date'] = 'required|date';
        }

        $validator = Validator::make($request->all(), $rules, [
            'execution_date.required' => 'Tanggal eksekusi harus diisi',
            'execution_date.date' => 'Format tanggal eksekusi tidak valid',
            'tahun.required' => 'Tahun harus diisi',
            'tahun.integer' => 'Tahun harus berupa angka',
            'tahun.min' => 'Tahun minimal 2000',
            'tahun.max' => 'Tahun maksimal 2099',
            'jenis_project.required' => 'Jenis project harus dipilih',
            'jenis_project.in' => 'Jenis project tidak valid',
            'cutoff_date.required' => 'Cut Off Date harus diisi',
            'cutoff_date.date' => 'Format Cut Off Date tidak valid'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('superadmin.eps.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Ambil data EPS
            $eps = EPS::findOrFail($id);
            $tahunLama = $eps->tahun;
            $tahunBaru = $request->tahun;

            // Set execution_date berdasarkan jenis project
            if (in_array($request->jenis_project, EPS::$jenisNoExecutionDate)) {
                if ($request->jenis_project === EPS::JENIS_OH) {
                    // Set tanggal eksekusi ke 1 Januari tahun yang diinput
                    $request->merge(['execution_date' => $request->tahun . '-01-01']);
                } else {
                    // Untuk jenis Routine, tetap set null
                    $request->merge(['execution_date' => null]);
                }
            }

            Log::info('Memproses perubahan tahun EPS', [
                'id_eps' => $id,
                'tahun_lama' => $tahunLama,
                'tahun_baru' => $tahunBaru
            ]);

            // Ambil semua project yang terkait dengan EPS ini
            $projects = Project::where('id_eps', $id)
                ->whereNull('delete_date')  // Hanya ambil project yang aktif
                ->get();

            Log::info('Project yang terkait dengan EPS', [
                'id_eps' => $id,
                'jumlah_project' => $projects->count(),
                'project_ids' => $projects->pluck('id_project')
            ]);

            // Update EPS
            $eps->update($request->all());

            DB::commit();
            Log::info('Transaksi berhasil di-commit');

            return redirect()
                ->route('superadmin.eps')
                ->with('success', 'Data EPS berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saat memperbarui EPS', [
                'id_eps' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('superadmin.eps.edit', $id)
                ->with('error', 'Gagal memperbarui data EPS. ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Cek apakah ada project yang terkait dengan EPS ini
            $projects = Project::where('id_eps', $id)->get();

            if ($projects->count() > 0) {
                // Jika ada project terkait, set delete_date pada project
                foreach ($projects as $project) {
                    $project->update(['delete_date' => now()]);
                }

                Log::info('EPS dihapus dengan project terkait', [
                    'id_eps' => $id,
                    'jumlah_project' => $projects->count()
                ]);
            }

            // Hapus EPS
            $eps = EPS::findOrFail($id);
            $eps->delete();

            DB::commit();

            return redirect()
                ->route('superadmin.eps')
                ->with('success', 'Data EPS berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saat menghapus EPS', [
                'id_eps' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('superadmin.eps')
                ->with('error', 'Gagal menghapus data EPS. ' . $e->getMessage());
        }
    }

    public function setDefault($id)
    {
        // Reset semua EPS ke default = 0
        EPS::query()->update(['default' => 0]);
        // Set EPS terpilih ke default = 1
        $eps = EPS::findOrFail($id);
        $eps->default = 1;
        $eps->save();

        return redirect()->back()->with('success', 'EPS berhasil dijadikan default.');
    }
}
