<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;

class MahasiswaImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private int $imported = 0;
    private int $skipped  = 0;

    public function model(array $row): ?Mahasiswa
    {
        // Skip baris kosong
        if (empty($row['nim']) || empty($row['nama'])) {
            $this->skipped++;
            return null;
        }

        // Skip jika NIM sudah ada
        if (Mahasiswa::where('nim', trim($row['nim']))->exists()) {
            $this->skipped++;
            return null;
        }

        $this->imported++;

        return new Mahasiswa([
            'nim'           => trim($row['nim']),
            'nama'          => trim($row['nama']),
            'program_studi' => trim($row['program_studi'] ?? ''),
            'angkatan'      => !empty($row['angkatan']) ? (int) $row['angkatan'] : null,
        ]);
    }

    public function getImported(): int { return $this->imported; }
    public function getSkipped(): int  { return $this->skipped; }
}