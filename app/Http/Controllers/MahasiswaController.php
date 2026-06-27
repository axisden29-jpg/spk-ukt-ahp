<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Mahasiswa;
use App\Models\NilaiMahasiswa;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $periode    = Pengaturan::get('periode_aktif', '2024/2025');
        $totalKriteria = Kriteria::aktif()->count();
        
        $query = Mahasiswa::withCount([
            'nilaiMahasiswas as nilai_count' => fn($q) => $q->where('periode', $periode)
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('program_studi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter_nilai')) {
            $filter = $request->filter_nilai;
            if ($filter == 'lengkap') {
                if ($totalKriteria > 0) {
                    $query->having('nilai_count', '>=', $totalKriteria);
                }
            } elseif ($filter == 'sebagian') {
                $query->having('nilai_count', '>', 0);
                if ($totalKriteria > 0) {
                    $query->having('nilai_count', '<', $totalKriteria);
                }
            } elseif ($filter == 'belum') {
                $query->having('nilai_count', '=', 0);
            }
        }

        $sort = $request->sort;
        $dir = $request->dir === 'desc' ? 'desc' : 'asc';

        if (!$sort && $request->filled('sort_by')) {
            $parts = explode('-', $request->sort_by);
            if (count($parts) == 2) {
                $sort = $parts[0];
                $dir = $parts[1] === 'desc' ? 'desc' : 'asc';
            }
        }

        if ($sort) {
            if ($sort == 'nama') {
                $query->orderBy('nama', $dir);
            } elseif ($sort == 'nim') {
                $query->orderBy('nim', $dir);
            } elseif ($sort == 'angkatan') {
                $query->orderBy('angkatan', $dir);
            } elseif ($sort == 'nilai') {
                $query->orderBy('nilai_count', $dir);
            } elseif ($sort == 'prodi') {
                $query->orderBy('program_studi', $dir);
            } else {
                $query->orderBy('nama', 'asc');
            }
        } else {
            $query->orderBy('nama', 'asc');
        }

        $mahasiswas = $query->paginate(20)->withQueryString();

        return view('mahasiswa.index', compact('mahasiswas', 'periode', 'totalKriteria'));
    }

    public function store(Request $request)
{
    $request->validate([
        'nim'           => 'required|string|max:20|unique:mahasiswas,nim',
        'nama'          => 'required|string|max:150',
        'program_studi' => 'nullable|string|max:100',
        'angkatan'      => 'nullable|digits:4',
    ]);

    $mahasiswa = Mahasiswa::create(
        $request->only('nim', 'nama', 'program_studi', 'angkatan')
    );

    return redirect()->route('mahasiswa.index')
        ->with('success', "Mahasiswa {$mahasiswa->nama} berhasil ditambahkan. Akun login otomatis dibuat dengan password = NIM.");
}

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'nim'           => 'required|string|max:20|unique:mahasiswas,nim,' . $mahasiswa->id,
            'nama'          => 'required|string|max:150',
            'program_studi' => 'nullable|string|max:100',
            'angkatan'      => 'nullable|digits:4',
        ]);

        $mahasiswa->update($request->only('nim', 'nama', 'program_studi', 'angkatan'));
        return back()->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->delete();
        return back()->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    public function nilai(Mahasiswa $mahasiswa)
    {
        $periode   = Pengaturan::get('periode_aktif', '2024/2025');
        $kriterias = Kriteria::aktif()->get();
        $nilaiExisting = NilaiMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('periode', $periode)
            ->get()->keyBy('kriteria_id');

        return view('mahasiswa.nilai', compact('mahasiswa', 'kriterias', 'nilaiExisting', 'periode'));
    }

    public function simpanNilai(Request $request, Mahasiswa $mahasiswa)
    {
        $periode   = Pengaturan::get('periode_aktif', '2024/2025');
        $kriterias = Kriteria::aktif()->get();

        $rules = [];
        foreach ($kriterias as $k) {
            $rules["nilai.{$k->id}"] = "required|numeric|min:{$k->skala_min}|max:{$k->skala_max}";
        }
        $request->validate($rules);

        foreach ($kriterias as $k) {
            NilaiMahasiswa::updateOrCreate(
                ['mahasiswa_id' => $mahasiswa->id, 'kriteria_id' => $k->id, 'periode' => $periode],
                ['nilai' => $request->input("nilai.{$k->id}")]
            );
        }

        return redirect()->route('mahasiswa.index')->with('success', "Nilai kriteria {$mahasiswa->nama} berhasil disimpan.");
    }
    public function downloadTemplate()
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Data Mahasiswa');

    // Header
    $headers = ['nim', 'nama', 'program_studi', 'angkatan'];
    foreach ($headers as $col => $header) {
        $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
        $sheet->setCellValue($cell, $header);
        $sheet->getStyle($cell)->getFont()->setBold(true);
        $sheet->getStyle($cell)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('006b3f');
        $sheet->getStyle($cell)->getFont()->getColor()->setRGB('FFFFFF');
    }

    // Contoh data
    $contoh = [
        ['2021001001', 'Contoh Mahasiswa Satu', 'Teknik Informatika', 2021],
        ['2021001002', 'Contoh Mahasiswa Dua', 'Sistem Informasi', 2021],
    ];
    foreach ($contoh as $rowIdx => $row) {
        foreach ($row as $colIdx => $val) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1) . ($rowIdx + 2);
            $sheet->setCellValue($cell, $val);
        }
    }

    // Auto width kolom
    foreach (range('A', 'D') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, 'template_mahasiswa.xlsx', [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls|max:2048',
    ]);

    try {
        $import = new \App\Imports\MahasiswaImport();
        \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

        $imported = $import->getImported();
        $skipped  = $import->getSkipped();

        $msg = "{$imported} mahasiswa berhasil diimport.";
        if ($skipped > 0) {
            $msg .= " {$skipped} baris dilewati (NIM duplikat atau data kosong).";
        }

        return back()->with('success', $msg);

    } catch (\Exception $e) {
        return back()->with('error', 'Gagal import: ' . $e->getMessage());
    }
}
public function resetPassword(Mahasiswa $mahasiswa)
{
    $user = \App\Models\User::firstOrCreate(
        ['mahasiswa_id' => $mahasiswa->id],
        [
            'name'         => $mahasiswa->nama,
            'email'        => $mahasiswa->nim,
            'password'     => $mahasiswa->nim, // hashed automatically by User model
            'role'         => 'mahasiswa',
        ]
    );

    $user->update(['password' => $mahasiswa->nim]);
    return back()->with('success', "Password {$mahasiswa->nama} berhasil direset ke NIM.");
}
    public function statusPengisian(Request $request)
    {
        $periode       = Pengaturan::get('periode_aktif', '2024/2025');
        $kriterias     = Kriteria::aktif()->get();
        $totalKriteria = $kriterias->count();

        // Query for aggregate statistics (across all data)
        $totalMahasiswa = Mahasiswa::count();
        $sudahDihitung  = \App\Models\HasilUkt::where('periode', $periode)->count();
        
        // Mahasiswa is complete if they have exactly totalKriteria records in nilaiMahasiswas
        if ($totalKriteria > 0) {
            $sudahLengkap = Mahasiswa::whereHas('nilaiMahasiswas', function($q) use ($periode) {
                $q->where('periode', $periode);
            }, '>=', $totalKriteria)->count();
        } else {
            $sudahLengkap = 0;
        }
        
        $belumLengkap = $totalMahasiswa - $sudahLengkap;

        // Base query with relationships and count
        $query = Mahasiswa::with([
            'nilaiMahasiswas' => fn($q) => $q->where('periode', $periode),
            'hasilUkts'       => fn($q) => $q->where('periode', $periode),
        ])->withCount([
            'nilaiMahasiswas as nilai_count' => fn($q) => $q->where('periode', $periode)
        ])->orderBy('nama');

        // Apply Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        // Apply Filter Status
        $filterAktif = $request->input('filter_status', 'semua');
        if ($filterAktif === 'lengkap') {
            if ($totalKriteria > 0) {
                $query->having('nilai_count', '>=', $totalKriteria);
            }
        } elseif ($filterAktif === 'belum') {
            if ($totalKriteria > 0) {
                $query->having('nilai_count', '<', $totalKriteria);
            }
        }

        // Paginate and retain query string
        $mahasiswasList = $query->paginate(20)->withQueryString();

        // Map over the paginated items
        $mahasiswasList->getCollection()->transform(function ($m) use ($totalKriteria, $periode, $kriterias) {
            $terisi        = $m->nilaiMahasiswas->count();
            $belumTerisi   = $kriterias->filter(fn($k) =>
                !$m->nilaiMahasiswas->pluck('kriteria_id')->contains($k->id)
            )->pluck('nama');
            $hasilUkt      = $m->hasilUkts->first();

            return [
                'mahasiswa'     => $m,
                'terisi'        => $terisi,
                'total'         => $totalKriteria,
                'lengkap'       => $terisi >= $totalKriteria && $totalKriteria > 0,
                'belum_terisi'  => $belumTerisi,
                'persen'        => $totalKriteria > 0 ? round($terisi / $totalKriteria * 100) : 0,
                'hasil_ukt'     => $hasilUkt,
            ];
        });

        // Pass the paginated object to the view
        $mahasiswas = $mahasiswasList;
        $searchQuery = $request->search;

        return view('mahasiswa.status', compact(
            'mahasiswas', 'periode', 'totalKriteria',
            'sudahLengkap', 'belumLengkap', 'sudahDihitung',
            'filterAktif', 'searchQuery'
        ));
    }
}