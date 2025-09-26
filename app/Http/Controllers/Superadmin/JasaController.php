<?php

namespace App\Http\Controllers\Superadmin;

use App\Exports\JasaWithDetailsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jasa;
use App\Models\MasterDisiplin;
use App\Models\MasterSubDisiplin;
use App\Models\EPS;
use App\Models\MasterTahapan;
use App\Models\DetailProgress;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\JasaWithDetailsImport;

class JasaController extends Controller
{

    public function index(Request $request)
    {
        $query = Jasa::with('disiplin', 'subDisiplin');

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

        // Filter berdasarkan Sub Disiplin
        if ($request->filled('id_sub_disiplin')) {
            $query->where('id_sub_disiplin', $request->id_sub_disiplin);
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_jasa', 'like', "%{$search}%")
                    ->orWhere('judul_kontrak', 'like', "%{$search}%")
                    ->orWhere('planner', 'like', "%{$search}%")
                    ->orWhere('wo', 'like', "%{$search}%")
                    ->orWhere('pr', 'like', "%{$search}%")
                    ->orWhere('po', 'like', "%{$search}%")
                    ->orWhere('pemenang', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Ubah ordering menjadi ascending berdasarkan kode_jasa dan created_at
        $query->orderBy('kode_jasa', 'asc')
            ->orderBy('created_at', 'asc');

        // Log untuk debugging
        Log::info('Jasa filter parameters:', [
            'id_eps' => $request->id_eps,
            'id_disiplin' => $request->id_disiplin,
            'search' => $request->search
        ]);

        $jasas = $query->get();
        $disiplins = MasterDisiplin::all();
        $sub_disiplins = MasterSubDisiplin::all();
        $eps_list = EPS::all();

        return view('superadmin.jasa.index', compact('jasas', 'disiplins', 'sub_disiplins', 'eps_list'));
    }

    public function create()
    {
        $eps = EPS::all();
        $disiplins = MasterDisiplin::all();
        $eps_list = EPS::all();
        $sub_disiplins = MasterSubDisiplin::all();

        // Ambil EPS default
        $defaultEps = EPS::where('default', 1)->first();

        return view('superadmin.jasa.create', compact('disiplins', 'eps_list', 'sub_disiplins', 'defaultEps'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_eps' => 'required',
            'kode_jasa' => 'required',
            'judul_kontrak' => 'required',
            'id_disiplin' => 'required',
            'planner' => 'nullable',
            'wo' => 'nullable',
            'pr' => 'nullable',
            'po' => 'nullable',
            'pemenang' => 'nullable',
            'keterangan' => 'nullable',
        ]);

        $jasa = Jasa::create($request->all());
        return redirect()->route('superadmin.jasa')->with('success', 'Jasa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $jasa = Jasa::find($id);
        $eps_list = EPS::all();
        $disiplins = MasterDisiplin::all();
        $sub_disiplins = MasterSubDisiplin::all();

        // Ambil EPS default untuk fallback
        $defaultEps = EPS::where('default', 1)->first();

        return view('superadmin.jasa.edit', compact('jasa', 'eps_list', 'disiplins', 'sub_disiplins', 'defaultEps'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_eps' => 'required',
            'kode_jasa' => 'required',
            'judul_kontrak' => 'required',
            'id_disiplin' => 'required',
            'planner' => 'nullable',
            'wo' => 'nullable',
            'pr' => 'nullable',
            'po' => 'nullable',
            'pemenang' => 'nullable',
            'keterangan' => 'nullable',
        ]);

        $jasa = Jasa::find($id);
        $jasa->update($request->all());
        return redirect()->route('superadmin.jasa')->with('success', 'Jasa berhasil diubah');
    }

    public function show($id)
    {
        $jasa = Jasa::find($id);
        $eps_list = EPS::all();
        $disiplin = MasterDisiplin::find($jasa->id_disiplin);
        $sub_disiplin = MasterSubDisiplin::find($jasa->id_sub_disiplin);
        $eps = EPS::find($jasa->id_eps);

        // Filter tahapans by the 'jasa' category instead of showing all tahapans
        $tahapans = MasterTahapan::where('kategori', 'jasa')
            ->orderBy('urutan')
            ->get();

        $details = DetailProgress::where('id_tamu', $id)->get()->keyBy('id_kategori');

        return view('superadmin.jasa.show', compact('jasa', 'eps_list', 'disiplin', 'sub_disiplin', 'details', 'tahapans'));
    }

    public function import()
    {
        $eps_list = EPS::all();

        // Ambil EPS default
        $defaultEps = EPS::where('default', 1)->first();

        return view('superadmin.jasa.import', compact('eps_list', 'defaultEps'));
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'id_eps' => 'required|exists:eps,id_eps'
        ], [
            'file.required' => 'File Excel harus diupload',
            'file.mimes' => 'File harus berformat Excel (.xlsx, .xls) atau CSV',
            'id_eps.required' => 'EPS harus dipilih',
            'id_eps.exists' => 'EPS tidak valid'
        ]);

        try {
            $import = new JasaWithDetailsImport($request->id_eps);

            Excel::import($import, $request->file('file'));

            // Debug: Log info import
            Log::info('Import jasa dengan detail - File uploaded:', [
                'filename' => $request->file('file')->getClientOriginalName(),
                'id_eps' => $request->id_eps
            ]);

            // Ambil data dari kedua sheet
            $jasaImport = $import->sheets()[0];        // Sheet pertama
            $detailImport = $import->sheets()[1];      // Sheet kedua

            // Debug: Log info dari kedua sheet
            Log::info('Import jasa dengan detail - Sheet info:', [
                'jasa_sheet_errors' => $jasaImport->getCustomErrors(),
                'jasa_sheet_success' => $jasaImport->getSuccessCount(),
                'detail_sheet_errors' => $detailImport->getCustomErrors(),
                'detail_sheet_success' => $detailImport->getSuccessCount()
            ]);

            // Gabungkan custom errors dari kedua sheet
            $customErrors = array_merge(
                $jasaImport->getCustomErrors(),
                $detailImport->getCustomErrors()
            );

            // Gabungkan statistik dari kedua sheet
            $successCount = $jasaImport->getSuccessCount() + $detailImport->getSuccessCount();
            $errorCount = $jasaImport->getErrorCount() + $detailImport->getErrorCount();
            $createdCount = $jasaImport->getCreatedCount() + $detailImport->getCreatedCount();
            $updatedCount = $jasaImport->getUpdatedCount() + $detailImport->getUpdatedCount();

            $message = "Import berhasil! ";
            if ($createdCount > 0) {
                $message .= "{$createdCount} data baru ditambahkan";
            }
            if ($updatedCount > 0) {
                if ($createdCount > 0)
                    $message .= ", ";
                $message .= "{$updatedCount} data diupdate";
            }
            $message .= ".";

            if ($errorCount > 0) {
                $message .= " {$errorCount} data gagal diimport.";
            }

            Log::info('Import jasa berhasil', [
                'file' => $request->file('file')->getClientOriginalName(),
                'id_eps' => $request->id_eps,
                'success_count' => $successCount,
                'created_count' => $createdCount,
                'updated_count' => $updatedCount,
                'error_count' => $errorCount,
                'custom_errors' => $customErrors
            ]);

            return redirect()
                ->route('superadmin.jasa')
                ->with('success', $message)
                ->with('import_errors', $customErrors);

        } catch (\Exception $e) {
            Log::error('Gagal import jasa', [
                'file' => $request->file('file')->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('superadmin.jasa.import')
                ->with('error', 'Gagal import data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $jasa = Jasa::find($id);
        $jasa->delete();
        return redirect()->route('superadmin.jasa')->with('success', 'Jasa berhasil dihapus');
    }

    public function downloadTemplate()
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            // Sheet 1: Summary (Jasa)
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Jasa');

            // Set header kolom sesuai format export (tanpa EPS dan Sub Disiplin)
            $headers = [
                'Kode Jasa',
                'Judul Kontrak',
                'Disiplin',
                'Planner',
                'WO',
                'PR',
                'PO',
                'Pemenang',
                'Keterangan'
            ];

            foreach ($headers as $col => $header) {
                $sheet->setCellValueByColumnAndRow($col + 1, 1, $header);
            }

            // Data contoh yang lebih realistis (tanpa EPS dan Sub Disiplin)
            $sampleData = [
                [
                    'JSA-TNK-001',
                    'Pemeliharaan Tanki Storage T-101',
                    'Tanki',
                    'John Doe',
                    'WO-2024-001',
                    'PR-2024-001',
                    'PO-2024-001',
                    'PT. Jaya Konstruksi',
                    'Pemeliharaan rutin tanki storage'
                ],
                [
                    'JSA-BLR-001',
                    'Overhaul Boiler Unit 1',
                    'Boiler',
                    'Jane Smith',
                    'WO-2024-002',
                    'PR-2024-002',
                    'PO-2024-002',
                    'PT. Teknik Maju',
                    'Overhaul boiler tahunan'
                ],
                [
                    'JSA-PIP-001',
                    'Penggantian Pipa Utama',
                    'Pipa',
                    'Bob Wilson',
                    'WO-2024-003',
                    'PR-2024-003',
                    'PO-2024-003',
                    'PT. Pipa Sejahtera',
                    'Penggantian pipa yang sudah aus'
                ]
            ];

            foreach ($sampleData as $row => $data) {
                foreach ($data as $col => $value) {
                    $sheet->setCellValueByColumnAndRow($col + 1, $row + 2, $value);
                }
            }

            // Style header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
            ];

            $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

            // Auto size kolom
            foreach (range('A', 'I') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Border style
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $sheet->getStyle('A1:I4')->applyFromArray($borderStyle);

            // Sheet 2: Detail Progress
            $detailSheet = $spreadsheet->createSheet();
            $detailSheet->setTitle('Detail Progress');

            // Set header kolom untuk detail progress
            $detailHeaders = [
                'Kode Jasa',
                'Judul Kontrak / Step',
                'Plan Start',
                'Plan Finish',
                'Actual Start',
                'Actual Finish',
                'Plan Progress',
                'Actual Progress'
            ];

            foreach ($detailHeaders as $col => $header) {
                $detailSheet->setCellValueByColumnAndRow($col + 1, 1, $header);
            }

            // Data contoh untuk detail progress
            $detailSampleData = [
                [
                    'JSA-TNK-001',
                    'Survey Lokasi',
                    '01/01/2024',
                    '05/01/2024',
                    '02/01/2024',
                    '06/01/2024',
                    '80',
                    '75'
                ],
                [
                    'JSA-TNK-001',
                    'Persiapan Material',
                    '06/01/2024',
                    '10/01/2024',
                    '07/01/2024',
                    '11/01/2024',
                    '60',
                    '65'
                ],
                [
                    'JSA-BLR-001',
                    'Pemeriksaan Awal',
                    '01/01/2024',
                    '03/01/2024',
                    '01/01/2024',
                    '04/01/2024',
                    '90',
                    '85'
                ],
                [
                    'JSA-BLR-001',
                    'Penggantian Spare Part',
                    '05/01/2024',
                    '08/01/2024',
                    '06/01/2024',
                    '09/01/2024',
                    '70',
                    '75'
                ]
            ];

            foreach ($detailSampleData as $row => $data) {
                foreach ($data as $col => $value) {
                    $detailSheet->setCellValueByColumnAndRow($col + 1, $row + 2, $value);
                }
            }

            // Style untuk sheet detail
            $detailSheet->getStyle('A1:H1')->applyFromArray($headerStyle);
            foreach (range('A', 'H') as $column) {
                $detailSheet->getColumnDimension($column)->setAutoSize(true);
            }
            $detailSheet->getStyle('A1:H4')->applyFromArray($borderStyle);

            // Download file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'template_import_jasa_dengan_detail.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            Log::error('Gagal download template jasa', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('superadmin.jasa.import')
                ->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = Jasa::with('disiplin', 'subDisiplin', 'eps');

        if ($request->has('id_eps')) {
            if ($request->filled('id_eps')) {
                $query->where('id_eps', $request->id_eps);
            }
        } else {
            $defaultEps = EPS::where('default', 1)->first();
            if ($defaultEps) {
                $query->where('id_eps', $defaultEps->id_eps);
            }
        }

        if ($request->filled('id_disiplin')) {
            $query->where('id_disiplin', $request->id_disiplin);
        }

        if ($request->filled('id_sub_disiplin')) {
            $query->where('id_sub_disiplin', $request->id_sub_disiplin);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_jasa', 'like', "%{$search}%")
                    ->orWhere('judul_kontrak', 'like', "%{$search}%")
                    ->orWhere('planner', 'like', "%{$search}%")
                    ->orWhere('wo', 'like', "%{$search}%")
                    ->orWhere('pr', 'like', "%{$search}%")
                    ->orWhere('po', 'like', "%{$search}%")
                    ->orWhere('pemenang', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Gunakan ordering yang sama dengan index
        $jasas = $query->orderBy('kode_jasa', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $detailQuery = DetailProgress::with('masterTahapan')
            ->whereIn('id_tamu', $jasas->pluck('id_jasa'))
            ->orderBy('id_tamu')
            ->orderBy('id_kategori');
        $details = $detailQuery->get();

        $filename = 'jasa_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new JasaWithDetailsExport($jasas, $details), 'laporan_jasa.xlsx');
    }

    public function exportStore(Request $request)
    {
        $jasas = Jasa::all();
        return view('superadmin.jasa.export', compact('jasas'));
    }


}
