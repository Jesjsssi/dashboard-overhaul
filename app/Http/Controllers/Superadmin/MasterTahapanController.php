<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterTahapan;
use App\Models\EPS;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MasterTahapanController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterTahapan::query();

        // Filter berdasarkan kategori
        $selectedKategori = $request->kategori;

        // Jika tidak ada kategori yang dipilih dan bukan request pertama, tampilkan semua
        if (empty($selectedKategori)) {
            if (!$request->has('kategori')) {
                // Ini adalah request pertama kali, set default ke 'jasa'
                $selectedKategori = 'jasa';
                $query->where('kategori', $selectedKategori);
            }
            // Jika kategori kosong tapi ada di request (user memilih "Semua Kategori"), tampilkan semua
        } else {
            // User memilih kategori spesifik
            $query->where('kategori', $selectedKategori);
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kategori', 'like', "%{$search}%")
                    ->orWhere('step', 'like', "%{$search}%")
                    ->orWhere('weight_factor', 'like', "%{$search}%")
                    ->orWhere('irkap', 'like', "%{$search}%");
            });
        }

        $tahapan = $query->orderBy('kategori', 'asc')
            ->orderBy('urutan', 'asc')
            ->get();
        $kategori = MasterTahapan::select('kategori')->distinct()->get();
        return view('Superadmin.master_tahapan.index', compact('tahapan', 'kategori', 'selectedKategori'));
    }

    public function create(Request $request)
    {
        $kategori = MasterTahapan::select('kategori')->distinct()->get();
        $selectedKategori = $request->kategori;
        return view('Superadmin.master_tahapan.create', compact('kategori', 'selectedKategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required',
            'step' => 'required',
            'weight_factor' => 'required|numeric|min:0|max:100',
            'irkap' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Mendapatkan urutan terakhir untuk kategori yang dipilih
                $lastUrutan = MasterTahapan::where('kategori', $request->kategori)
                    ->max('urutan');

                // Membuat data baru dengan urutan + 1
                MasterTahapan::create([
                    'kategori' => $request->kategori,
                    'step' => $request->step,
                    'weight_factor' => $request->weight_factor,
                    'irkap' => $request->irkap,
                    'urutan' => is_null($lastUrutan) ? 1 : $lastUrutan + 1
                ]);
            });

            // Redirect dengan mempertahankan filter kategori
            return redirect()->route('superadmin.master-tahapan', ['kategori' => $request->kategori])
                ->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id, Request $request)
    {
        $tahapan = MasterTahapan::findOrFail($id);
        $selectedKategori = $request->kategori;
        return view('Superadmin.master_tahapan.edit', compact('tahapan', 'selectedKategori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required',
            'step' => 'required',
            'weight_factor' => 'required|numeric|min:0|max:100',
            'irkap' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $tahapan = MasterTahapan::findOrFail($id);
                $oldKategori = $tahapan->kategori;
                $oldUrutan = $tahapan->urutan;

                // Jika kategori berubah
                if ($oldKategori !== $request->kategori) {
                    // Update urutan di kategori lama
                    MasterTahapan::where('kategori', $oldKategori)
                        ->where('urutan', '>', $oldUrutan)
                        ->get()
                        ->each(function ($item) {
                            $item->update(['urutan' => $item->urutan - 1]);
                        });

                    // Dapatkan urutan terakhir di kategori baru
                    $lastUrutan = MasterTahapan::where('kategori', $request->kategori)
                        ->max('urutan');

                    // Update data dengan urutan baru
                    $tahapan->update([
                        'kategori' => $request->kategori,
                        'step' => $request->step,
                        'weight_factor' => $request->weight_factor,
                        'irkap' => $request->irkap,
                        'urutan' => is_null($lastUrutan) ? 1 : $lastUrutan + 1
                    ]);
                } else {
                    // Jika kategori tidak berubah, update data tanpa mengubah urutan
                    $tahapan->update([
                        'kategori' => $request->kategori,
                        'step' => $request->step,
                        'weight_factor' => $request->weight_factor,
                        'irkap' => $request->irkap
                    ]);
                }
            });

            // Redirect dengan mempertahankan filter kategori
            return redirect()->route('superadmin.master-tahapan', ['kategori' => $request->return_kategori ?? $request->kategori])
                ->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            DB::transaction(function () use ($id) {
                $tahapan = MasterTahapan::findOrFail($id);
                $kategori = $tahapan->kategori;
                $urutan = $tahapan->urutan;

                // Hapus data
                $tahapan->delete();

                // Update urutan untuk data setelahnya dalam kategori yang sama
                MasterTahapan::where('kategori', $kategori)
                    ->where('urutan', '>', $urutan)
                    ->get()
                    ->each(function ($item) {
                        $item->update(['urutan' => $item->urutan - 1]);
                    });
            });

            // Redirect dengan mempertahankan filter kategori
            return redirect()->route('superadmin.master-tahapan', ['kategori' => $request->kategori])
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Fungsi untuk mengatur ulang semua urutan
    private function reorderAll()
    {
        try {
            DB::transaction(function () {
                // Ambil semua kategori
                $kategori = MasterTahapan::select('kategori')
                    ->distinct()
                    ->get();

                // Untuk setiap kategori
                foreach ($kategori as $kat) {
                    $urutan = 1;

                    // Ambil semua data dalam kategori ini
                    $tahapan = MasterTahapan::where('kategori', $kat->kategori)
                        ->orderBy('urutan', 'asc')
                        ->get();

                    // Update urutan
                    foreach ($tahapan as $item) {
                        $item->update(['urutan' => $urutan]);
                        $urutan++;
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error('Error in reorderAll: ' . $e->getMessage());
        }
    }

    public function moveUp($id, Request $request)
    {
        try {
            DB::transaction(function () use ($id) {
                $tahapan = MasterTahapan::findOrFail($id);

                // Jika urutan sudah 1, tidak bisa naik lagi
                if ($tahapan->urutan <= 1) {
                    return;
                }

                // Cari item di atas
                $itemAtas = MasterTahapan::where('kategori', $tahapan->kategori)
                    ->where('urutan', $tahapan->urutan - 1)
                    ->first();

                if ($itemAtas) {
                    // Tukar posisi
                    $itemAtas->update(['urutan' => $tahapan->urutan]);
                    $tahapan->update(['urutan' => $tahapan->urutan - 1]);
                }
            });

            // Redirect dengan parameter yang sama
            return redirect()->route('superadmin.master-tahapan', ['kategori' => $request->kategori])
                ->with('success', 'Urutan berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function moveDown($id, Request $request)
    {
        try {
            DB::transaction(function () use ($id) {
                $tahapan = MasterTahapan::findOrFail($id);

                // Cari urutan maksimal dalam kategori yang sama
                $maxUrutan = MasterTahapan::where('kategori', $tahapan->kategori)
                    ->max('urutan');

                // Jika sudah di urutan terakhir, tidak bisa turun lagi
                if ($tahapan->urutan >= $maxUrutan) {
                    return;
                }

                // Cari item di bawah
                $itemBawah = MasterTahapan::where('kategori', $tahapan->kategori)
                    ->where('urutan', $tahapan->urutan + 1)
                    ->first();

                if ($itemBawah) {
                    // Tukar posisi
                    $itemBawah->update(['urutan' => $tahapan->urutan]);
                    $tahapan->update(['urutan' => $tahapan->urutan + 1]);
                }
            });

            // Redirect dengan parameter yang sama
            return redirect()->route('superadmin.master-tahapan', ['kategori' => $request->kategori])
                ->with('success', 'Urutan berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
