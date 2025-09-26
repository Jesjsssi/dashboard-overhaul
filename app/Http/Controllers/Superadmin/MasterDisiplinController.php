<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\MasterDisiplin;
use Illuminate\Http\Request;

class MasterDisiplinController extends Controller
{
    public function index()
    {
        $master_disiplin = MasterDisiplin::all();
        return view('superadmin.master_displin.index', compact('master_disiplin'));
    }

    public function create()
    {
        return view('superadmin.master_displin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'remark' => 'required|string',
        ]);

        MasterDisiplin::create([
            'remark' => $request->remark,
        ]);

        return redirect()->route('superadmin.master-disiplin')
            ->with('success', 'Data master disiplin berhasil ditambahkan');
    }

    public function edit($id)
    {
        $master_disiplin = MasterDisiplin::findOrFail($id);
        return view('superadmin.master_displin.edit', compact('master_disiplin'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'remark' => 'required|string',
        ]);

        $master_disiplin = MasterDisiplin::findOrFail($id);
        $master_disiplin->update([
            'remark' => $request->remark,
        ]);

        return redirect()->route('superadmin.master-disiplin')
            ->with('success', 'Data master disiplin berhasil diperbarui');
    }

    public function destroy($id)
    {
        $master_disiplin = MasterDisiplin::findOrFail($id);
        $master_disiplin->delete();

        return redirect()->route('superadmin.master-disiplin')
            ->with('success', 'Data master disiplin berhasil dihapus');
    }
}
