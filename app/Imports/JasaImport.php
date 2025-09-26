<?php

namespace App\Imports;

use App\Models\Jasa;
use App\Models\MasterDisiplin;
use App\Models\EPS;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JasaImport implements ToArray, WithHeadingRow
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
        foreach ($rows as $index => $row) {
            try {
                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                // Debug: Log data yang diterima
                Log::info('Import jasa - Raw data baris ' . ($index + 2), [
                    'row_data' => $row,
                    'kode_jasa_raw' => $row['kode_jasa'] ?? 'NULL',
                    'judul_kontrak_raw' => $row['judul_kontrak'] ?? 'NULL',
                    'disiplin_raw' => $row['disiplin'] ?? 'NULL'
                ]);

                // Validasi data wajib dengan pengecekan yang lebih detail
                $kodeJasa = trim($row['kode_jasa'] ?? '');
                $judulKontrak = trim($row['judul_kontrak'] ?? '');
                $disiplin = trim($row['disiplin'] ?? '');

                // Debug: Log data setelah trim
                Log::info('Import jasa - Data setelah trim baris ' . ($index + 2), [
                    'kode_jasa_trimmed' => $kodeJasa,
                    'judul_kontrak_trimmed' => $judulKontrak,
                    'disiplin_trimmed' => $disiplin,
                    'kode_jasa_length' => strlen($kodeJasa),
                    'judul_kontrak_length' => strlen($judulKontrak),
                    'disiplin_length' => strlen($disiplin)
                ]);

                if (empty($kodeJasa)) {
                    $this->errorCount++;
                    $this->customErrors[] = "Baris " . ($index + 2) . ": Kode Jasa wajib diisi (kosong setelah trim)";
                    continue;
                }

                if (empty($judulKontrak)) {
                    $this->errorCount++;
                    $this->customErrors[] = "Baris " . ($index + 2) . ": Judul Kontrak wajib diisi (kosong setelah trim)";
                    continue;
                }

                if (empty($disiplin)) {
                    $this->errorCount++;
                    $this->customErrors[] = "Baris " . ($index + 2) . ": Disiplin wajib diisi (kosong setelah trim)";
                    continue;
                }

                // Gunakan EPS dari constructor
                $eps = EPS::find($this->idEps);
                if (!$eps) {
                    $this->errorCount++;
                    $this->customErrors[] = "Baris " . ($index + 2) . ": EPS tidak valid";
                    continue;
                }

                // Cari disiplin berdasarkan nama
                $masterDisiplin = MasterDisiplin::where('remark', 'like', '%' . $disiplin . '%')->first();
                if (!$masterDisiplin) {
                    $this->errorCount++;
                    $this->customErrors[] = "Baris " . ($index + 2) . ": Disiplin '{$disiplin}' tidak ditemukan";
                    continue;
                }

                // Sub disiplin diabaikan (tidak digunakan)

                // Cek apakah kode jasa sudah ada
                $existingJasa = Jasa::where('kode_jasa', $kodeJasa)->first();

                // Data untuk update/create
                $jasaData = [
                    'id_eps' => $eps->id_eps,
                    'kode_jasa' => $kodeJasa,
                    'judul_kontrak' => $judulKontrak,
                    'id_disiplin' => $masterDisiplin->id_disiplin,
                    'planner' => trim($row['planner'] ?? ''),
                    'wo' => trim($row['wo'] ?? ''),
                    'pr' => trim($row['pr'] ?? ''),
                    'po' => trim($row['po'] ?? ''),
                    'pemenang' => trim($row['pemenang'] ?? ''),
                    'keterangan' => trim($row['keterangan'] ?? ''),
                ];

                if ($existingJasa) {
                    // Update data yang sudah ada
                    $existingJasa->update($jasaData);
                    $this->successCount++;
                    $this->updatedCount++;

                    // Log untuk debugging
                    Log::info('Import jasa - Data updated:', [
                        'row' => $index + 2,
                        'kode_jasa' => $kodeJasa,
                        'action' => 'updated',
                        'jasa_data' => $jasaData
                    ]);
                } else {
                    // Buat data baru
                    Jasa::create($jasaData);
                    $this->successCount++;
                    $this->createdCount++;

                    // Log untuk debugging
                    Log::info('Import jasa - Data created:', [
                        'row' => $index + 2,
                        'kode_jasa' => $kodeJasa,
                        'action' => 'created',
                        'jasa_data' => $jasaData
                    ]);
                }

            } catch (\Exception $e) {
                $this->errorCount++;
                $this->customErrors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                Log::error('Import jasa - Error pada baris ' . ($index + 2), [
                    'error' => $e->getMessage(),
                    'row_data' => $row
                ]);
            }
        }
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
