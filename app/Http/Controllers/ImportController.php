<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ProjectImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Models\EPS;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportController extends Controller
{
    public function index()
    {
        $eps_list = EPS::orderBy('tahun', 'desc')->orderBy('jenis_project')->get();

        // Ambil EPS default
        $defaultEps = EPS::where('default', 1)->first();

        return view('superadmin.project.import', compact('eps_list', 'defaultEps'));
    }

    public function import(Request $request)
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
            $import = new ProjectImport();
            $import->setEpsId($request->id_eps);

            Excel::import($import, $request->file('file'));

            // Ambil custom errors jika ada
            $customErrors = $import->getCustomErrors();
            $errorMessage = '';
            if (!empty($customErrors)) {
                $errorMessage = 'Beberapa data gagal diimport: ' . implode(', ', $customErrors);
            }

            Log::info('Import project berhasil', [
                'file' => $request->file('file')->getClientOriginalName(),
                'id_eps' => $request->id_eps,
                'rows_imported' => $import->getRowCount(),
                'custom_errors' => $customErrors
            ]);

            $successMessage = 'Data project berhasil diimport! Total ' . $import->getRowCount() . ' data berhasil ditambahkan.';
            if (!empty($errorMessage)) {
                $successMessage .= ' ' . $errorMessage;
            }

            return redirect()
                ->route('superadmin.project')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Gagal import project', [
                'file' => $request->file('file')->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('superadmin.import-data')
                ->with('error', 'Gagal import data: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Donwload Template Import Project
    public function downloadTemplate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set header sesuai dengan yang ditampilkan di view
            $headers = [
                'KODE RKAP',
                'Tagno',
                'Program',
                'Disiplin',
                'Weight Factor',
                'Step 1',
                'Step 2',
                'Step 3',
                'Step 4',
                'Step 5',
                'Step 6',
                'Step 7',
                'Step 8',
                'Step 9',
                'Step 10',
                'Step 11',
                'Step 12',
                'Step 13',
                'Step 14',
                'Step 15'
            ];

            // Set header row
            foreach ($headers as $col => $header) {
                $sheet->setCellValueByColumnAndRow($col + 1, 1, $header);
            }

            // Set contoh data dengan format tanggal DD/MM/YYYY
            $sampleData = [
                [
                    'RKAP-001',
                    'TAG-001',
                    'Program A',
                    'MECHANICAL',
                    1.0,
                    '01/01/2025',
                    '15/01/2025',
                    '01/02/2025',
                    '15/02/2025',
                    '01/03/2025',
                    '15/03/2025',
                    '01/04/2025',
                    '15/04/2025',
                    '01/05/2025',
                    '15/05/2025',
                    '01/06/2025',
                    '15/06/2025',
                    '01/07/2025',
                    '15/07/2025',
                    '01/08/2025'
                ],
                [
                    'RKAP-002',
                    'TAG-002',
                    'Program B',
                    'INSTRUMENT',
                    0.8,
                    '05/01/2025',
                    '20/01/2025',
                    '05/02/2025',
                    '20/02/2025',
                    '05/03/2025',
                    '20/03/2025',
                    '05/04/2025',
                    '20/04/2025',
                    '05/05/2025',
                    '20/05/2025',
                    '05/06/2025',
                    '20/06/2025',
                    '05/07/2025',
                    '20/07/2025',
                    '05/08/2025'
                ]
            ];

            foreach ($sampleData as $row => $data) {
                foreach ($data as $col => $value) {
                    $sheet->setCellValueByColumnAndRow($col + 1, $row + 2, $value);
                }
            }

            // Format date columns untuk DD/MM/YYYY
            for ($col = 6; $col <= 20; $col++) {
                $dateCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->getStyle($dateCol . '2:' . $dateCol . '3')
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');
            }

            // Get last column letter
            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

            // Set style untuk header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];

            // Apply header style to all header columns
            $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);

            // Auto size all columns
            foreach (range('A', $lastCol) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Set border
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            // Apply border to all data including headers
            $sheet->getStyle('A1:' . $lastCol . '3')->applyFromArray($borderStyle);

            // Create writer
            $writer = new Xlsx($spreadsheet);

            // Set headers untuk download
            $filename = 'template_import_project.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Output file
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            Log::error('Gagal download template', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('superadmin.import-data')
                ->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }


    // Donwload Template Import Jasa
    public function downloadTemplateJasa()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set header kolom
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

            // Data contoh
            $sampleData = [
                [
                    'JSA-001',
                    'Contoh Judul Kontrak 1',
                    'MECHANICAL',
                    'Planner 1',
                    'WO-001',
                    'PR-001',
                    'PO-001',
                    'Vendor A',
                    'Keterangan 1'
                ],
                [
                    'JSA-002',
                    'Contoh Judul Kontrak 2',
                    'INSTRUMENT',
                    'Planner 2',
                    'WO-002',
                    'PR-002',
                    'PO-002',
                    'Vendor B',
                    'Keterangan 2'
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

            $sheet->getStyle('A1:I3')->applyFromArray($borderStyle);

            // Download file
            $writer = new Xlsx($spreadsheet);
            $filename = 'template_import_jasa.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            Log::error('Gagal download template', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('superadmin.jasa.import')
                ->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }
}
