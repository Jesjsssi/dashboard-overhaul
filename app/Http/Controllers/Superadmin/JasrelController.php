<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\EPS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JasrelController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['eps', 'disiplin', 'subDisiplin']);

        // Only include projects with kategori Jasa or Material dan Jasa by default
        $query->whereIn('kategori', ['Jasa', 'Material dan Jasa']);

        // Filter by specific kategori if provided
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

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
        Log::info('Jasrel filter parameters:', [
            'id_eps' => $request->id_eps,
            'id_disiplin' => $request->id_disiplin,
            'kategori' => $request->kategori,
            'search' => $request->search
        ]);

        // Paginate results with 10 items per page
        $projects = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Append query parameters to pagination links
        $projects->appends($request->query());

        return view('superadmin.jasrel.index', compact('projects'));
    }
}
