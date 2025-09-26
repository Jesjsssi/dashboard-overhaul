<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\MasterSubDisiplin;
use App\Models\MasterDisiplin;
use Illuminate\Http\Request;

class MasterSubDisiplinController extends Controller
{
    public function index()
    {
        $subDisiplin = MasterSubDisiplin::with('disiplin')->get();
        return view('Superadmin.master_sub_disiplin.index', compact('subDisiplin'));
    }

    public function create()
    {
        $disiplins = MasterDisiplin::all();
        return view('Superadmin.master_sub_disiplin.create', compact('disiplins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'remark' => 'required|string',
            'id_disiplin' => 'required|exists:master_disiplin,id_disiplin'
        ]);

        MasterSubDisiplin::create($request->all());
        return redirect()->route('superadmin.master-sub-disiplin')->with('success', 'Sub Disiplin berhasil ditambahkan');
    }

    public function edit($id)
    {
        $subDisiplin = MasterSubDisiplin::findOrFail($id);
        $disiplins = MasterDisiplin::all();
        return view('Superadmin.master_sub_disiplin.edit', compact('subDisiplin', 'disiplins'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'remark' => 'required|string',
            'id_disiplin' => 'required|exists:master_disiplin,id_disiplin'
        ]);

        $subDisiplin = MasterSubDisiplin::findOrFail($id);
        $subDisiplin->update($request->all());
        return redirect()->route('superadmin.master-sub-disiplin')->with('success', 'Sub Disiplin berhasil diperbarui');
    }

    public function destroy($id)
    {
        $subDisiplin = MasterSubDisiplin::findOrFail($id);
        $subDisiplin->delete();
        return redirect()->route('superadmin.master-sub-disiplin')->with('success', 'Sub Disiplin berhasil dihapus');
    }

    public function getByDisiplin($id_disiplin)
    {
        $subDisiplins = MasterSubDisiplin::where('id_disiplin', $id_disiplin)->get();
        return response()->json($subDisiplins);
    }
}
