<?php

namespace App\Services;

class AhpService
{
    // Tabel Random Index Saaty
    const RI_TABLE = [
        1 => 0.00, 2 => 0.00, 3 => 0.58, 4 => 0.90,
        5 => 1.12, 6 => 1.24, 7 => 1.32, 8 => 1.41,
        9 => 1.45, 10 => 1.49, 11 => 1.51, 12 => 1.54,
        13 => 1.56, 14 => 1.57, 15 => 1.58,
    ];

    /**
     * Hitung semua langkah AHP dari matriks input
     * @param array $matriks Array 2D [i][j] berisi nilai perbandingan
     * @param array $kriterias Array model Kriteria (urut sesuai index matriks)
     * @return array Hasil lengkap perhitungan AHP
     */
    public function hitung(array $matriks, array $kriterias): array
    {
        $n = count($kriterias);

        // Tahap 1: Lengkapi matriks (diagonal=1, bawah=kebalikan)
        $matriksLengkap = $this->lengkapiMatriks($matriks, $n);

        // Tahap 2: Hitung jumlah kolom
        $jumlahKolom = $this->hitungJumlahKolom($matriksLengkap, $n);

        // Tahap 3: Normalisasi matriks
        $matriksNormal = $this->normalisasi($matriksLengkap, $jumlahKolom, $n);

        // Tahap 4: Priority Vector (bobot)
        $bobot = $this->hitungBobot($matriksNormal, $n);

        // Tahap 5: Uji konsistensi
        $konsistensi = $this->hitungKonsistensi($matriksLengkap, $bobot, $jumlahKolom, $n);

        return [
            'n'               => $n,
            'matriks'         => $matriksLengkap,
            'jumlah_kolom'    => $jumlahKolom,
            'matriks_normal'  => $matriksNormal,
            'bobot'           => $bobot,
            'lambda_max'      => $konsistensi['lambda_max'],
            'ci'              => $konsistensi['ci'],
            'ri'              => $konsistensi['ri'],
            'cr'              => $konsistensi['cr'],
            'konsisten'       => $konsistensi['cr'] <= 0.1,
        ];
    }

    private function lengkapiMatriks(array $matriks, int $n): array
    {
        $m = $matriks;
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i === $j) {
                    $m[$i][$j] = 1.0;
                } elseif (isset($m[$i][$j]) && $m[$i][$j] > 0) {
                    $m[$j][$i] = 1 / $m[$i][$j];
                }
            }
        }
        return $m;
    }

    private function hitungJumlahKolom(array $matriks, int $n): array
    {
        $jumlah = array_fill(0, $n, 0.0);
        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $jumlah[$j] += $matriks[$i][$j];
            }
        }
        return $jumlah;
    }

    private function normalisasi(array $matriks, array $jumlahKolom, int $n): array
    {
        $normal = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $normal[$i][$j] = $jumlahKolom[$j] > 0
                    ? $matriks[$i][$j] / $jumlahKolom[$j]
                    : 0;
            }
        }
        return $normal;
    }

    private function hitungBobot(array $matriksNormal, int $n): array
    {
        $bobot = [];
        for ($i = 0; $i < $n; $i++) {
            $bobot[$i] = array_sum($matriksNormal[$i]) / $n;
        }
        return $bobot;
    }

    private function hitungKonsistensi(array $matriks, array $bobot, array $jumlahKolom, int $n): array
    {
        // Lambda max = sum(jumlah_kolom * bobot)
        $lambdaMax = 0;
        for ($j = 0; $j < $n; $j++) {
            $lambdaMax += $jumlahKolom[$j] * $bobot[$j];
        }

        $ci = $n > 1 ? ($lambdaMax - $n) / ($n - 1) : 0;
        $ri = self::RI_TABLE[$n] ?? 1.58;
        $cr = $ri > 0 ? $ci / $ri : 0;

        return [
    'lambda_max' => $lambdaMax,
    'ci'         => $ci,
    'ri'         => $ri,
    'cr'         => $cr,
];
    }

    /**
     * Hitung skor mahasiswa berdasarkan nilai kriteria dan bobot AHP
     */
    public function hitungSkorMahasiswa(array $nilaiKriteria, array $bobot): float
    {
        $skor = 0.0;
        foreach ($nilaiKriteria as $index => $nilai) {
            $skor += $nilai * ($bobot[$index] ?? 0);
        }
        return $skor;
    }
}