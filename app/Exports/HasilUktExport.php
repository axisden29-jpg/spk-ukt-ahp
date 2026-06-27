<?php

namespace App\Exports;

use App\Models\HasilUkt;
use App\Models\Kriteria;
use App\Models\Pengaturan;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class HasilUktExport implements WithMultipleSheets
{
    public function __construct(private string $periode) {}

    public function sheets(): array
    {
        return [
            new HasilUktSheetMahasiswa($this->periode),
            new HasilUktSheetRekap($this->periode),
        ];
    }
}

// ===== SHEET 1: DATA MAHASISWA =====
class HasilUktSheetMahasiswa implements
    \Maatwebsite\Excel\Concerns\FromArray,
    \Maatwebsite\Excel\Concerns\WithStyles,
    \Maatwebsite\Excel\Concerns\WithColumnWidths,
    \Maatwebsite\Excel\Concerns\WithTitle
{
    public function __construct(private string $periode) {}

    public function title(): string { return 'Data Mahasiswa'; }

    public function array(): array
    {
        $hasil = HasilUkt::with('mahasiswa')
            ->where('periode', $this->periode)
            ->orderBy('peringkat')
            ->get();

        $rows = [];

        // ── KOP ──
        $rows[] = ['LAPORAN HASIL PENENTUAN GOLONGAN UKT'];
        $rows[] = ['Menggunakan Metode AHP (Analytical Hierarchy Process)'];
        $rows[] = ['Universitas Islam Negeri Sjech M. Djamil Djambek Bukittinggi'];
        $rows[] = ['Fakultas Tarbiyah dan Ilmu Keguruan'];
        $rows[] = [];
        $rows[] = ['Periode Akademik', ':', $this->periode, '', 'Total Mahasiswa', ':', $hasil->count() . ' Orang'];
        $rows[] = ['Metode Golongan', ':', ucfirst(str_replace('_', ' ', Pengaturan::get('metode_golongan', 'persentil'))), '', 'Dicetak', ':', now()->format('d F Y')];
        $rows[] = [];

        // ── HEADER TABEL ──
        $rows[] = [
            'No', 'NIM', 'Nama Mahasiswa', 'Program Studi', 'Angkatan',
            'Skor AHP', 'Peringkat', 'Golongan', 'Label UKT', 'Nominal (Rp)'
        ];

        // ── DATA ──
        foreach ($hasil as $h) {
            $nom = Pengaturan::nominalUkt($h->golongan_ukt);
            $rows[] = [
                $h->peringkat,
                $h->mahasiswa->nim,
                $h->mahasiswa->nama,
                $h->mahasiswa->program_studi ?? '-',
                $h->mahasiswa->angkatan ?? '-',
                number_format($h->skor_total, 6),
                $h->peringkat,
                'UKT ' . $h->golongan_ukt,
                $nom['label'],
                $nom['nominal'],
            ];
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,  'B' => 16, 'C' => 32, 'D' => 26,
            'E' => 10, 'F' => 15, 'G' => 10, 'H' => 10,
            'I' => 15, 'J' => 18,
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $hasil = HasilUkt::where('periode', $this->periode)->count();
        $lastDataRow = 9 + $hasil; // baris data mulai dari 9

        // ── Baris 1: Judul utama ──
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '006b3f']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Baris 2: Sub judul ──
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '444444']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Baris 3: Nama Universitas ──
        $sheet->mergeCells('A3:J3');
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '006b3f']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Baris 4: Fakultas ──
        $sheet->mergeCells('A4:J4');
        $sheet->getStyle('A4')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Garis bawah kop (baris 4) ──
        $sheet->getStyle('A4:J4')->getBorders()->getBottom()->applyFromArray([
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            'color'       => ['rgb' => '006b3f'],
        ]);

        // ── Baris 6-7: Info dokumen ──
        $sheet->getStyle('A6:A7')->getFont()->setBold(true)->getColor()->setRGB('3f4849');
        $sheet->getStyle('D6:D7')->getFont()->setBold(true)->getColor()->setRGB('3f4849');
        $sheet->getStyle('E6:E7')->getFont()->setBold(true)->getColor()->setRGB('3f4849');

        // ── Baris 9: Header tabel ──
        $sheet->getStyle('A9:J9')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '006b3f'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders'   => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => '004d2c'],
                ],
            ],
        ]);
        $sheet->getRowDimension(9)->setRowHeight(20);

        // ── Baris data ──
        if ($lastDataRow >= 10) {
            $sheet->getStyle("A10:J{$lastDataRow}")->applyFromArray([
                'borders'   => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color'       => ['rgb' => 'DDDDDD'],
                    ],
                ],
                'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            ]);

            // Warna baris selang-seling
            for ($row = 10; $row <= $lastDataRow; $row++) {
                if ($row % 2 === 0) {
                    $sheet->getStyle("A{$row}:J{$row}")->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F0FDF4');
                }
            }

            // Format angka kolom J (Nominal)
            $sheet->getStyle("J10:J{$lastDataRow}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            // Center beberapa kolom
            $sheet->getStyle("A10:A{$lastDataRow}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E10:H{$lastDataRow}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}

// ===== SHEET 2: REKAP =====
class HasilUktSheetRekap implements
    \Maatwebsite\Excel\Concerns\FromArray,
    \Maatwebsite\Excel\Concerns\WithStyles,
    \Maatwebsite\Excel\Concerns\WithColumnWidths,
    \Maatwebsite\Excel\Concerns\WithTitle
{
    public function __construct(private string $periode) {}

    public function title(): string { return 'Rekap Golongan'; }

    public function array(): array
    {
        $hasil = HasilUkt::where('periode', $this->periode)->get();
        $total = $hasil->count();

        $rows = [];

        // ── KOP ──
        $rows[] = ['REKAP DISTRIBUSI GOLONGAN UKT'];
        $rows[] = ['Metode AHP (Analytical Hierarchy Process)'];
        $rows[] = ['Universitas Islam Negeri Sjech M. Djamil Djambek Bukittinggi'];
        $rows[] = [];
        $rows[] = ['Periode', ':', $this->periode, '', 'Dicetak', ':', now()->format('d F Y')];
        $rows[] = [];

        // ── HEADER ──
        $rows[] = ['Golongan', 'Label', 'Keterangan', 'Nominal (Rp)', 'Jumlah Mahasiswa', 'Persentase (%)'];

        // ── DATA ──
        foreach ([1=>'Sangat Tidak Mampu',2=>'Tidak Mampu',3=>'Menengah ke Bawah',4=>'Menengah',5=>'Mampu'] as $g => $label) {
            $nom   = Pengaturan::nominalUkt($g);
            $count = $hasil->where('golongan_ukt', $g)->count();
            $rows[] = [
                'UKT ' . $g,
                $nom['label'],
                $label,
                $nom['nominal'],
                $count,
                $total > 0 ? round($count / $total * 100, 2) : 0,
            ];
        }

        // ── TOTAL ──
        $rows[] = ['TOTAL', '', '', '', $total, 100];

        return $rows;
    }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 15, 'C' => 22, 'D' => 18, 'E' => 18, 'F' => 15];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        // ── Baris 1: Judul ──
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '006b3f']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Baris 2: Sub judul ──
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '444444']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Baris 3: Universitas ──
        $sheet->mergeCells('A3:F3');
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '006b3f']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Garis bawah kop ──
        $sheet->getStyle('A3:F3')->getBorders()->getBottom()->applyFromArray([
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            'color'       => ['rgb' => '006b3f'],
        ]);

        // ── Baris 5: Info ──
        $sheet->getStyle('A5')->getFont()->setBold(true)->getColor()->setRGB('3f4849');
        $sheet->getStyle('D5')->getFont()->setBold(true)->getColor()->setRGB('3f4849');
        $sheet->getStyle('E5')->getFont()->setBold(true)->getColor()->setRGB('3f4849');

        // ── Baris 7: Header tabel ──
        $sheet->getStyle('A7:F7')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '006b3f'],
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => '004d2c'],
                ],
            ],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(18);

        // ── Baris data (8–12) ──
        $sheet->getStyle('A8:F12')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => 'DDDDDD'],
                ],
            ],
        ]);

        for ($row = 8; $row <= 12; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:F{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F0FDF4');
            }
        }

        // Format nominal
        $sheet->getStyle('D8:D12')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // Center kolom E & F
        $sheet->getStyle('E8:F12')
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // ── Baris Total (13) ──
        $sheet->getStyle('A13:F13')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5EE'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => '006b3f'],
                ],
            ],
        ]);
        $sheet->getStyle('E13:F13')
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        return [];
    }
}