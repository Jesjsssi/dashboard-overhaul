<?php

namespace App\Imports;

use App\Models\DetailProgress;
use App\Models\MasterTahapan;
use App\Models\Jasa;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DetailProgressImport implements ToArray, WithHeadingRow
{
    protected $idEps;
    protected $customErrors = [];
    protected $successCount = 0;
    protected $errorCount = 0;
    protected $createdCount = 0;
    protected $updatedCount = 0;

    public function __construct($idEps)
    {
        $this->idEps = $idEps;
    }

    public function array(array $rows)
    {
        // Prefetch all existing tahapan for 'jasa' category
        $existingTahapans = MasterTahapan::where('kategori', 'jasa')
            ->orderBy('urutan')
            ->get()
            ->keyBy('step');

        foreach ($rows as $index => $row) {
            try {
                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                // Debug: Log data yang diterima
                Log::info('Import detail progress - Raw data baris ' . ($index + 2), [
                    'row_data' => $row,
                    'kode_jasa_raw' => $row['kode_jasa'] ?? 'NULL',
                    'judul_kontrak_step_raw' => $row['judul_kontrak_step'] ?? 'NULL',
                ]);

                // Validasi data wajib
                $kodeJasa = trim($row['kode_jasa'] ?? '');
                $step = trim($row['judul_kontrak_step'] ?? '');

                if (empty($kodeJasa)) {
                    Log::info('Import detail progress - Skip: Kode Jasa kosong pada baris ' . ($index + 2));
                    continue;
                }

                if (empty($step)) {
                    Log::info('Import detail progress - Skip: Step kosong pada baris ' . ($index + 2));
                    continue;
                }

                // Skip baris header per Jasa (baris kuning yang mengandung "Tanggal Plan", "Tanggal Actual", dll)
                // Kondisi skip dihapus

                // Skip jika step sama dengan judul kontrak (baris header kuning lainnya)
                $jasa = Jasa::where('kode_jasa', $kodeJasa)
                    ->where('id_eps', $this->idEps)
                    ->first();

                if (!$jasa) {
                    // Coba cari jasa di semua EPS jika tidak ditemukan di EPS yang dipilih
                    $jasa = Jasa::where('kode_jasa', $kodeJasa)->first();
                }

                // Skip jika step sama dengan judul kontrak (header kuning)
                if ($jasa && $step === $jasa->judul_kontrak) {
                    Log::info('Import detail progress - Skip judul kontrak baris: ' . ($index + 2) . ' - Step: ' . $step);
                    continue;
                }

                if (!$jasa) {
                    $this->errorCount++;
                    $this->customErrors[] = "Baris " . ($index + 2) . ": Jasa dengan kode '{$kodeJasa}' tidak ditemukan di sistem";
                    continue;
                }

                // Cari tahapan HANYA dari data yang sudah di-prefetch (tidak membuat yang baru)
                $masterTahapan = $existingTahapans->get($step);

                // Jika tidak ditemukan di cache, cari di database tapi tidak membuat baru
                if (!$masterTahapan) {
                    $masterTahapan = MasterTahapan::where('step', $step)
                        ->where('kategori', 'jasa')
                        ->first();
                }

                // Jika master tahapan tidak ditemukan, skip baris ini (tidak membuat yang baru)
                if (!$masterTahapan) {
                    Log::info('Import detail progress - Skip: Master tahapan tidak ditemukan untuk step: ' . $step . ' pada baris ' . ($index + 2));
                    continue;
                }

                // Cek apakah detail progress sudah ada
                $existingDetail = DetailProgress::where('id_tamu', $jasa->id_jasa)
                    ->where('id_kategori', $masterTahapan->id)
                    ->first();

                // Data untuk update/create
                $detailData = [
                    'id_tamu' => $jasa->id_jasa,
                    'id_kategori' => $masterTahapan->id,
                    'plan_start' => $this->parseDate($row['plan_start'] ?? ''),
                    'plan_finish' => $this->parseDate($row['plan_finish'] ?? ''),
                    'actual_start' => $this->parseDate($row['actual_start'] ?? ''),
                    'actual_finish' => $this->parseDate($row['actual_finish'] ?? ''),
                    'plan_progress' => $this->parseProgress($row['plan_progress'] ?? ''),
                    'actual_progress' => $this->parseProgress($row['actual_progress'] ?? ''),
                ];

                if ($existingDetail) {
                    // Update data yang sudah ada
                    $existingDetail->update($detailData);
                    $this->successCount++;
                    $this->updatedCount++;

                    Log::info('Import detail progress - Data updated:', [
                        'row' => $index + 2,
                        'kode_jasa' => $kodeJasa,
                        'step' => $step,
                        'action' => 'updated'
                    ]);
                } else {
                    // Buat data baru
                    DetailProgress::create($detailData);
                    $this->successCount++;
                    $this->createdCount++;

                    Log::info('Import detail progress - Data created:', [
                        'row' => $index + 2,
                        'kode_jasa' => $kodeJasa,
                        'step' => $step,
                        'action' => 'created'
                    ]);
                }
            } catch (\Exception $e) {
                $this->errorCount++;
                $this->customErrors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                Log::error('Import detail progress - Error pada baris ' . ($index + 2), [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'row_data' => $row
                ]);
            }
        }
    }

    /**
     * Parse tanggal dari string Excel
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Coba parse berbagai format tanggal
            if (is_numeric($dateString)) {
                // Excel date number
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateString);
                return $date->format('Y-m-d'); // Simpan dalam format database Y-m-d
            } else {
                // String date - coba berbagai format
                $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d', 'd/m/y', 'd-m-y'];

                foreach ($formats as $format) {
                    try {
                        $date = \Carbon\Carbon::createFromFormat($format, $dateString);
                        return $date->format('Y-m-d'); // Simpan dalam format database Y-m-d
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                // Jika semua format gagal, coba parse dengan Carbon default
                $date = \Carbon\Carbon::parse($dateString);
                return $date->format('Y-m-d'); // Simpan dalam format database Y-m-d
            }
        } catch (\Exception $e) {
            Log::warning('Import detail progress - Gagal parse tanggal: ' . $dateString . ' - Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse progress dari string Excel
     */
    private function parseProgress($progressString)
    {
        if (empty($progressString)) {
            return null;
        }

        // Hapus karakter % dan spasi
        $progress = trim(str_replace('%', '', $progressString));

        if (is_numeric($progress)) {
            return (float) $progress;
        }

        return null;
    }

    public function getCustomErrors()
    {
        return $this->customErrors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getErrorCount()
    {
        return $this->errorCount;
    }

    public function getCreatedCount()
    {
        return $this->createdCount;
    }

    public function getUpdatedCount()
    {
        return $this->updatedCount;
    }
}
