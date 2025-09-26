<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\EPS;
use App\Models\MasterDisiplin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['eps', 'disiplin', 'subDisiplin']);

        // Filter berdasarkan EPS
        if ($request->has('id_eps')) {
            if ($request->filled('id_eps')) {
                $query->where('id_eps', $request->id_eps);
            }
        } else {
            // Jika tidak ada parameter id_eps sama sekali, gunakan EPS default
            $defaultEps = EPS::where('default', 1)->first();
            if ($defaultEps) {
                $query->where('id_eps', $defaultEps->id_eps);
            }
        }

        // Filter berdasarkan Disiplin
        if ($request->filled('id_disiplin')) {
            $query->where('id_disiplin', $request->id_disiplin);
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_rkap', 'like', "%{$search}%")
                    ->orWhere('tagno', 'like', "%{$search}%")
                    ->orWhere('remark', 'like', "%{$search}%");
            });
        }

        // Log untuk debugging
        Log::info('Project filter parameters:', [
            'id_eps' => $request->id_eps,
            'id_disiplin' => $request->id_disiplin,
            'search' => $request->search
        ]);

        // Paginate results with 10 items per page
        $projects = $query->orderBy('created_at', 'desc')->paginate(10);

        // Append query parameters to pagination links
        $projects->appends($request->query());

        return view('superadmin.project.index', compact('projects'));
    }

    public function create()
    {
        $eps_list = EPS::orderBy('tahun', 'desc')->orderBy('jenis_project')->get();
        $disiplin_list = MasterDisiplin::all();

        // Define kategori options
        $kategori_list = Project::$validKategori;

        // Ambil EPS default
        $defaultEps = EPS::where('default', 1)->first();

        return view('superadmin.project.create', compact('eps_list', 'disiplin_list', 'defaultEps', 'kategori_list'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_eps' => 'required|exists:eps,id_eps',
            'kode_rkap' => 'required|string|max:255',
            'id_disiplin' => 'required|exists:master_disiplin,id_disiplin',
            'id_sub_disiplin' => 'nullable|exists:master_sub_disiplin,id_sub_disiplin',
            'tagno' => 'required|string',
            'remark' => 'nullable|string',
            'weight_factor' => 'required|numeric|min:0',
            // Step date fields validation
            'step_1_date' => 'nullable|date',
            'step_2_date' => 'nullable|date',
            'step_3_date' => 'nullable|date',
            'step_4_date' => 'nullable|date',
            'step_5_date' => 'nullable|date',
            'step_6_date' => 'nullable|date',
            'step_7_date' => 'nullable|date',
            'step_8_date' => 'nullable|date',
            'step_9_date' => 'nullable|date',
            'step_10_date' => 'nullable|date',
            'step_11_date' => 'nullable|date',
            'step_12_date' => 'nullable|date',
            'step_13_date' => 'nullable|date',
            'step_14_date' => 'nullable|date',
            'step_15_date' => 'nullable|date',
            'kategori' => ['required', 'string', \Illuminate\Validation\Rule::in(Project::$validKategori)],
        ], [
            'id_eps.required' => 'EPS harus dipilih',
            'id_eps.exists' => 'EPS tidak valid',
            'kode_rkap.required' => 'Kode RKAP harus diisi',
            'kode_rkap.max' => 'Kode RKAP maksimal 255 karakter',
            'id_disiplin.required' => 'Disiplin harus dipilih',
            'id_disiplin.exists' => 'Disiplin tidak valid',
            'id_sub_disiplin.exists' => 'Sub Disiplin tidak valid',
            'tagno.required' => 'Tag No harus diisi',
            'remark.required' => 'Program harus diisi',
            'weight_factor.required' => 'Weight Factor harus diisi',
            'weight_factor.numeric' => 'Weight Factor harus berupa angka',
            'weight_factor.min' => 'Weight Factor minimal 0',
            // Step date fields validation messages
            'step_1_date.date' => 'Step 1 Date harus berupa tanggal yang valid',
            'step_2_date.date' => 'Step 2 Date harus berupa tanggal yang valid',
            'step_3_date.date' => 'Step 3 Date harus berupa tanggal yang valid',
            'step_4_date.date' => 'Step 4 Date harus berupa tanggal yang valid',
            'step_5_date.date' => 'Step 5 Date harus berupa tanggal yang valid',
            'step_6_date.date' => 'Step 6 Date harus berupa tanggal yang valid',
            'step_7_date.date' => 'Step 7 Date harus berupa tanggal yang valid',
            'step_8_date.date' => 'Step 8 Date harus berupa tanggal yang valid',
            'step_9_date.date' => 'Step 9 Date harus berupa tanggal yang valid',
            'step_10_date.date' => 'Step 10 Date harus berupa tanggal yang valid',
            'step_11_date.date' => 'Step 11 Date harus berupa tanggal yang valid',
            'step_12_date.date' => 'Step 12 Date harus berupa tanggal yang valid',
            'step_13_date.date' => 'Step 13 Date harus berupa tanggal yang valid',
            'step_14_date.date' => 'Step 14 Date harus berupa tanggal yang valid',
            'step_15_date.date' => 'Step 15 Date harus berupa tanggal yang valid',
            'kategori.required' => 'Kategori harus dipilih',
            'kategori.in' => 'Kategori tidak valid',
        ]);

        try {
            // Set default weight factor jika tidak diisi
            if (!$request->filled('weight_factor')) {
                $request->merge(['weight_factor' => 1]);
            }

            // Only get database fields, excluding _token, _method, etc.
            $data = $request->only([
                'id_eps', 'kode_rkap', 'id_disiplin', 'id_sub_disiplin',
                'tagno', 'remark', 'weight_factor', 'kategori',
                'step_1_date', 'step_2_date', 'step_3_date', 'step_4_date', 'step_5_date',
                'step_6_date', 'step_7_date', 'step_8_date', 'step_9_date', 'step_10_date',
                'step_11_date', 'step_12_date', 'step_13_date', 'step_14_date', 'step_15_date'
            ]);

            // Process the dates
            for ($i = 1; $i <= 15; $i++) {
                $field = "step_{$i}_date";
                // Keep null values as null, don't convert empty strings to dates
                if (empty($data[$field])) {
                    $data[$field] = null;
                }
            }

            // Add timestamps
            $data['created_at'] = now();
            $data['updated_at'] = now();

            // Use DB facade for direct insertion without Eloquent model
            $id = \Illuminate\Support\Facades\DB::table('project')->insertGetId($data);

            // Now retrieve the created project
            $project = Project::findOrFail($id);

            Log::info('Project berhasil dibuat', [
                'id' => $project->id, // Changed from id_project to id
                'tagno' => $project->tagno,
                'weight_factor' => $project->weight_factor,
                'step_dates' => [
                    'step_1' => $project->step_1_date,
                    'step_2' => $project->step_2_date,
                    'step_3' => $project->step_3_date,
                    'step_4' => $project->step_4_date,
                    'step_5' => $project->step_5_date,
                    'step_6' => $project->step_6_date,
                    'step_7' => $project->step_7_date,
                    'step_8' => $project->step_8_date,
                    'step_9' => $project->step_9_date,
                    'step_10' => $project->step_10_date,
                    'step_11' => $project->step_11_date,
                    'step_12' => $project->step_12_date,
                    'step_13' => $project->step_13_date,
                    'step_14' => $project->step_14_date,
                    'step_15' => $project->step_15_date,
                    'kategori' => $project->kategori
                ]
            ]);

            return redirect()
                ->route('superadmin.project')
                ->with('success', 'Data project berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Gagal membuat project', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('superadmin.project.create')
                ->with('error', 'Gagal menambahkan project. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $eps_list = EPS::orderBy('tahun', 'desc')->orderBy('jenis_project')->get();
        $disiplin_list = MasterDisiplin::all();
        $sub_disiplin_list = \App\Models\MasterSubDisiplin::all();

        // Ensure kategori_list is defined properly
        $kategori_list = Project::$validKategori;

        // Debug log untuk melihat nilai kategori pada project
        Log::info('Project kategori value on edit:', [
            'id' => $id,
            'kategori' => $project->kategori,
            'kategori_list' => $kategori_list,
        ]);

        // Ambil EPS default untuk fallback
        $defaultEps = EPS::where('default', 1)->first();

        return view('superadmin.project.edit', compact('project', 'eps_list', 'disiplin_list', 'sub_disiplin_list', 'defaultEps', 'kategori_list'));
    }

    public function update(Request $request, $id)
    {
        // Log request data
        Log::info('Update project request data:', [
            'id' => $id,
            'all_data' => $request->all(),
            'kode_rkap' => $request->kode_rkap,
            'kategori' => $request->kategori  // Log kategori value specifically
        ]);

        $request->validate([
            'id_eps' => 'required|exists:eps,id_eps',
            'kode_rkap' => 'required|string|max:255',
            'id_disiplin' => 'required|exists:master_disiplin,id_disiplin',
            'id_sub_disiplin' => 'nullable|exists:master_sub_disiplin,id_sub_disiplin',
            'tagno' => 'nullable|string',
            'remark' => 'nullable|string',
            'weight_factor' => 'required|numeric|min:0',
            // Step date fields validation
            'step_1_date' => 'nullable|date',
            'step_2_date' => 'nullable|date',
            'step_3_date' => 'nullable|date',
            'step_4_date' => 'nullable|date',
            'step_5_date' => 'nullable|date',
            'step_6_date' => 'nullable|date',
            'step_7_date' => 'nullable|date',
            'step_8_date' => 'nullable|date',
            'step_9_date' => 'nullable|date',
            'step_10_date' => 'nullable|date',
            'step_11_date' => 'nullable|date',
            'step_12_date' => 'nullable|date',
            'step_13_date' => 'nullable|date',
            'step_14_date' => 'nullable|date',
            'step_15_date' => 'nullable|date',
            'kategori' => ['required', 'string', \Illuminate\Validation\Rule::in(Project::$validKategori)],
        ], [
            'id_eps.required' => 'EPS harus dipilih',
            'id_eps.exists' => 'EPS tidak valid',
            'kode_rkap.required' => 'Kode RKAP harus diisi',
            'kode_rkap.max' => 'Kode RKAP maksimal 255 karakter',
            'id_disiplin.required' => 'Disiplin harus dipilih',
            'id_disiplin.exists' => 'Disiplin tidak valid',
            'id_sub_disiplin.exists' => 'Sub Disiplin tidak valid',
            'tagno.required' => 'Tag No harus diisi',
            'weight_factor.required' => 'Weight Factor harus diisi',
            'weight_factor.numeric' => 'Weight Factor harus berupa angka',
            'weight_factor.min' => 'Weight Factor minimal 0',
            // Step date fields validation messages
            'step_1_date.date' => 'Step 1 Date harus berupa tanggal yang valid',
            'step_2_date.date' => 'Step 2 Date harus berupa tanggal yang valid',
            'step_3_date.date' => 'Step 3 Date harus berupa tanggal yang valid',
            'step_4_date.date' => 'Step 4 Date harus berupa tanggal yang valid',
            'step_5_date.date' => 'Step 5 Date harus berupa tanggal yang valid',
            'step_6_date.date' => 'Step 6 Date harus berupa tanggal yang valid',
            'step_7_date.date' => 'Step 7 Date harus berupa tanggal yang valid',
            'step_8_date.date' => 'Step 8 Date harus berupa tanggal yang valid',
            'step_9_date.date' => 'Step 9 Date harus berupa tanggal yang valid',
            'step_10_date.date' => 'Step 10 Date harus berupa tanggal yang valid',
            'step_11_date.date' => 'Step 11 Date harus berupa tanggal yang valid',
            'step_12_date.date' => 'Step 12 Date harus berupa tanggal yang valid',
            'step_13_date.date' => 'Step 13 Date harus berupa tanggal yang valid',
            'step_14_date.date' => 'Step 14 Date harus berupa tanggal yang valid',
            'step_15_date.date' => 'Step 15 Date harus berupa tanggal yang valid',
            'kategori.required' => 'Kategori harus dipilih',
            'kategori.in' => 'Kategori tidak valid',
        ]);

        try {
            $project = Project::findOrFail($id);

            // Log project data sebelum update
            Log::info('Project data before update:', [
                'id' => $project->id,
                'old_data' => $project->toArray(),
                'old_kategori' => $project->kategori  // Log current kategori specifically
            ]);

            // Only get database fields, excluding _token, _method, etc.
            $data = $request->only([
                'id_eps', 'kode_rkap', 'id_disiplin', 'id_sub_disiplin',
                'tagno', 'remark', 'weight_factor', 'kategori',
                'step_1_date', 'step_2_date', 'step_3_date', 'step_4_date', 'step_5_date',
                'step_6_date', 'step_7_date', 'step_8_date', 'step_9_date', 'step_10_date',
                'step_11_date', 'step_12_date', 'step_13_date', 'step_14_date', 'step_15_date'
            ]);

            for ($i = 1; $i <= 15; $i++) {
                $field = "step_{$i}_date";
                // Keep null values as null, don't convert empty strings to dates
                if (empty($data[$field])) {
                    $data[$field] = null;
                }
            }

            // Ensure kategori is included in the update data
            if ($request->has('kategori')) {
                $data['kategori'] = $request->kategori;
            }

            $project->update($data);

            // Log project data setelah update
            Log::info('Project data after update:', [
                'id' => $project->id,
                'new_data' => $project->fresh()->toArray(),
                'new_kategori' => $project->fresh()->kategori,  // Log new kategori specifically
                'step_dates' => [
                    'step_1' => $project->fresh()->step_1_date,
                    'step_2' => $project->fresh()->step_2_date,
                    'step_3' => $project->fresh()->step_3_date,
                    'step_4' => $project->fresh()->step_4_date,
                    'step_5' => $project->fresh()->step_5_date,
                    'step_6' => $project->fresh()->step_6_date,
                    'step_7' => $project->fresh()->step_7_date,
                    'step_8' => $project->fresh()->step_8_date,
                    'step_9' => $project->fresh()->step_9_date,
                    'step_10' => $project->fresh()->step_10_date,
                    'step_11' => $project->fresh()->step_11_date,
                    'step_12' => $project->fresh()->step_12_date,
                    'step_13' => $project->fresh()->step_13_date,
                    'step_14' => $project->fresh()->step_14_date,
                    'step_15' => $project->fresh()->step_15_date,
                ]
            ]);

            return redirect()
                ->route('superadmin.project')
                ->with('success', 'Data project berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate project', [
                'id' => $id, // Changed from id_project to id
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('superadmin.project.edit', $id)
                ->with('error', 'Gagal memperbarui project. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);

            // Log informasi project yang akan dihapus
            Log::info('Menghapus project:', [
                'id' => $project->id, // Changed from id_project to id
                'kode_rkap' => $project->kode_rkap,
                'tagno' => $project->tagno
            ]);

            $project->delete();

            return redirect()
                ->route('superadmin.project')
                ->with('success', 'Data project berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus project', [
                'id' => $id, // Changed from id_project to id
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('superadmin.project')
                ->with('error', 'Gagal menghapus project. ' . $e->getMessage());
        }
    }
}
