<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        Pengaturan::set('metode_golongan', 'persentil', 'Metode pembagian golongan UKT');
        Pengaturan::set('periode_aktif', '2025/2026', 'Periode akademik yang sedang aktif');
        Pengaturan::set('ambang_batas', json_encode([
            1 => ['min' => 0.00, 'max' => 0.20],
            2 => ['min' => 0.21, 'max' => 0.40],
            3 => ['min' => 0.41, 'max' => 0.60],
            4 => ['min' => 0.61, 'max' => 0.80],
            5 => ['min' => 0.81, 'max' => 1.00],
        ]), 'Ambang batas skor per golongan UKT');
        Pengaturan::set('nominal_ukt', json_encode([
            1 => ['label' => 'UKT 1', 'nominal' => 500000],
            2 => ['label' => 'UKT 2', 'nominal' => 1000000],
            3 => ['label' => 'UKT 3', 'nominal' => 2000000],
            4 => ['label' => 'UKT 4', 'nominal' => 3500000],
            5 => ['label' => 'UKT 5', 'nominal' => 5000000],
        ]), 'Nominal rupiah per golongan UKT');
    }
}