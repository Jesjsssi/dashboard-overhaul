<?php

namespace App\Imports;

use App\Models\Project;
use App\Models\MasterDisiplin;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProjectImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithBatchInserts, WithChunkReading, SkipsEmptyRows
{
    private $id_eps;
    private $rowCount = 0;
    private $successCount = 0;
    private $customErrors = [];
    private $disiplinById = [];
    private $disiplinByNameLower = [];

    private function ensureDisiplinCache(): void
    {
        if (!empty($this->disiplinById)) {
            return;
        }
        $all = MasterDisiplin::all(['id_disiplin', 'remark']);
        foreach ($all as $d) {
            $remark = $this->normalizeDisiplinName($d->remark);

            $this->disiplinById[(int) $d->id_disiplin] = (int) $d->id_disiplin;
            $this->disiplinByNameLower[mb_strtolower($remark)] = (int) $d->id_disiplin;
        }

        // Log available disiplin untuk debugging
        Log::info('Available Disiplin:', [
            'disiplin_list' => array_keys($this->disiplinByNameLower),
            'raw_data' => $all->pluck('remark')->toArray()
        ]);
    }

    public function setEpsId($id_eps)
    {
        $this->id_eps = $id_eps;
    }

    public function getRowCount()
    {
        return $this->successCount;
    }

    public function getCustomErrors()
    {
        return $this->customErrors;
    }

    
    public function model(array $row)
    {
        $this->rowCount++;

        try {
            DB::beginTransaction();

            // Sanitasi nilai dasar
            $tagno = isset($row['tagno']) ? trim((string) $row['tagno']) : '';
            $program = isset($row['program']) ? trim((string) $row['program']) : null;
            $kodeRkap = isset($row['kode_rkap']) ? trim((string) $row['kode_rkap']) : '';
            $weightFactor = isset($row['weight_factor']) ? (float) $row['weight_factor'] : 1.0;
            
            // Process kategori field
            $kategori = isset($row['kategori']) ? trim((string) $row['kategori']) : null;
            // Normalize kategori value
            if ($kategori) {
                $kategori = $this->normalizeKategori($kategori);
            }

            // Validasi kode RKAP wajib diisi
            if ($kodeRkap === '') {
                $this->customErrors[] = "Baris {$this->rowCount}: Kode RKAP tidak boleh kosong";
                DB::rollBack();
                return null;
            }

            // Validasi Tag No wajib diisi
            if ($tagno === '') {
                $this->customErrors[] = "Baris {$this->rowCount}: Tag No tidak boleh kosong";
                DB::rollBack();
                return null;
            }

            // Tentukan id_disiplin
            $idDisiplin = $this->getDisiplinId($row);

            // Tentukan id_sub_disiplin (nullable)
            $idSubDisiplin = null;
            if (!empty($row['sub_disiplin'])) {
                $idSubDisiplin = $this->getSubDisiplinId($row);
            }

            // Parse all step dates with improved logging
            $stepDates = [];
            for ($i = 1; $i <= 15; $i++) {
                $fieldName = "step_{$i}_date";
                $rowKey = strtolower("step_{$i}_date");

                if (isset($row[$rowKey])) {
                    $originalValue = $row[$rowKey];
                    $parsedDate = $this->parseDate($originalValue);

                    Log::info("Processing date field", [
                        'field' => $fieldName,
                        'original_value' => $originalValue,
                        'parsed_value' => $parsedDate,
                        'row_number' => $this->rowCount
                    ]);

                    $stepDates[$fieldName] = $parsedDate;
                } else {
                    $stepDates[$fieldName] = null;
                }
            }

            // Siapkan data project
            $projectData = [
                'id_eps' => $this->id_eps,
                'kode_rkap' => $kodeRkap,
                'id_disiplin' => $idDisiplin,
                'id_sub_disiplin' => $idSubDisiplin,
                'tagno' => $tagno,
                'remark' => $program,
                'weight_factor' => $weightFactor,
                'kategori' => $kategori, // Add kategori field
            ];

            // Explicitly ensure id is not included
            if (isset($row['id']) || isset($row['id_project'])) {
                Log::warning('Import data contains explicit id/id_project value. Ignoring it.', [
                    'row_number' => $this->rowCount,
                    'id_value' => isset($row['id']) ? $row['id'] : $row['id_project']
                ]);
            }

            // Add step dates to project data
            foreach ($stepDates as $field => $value) {
                $projectData[$field] = $value;
            }

            // Cek apakah data dengan kode RKAP sudah ada di EPS yang sama
            $existingProject = Project::where('id_eps', $this->id_eps)
                ->where('kode_rkap', $kodeRkap)
                ->first();

            if ($existingProject) {
                // Update data yang sudah ada
                try {
                    $oldData = $existingProject->toArray();
                    Project::where('id', $existingProject->id)  // Changed from id_project to id
                        ->update($projectData);

                    // Ambil data yang sudah diupdate untuk dikembalikan
                    $project = Project::find($existingProject->id);  // Changed from id_project to id

                    // Log detail update untuk debugging
                    Log::info('Project updated successfully', [
                        'row_number' => $this->rowCount,
                        'kode_rkap' => $kodeRkap,
                        'id' => $existingProject->id,  // Changed from id_project to id
                        'changed_fields' => array_diff_assoc($projectData, array_intersect_key($oldData, $projectData))
                    ]);
                } catch (Throwable $e) {
                    Log::error('Failed to update project', [
                        'row_number' => $this->rowCount,
                        'kode_rkap' => $kodeRkap,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            } else {
                // Buat project baru
                try {
                    // Use DB query builder for direct insertion
                    $projectData['created_at'] = now();
                    $projectData['updated_at'] = now();
                    
                    // Double check to ensure id/id_project is not in the data
                    if (isset($projectData['id'])) {
                        Log::warning('Removing id from data before insertion', [
                            'row_number' => $this->rowCount,
                            'id' => $projectData['id']
                        ]);
                        unset($projectData['id']);
                    }
                    
                    if (isset($projectData['id_project'])) {
                        Log::warning('Removing id_project from data before insertion', [
                            'row_number' => $this->rowCount,
                            'id_project' => $projectData['id_project']
                        ]);
                        unset($projectData['id_project']);
                    }
                    
                    $id = DB::table('project')->insertGetId($projectData);
                    $project = Project::find($id);
                    
                    Log::info('New project created', [
                        'row_number' => $this->rowCount,
                        'kode_rkap' => $kodeRkap,
                        'id' => $id  // Changed from id_project to id
                    ]);
                } catch (Throwable $e) {
                    Log::error('Failed to create project', [
                        'row_number' => $this->rowCount,
                        'kode_rkap' => $kodeRkap,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }

            DB::commit();
            $this->successCount++;

            return isset($project) ? $project : null;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error processing row', [
                'row_number' => $this->rowCount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->customErrors[] = "Baris {$this->rowCount}: " . $e->getMessage();
            return null;
        }
    }


    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Handle Excel numeric dates
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
            }

            // Handle d/m/Y format (like 15/01/2025)
            if (is_string($value) && preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            }

            // Handle string dates (Y-m-d format)
            if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value;
            }

            // Handle other date formats
            return Carbon::parse($value)->format('Y-m-d');
        } catch (Throwable $e) {
            Log::warning('Error parsing date', [
                'row_number' => $this->rowCount,
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function getDisiplinId($row)
    {
        $defaultId = 1;

        if (empty($row['disiplin'])) {
            return $defaultId;
        }

        $this->ensureDisiplinCache();
        $input = trim((string) $row['disiplin']);

        // Check numeric ID
        if (is_numeric($input) && isset($this->disiplinById[(int) $input])) {
            return (int) $input;
        }

        // Normalisasi input
        $input = $this->normalizeDisiplinName($input);

        // Check by exact name (case insensitive)
        $key = mb_strtolower($input);
        if (isset($this->disiplinByNameLower[$key])) {
            return $this->disiplinByNameLower[$key];
        }

        // Log warning dan gunakan default
        Log::warning('Disiplin not found, using default', [
            'row_number' => $this->rowCount,
            'input_disiplin' => $input,
            'normalized_input' => $key,
            'available_disiplin' => array_keys($this->disiplinByNameLower)
        ]);
        $this->customErrors[] = "Baris {$this->rowCount}: Disiplin '{$input}' tidak ditemukan, menggunakan default id_disiplin=1";

        return $defaultId;
    }

    private function normalizeDisiplinName($name)
    {
        // Daftar format yang mungkin untuk Electrical & Instrument
        $variations = [
            'elec. & inst.',
            'elec.& inst.',
            'elec &inst',
            'elec & inst',
            'electrical & instrument',
            'electrical &instrument',
            'electrical& instrument',
            'electrical&instrument',
            'electrical and instrument',
            'elec and inst',
            'elec. and inst.',
            'electrical/instrument',
            'elec/inst',
            'elec./inst.',
            'electrical - instrument',
            'elec - inst',
            'elec.- inst.',
            'elect&inst',
            'elect & inst',
            'elect.&inst.',
            'elect & instrument',
            'elect/inst',
            'elect./inst.',
            'elect and inst',
            'elect-inst',
            'electrical & inst',
            'elec & instrument',
            'e&i',
            'e & i',
            'electrical&inst'
        ];

        // Normalisasi input
        $name = str_replace(['&amp;', '&'], '&', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        $name = preg_replace('/[.-]+/', '.', $name);
        $name = trim($name);
        $name = strtolower($name);

        // Log untuk debugging
        Log::info('Normalizing discipline name:', [
            'original' => $name,
            'normalized' => $name,
            'variations' => $variations
        ]);

        // Jika input cocok dengan salah satu variasi, kembalikan format standar
        if (in_array($name, $variations)) {
            return 'Elec. & Inst.';
        }

        // Jika tidak cocok, coba cek dengan pattern matching
        $patterns = [
            '/^elect.*inst.*$/i',
            '/^elec.*inst.*$/i',
            '/^electrical.*instrument.*$/i',
            '/^e\s*&\s*i$/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $name)) {
                return 'Elec. & Inst.';
            }
        }

        // Jika tidak cocok, kembalikan format asli yang sudah dinormalisasi
        return ucwords($name);
    }

    private function getSubDisiplinId($row)
    {
        if (empty($row['sub_disiplin'])) {
            return null;
        }

        $input = trim((string) $row['sub_disiplin']);

        // Cek jika input adalah ID
        if (is_numeric($input)) {
            $subDisiplin = \App\Models\MasterSubDisiplin::find($input);
            if ($subDisiplin) {
                return (int) $input;
            }
        }

        // Cek berdasarkan nama
        $subDisiplin = \App\Models\MasterSubDisiplin::where('remark', 'like', $input)->first();
        if ($subDisiplin) {
            return $subDisiplin->id_sub_disiplin;
        }

        // Log warning jika tidak ditemukan
        Log::warning('Sub Disiplin not found', [
            'row_number' => $this->rowCount,
            'input_sub_disiplin' => $input
        ]);

        return null;
    }

    /**
     * Normalizes kategori input to standard values
     * 
     * @param string $kategori The raw kategori input
     * @return string Normalized kategori value
     */
    private function normalizeKategori($kategori)
    {
        $kategori = strtolower(trim($kategori));
        
        // Map common variations to standard values
        $materialVariations = ['material', 'materials', 'mat', 'barang'];
        $jasaVariations = ['jasa', 'service', 'services', 'servis'];
        $combinedVariations = [
            'material dan jasa', 'material & jasa', 'material and jasa',
            'materials and services', 'material and service',
            'mat & jasa', 'material & service', 'material dan service',
            'barang dan jasa', 'barang & jasa'
        ];
        
        if (in_array($kategori, $materialVariations)) {
            return 'Material';
        } elseif (in_array($kategori, $jasaVariations)) {
            return 'Jasa';
        } elseif (in_array($kategori, $combinedVariations)) {
            return 'Material dan jasa';
        }
        
        // If no match, check for partial matches
        if (strpos($kategori, 'mat') !== false && (strpos($kategori, 'jasa') !== false || strpos($kategori, 'serv') !== false)) {
            return 'Material dan jasa';
        } elseif (strpos($kategori, 'mat') !== false || strpos($kategori, 'barang') !== false) {
            return 'Material';
        } elseif (strpos($kategori, 'jasa') !== false || strpos($kategori, 'serv') !== false) {
            return 'Jasa';
        }
        
        // Log unknown kategori values but return as is
        Log::warning('Unknown kategori value', [
            'row_number' => $this->rowCount,
            'kategori' => $kategori
        ]);
        
        return $kategori;
    }

    public function rules(): array
    {
        return [
            'tagno' => 'required|string',
            'kode_rkap' => 'required|string',
            'program' => 'nullable|string',
            'disiplin' => 'nullable',
            'sub_disiplin' => 'nullable',
            'weight_factor' => 'nullable|numeric',
            'kategori' => 'nullable|string', // Add validation rule for kategori
            // Change validation for date fields to be more lenient
            'step_1_date' => 'nullable',
            'step_2_date' => 'nullable',
            'step_3_date' => 'nullable',
            'step_4_date' => 'nullable',
            'step_5_date' => 'nullable',
            'step_6_date' => 'nullable',
            'step_7_date' => 'nullable',
            'step_8_date' => 'nullable',
            'step_9_date' => 'nullable',
            'step_10_date' => 'nullable',
            'step_11_date' => 'nullable',
            'step_12_date' => 'nullable',
            'step_13_date' => 'nullable',
            'step_14_date' => 'nullable',
            'step_15_date' => 'nullable',
        ];
    }
    public function customValidationMessages()
    {
        return [
            'kode_rkap.required' => 'KODE RKAP harus diisi',
            'kode_rkap.string' => 'KODE RKAP harus berupa teks',
            'tagno.required' => 'Tag No harus diisi',
            'tagno.string' => 'Tag No harus berupa teks',
            'program.string' => 'Program harus berupa teks',
            'weight_factor.numeric' => 'Weight Factor harus berupa angka'
        ];
    }

    public function onError(Throwable $e)
    {
        Log::error('Import error', [
            'row_number' => $this->rowCount,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        $this->customErrors[] = "Error pada baris {$this->rowCount}: " . $e->getMessage();
    }

    public function batchSize(): int
    {
        return PHP_INT_MAX; // Menggunakan nilai maksimum integer PHP
    }

    public function chunkSize(): int
    {
        return PHP_INT_MAX; // Menggunakan nilai maksimum integer PHP
    }
}
