<?php

namespace App\Imports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class PegawaiImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithCustomCsvSettings
{
    use Importable;

    private $errors = [];
    private $failures = [];
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Log the row data for debugging
        \Log::info('Processing row with keys:', array_keys($row));
        \Log::info('Processing row data:', $row);

        // Check if all values are empty (skip empty rows)
        $allEmpty = true;
        foreach ($row as $value) {
            if (!empty($value)) {
                $allEmpty = false;
                break;
            }
        }

        if ($allEmpty) {
            \Log::info('Skipping empty row');
            return null;
        }

        // Clean NIP - remove quotes and spaces
        $nip = isset($row['nip']) ? trim(str_replace("'", "", $row['nip'])) : '';

        $pegawai = new Pegawai([
            'nama'     => trim($row['nama'] ?? ''),
            'nip'      => $nip,
            'jabatan'  => trim($row['jabatan'] ?? ''),
            'golongan' => trim($row['golongan'] ?? ''),
            'email'    => isset($row['email']) ? trim($row['email']) : null,
        ]);

        \Log::info('Created Pegawai:', $pegawai->toArray());

        return $pegawai;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|max:100',
            'nip' => 'required|max:30|unique:pegawai,nip',
            'jabatan' => 'required|max:100',
            'golongan' => 'required|max:20',
            'email' => 'nullable|email|max:100',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Nama wajib diisi',
            'nip.required' => 'NIP wajib diisi',
            'nip.max' => 'NIP maksimal 30 karakter',
            'nip.unique' => 'NIP sudah terdaftar',
            'jabatan.required' => 'Jabatan wajib diisi',
            'golongan.required' => 'Golongan wajib diisi',
            'email.email' => 'Format email tidak valid',
        ];
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
        \Log::error('Import Error: ' . $e->getMessage());
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = $failure;
            \Log::error('Import Failure on row ' . $failure->row() . ': ' . implode(', ', $failure->errors()));
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFailures()
    {
        return $this->failures;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_character' => '\\',
            'contiguous_delimiters' => false,
            'input_encoding' => 'UTF-8'
        ];
    }
}
