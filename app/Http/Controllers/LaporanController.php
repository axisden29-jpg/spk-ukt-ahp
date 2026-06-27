<?php

namespace App\Http\Controllers;

use App\Models\HasilUkt;
use App\Models\Kriteria;
use App\Models\MatriksAhp;
use App\Models\Pengaturan;
use App\Services\AhpService;
use App\Exports\HasilUktExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(private AhpService $ahpService) {}

    private function getAhpHasil(string $periode): array
    {
        $kriterias = Kriteria::aktif()->get();
        $n         = $kriterias->count();
        $ids       = $kriterias->pluck('id')->toArray();

        $matriksDb = MatriksAhp::where('periode', $periode)->get()
            ->keyBy(fn($m) => $m->kriteria_baris_id . '_' . $m->kriteria_kolom_id);

        $matriks = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $key = $ids[$i] . '_' . $ids[$j];
                $matriks[$i][$j] = $matriksDb->has($key)
                    ? (float) $matriksDb[$key]->nilai
                    : ($i === $j ? 1.0 : 0.0);
            }
        }

        return $this->ahpService->hitung($matriks, $kriterias->toArray());
    }

    public function index()
    {
        $periode = Pengaturan::get('periode_aktif', '2024/2025');
        
        $distribusi = HasilUkt::where('periode', $periode)
            ->groupBy('golongan_ukt')
            ->selectRaw('golongan_ukt, count(*) as count')
            ->pluck('count', 'golongan_ukt');
            
        $hasil   = HasilUkt::with('mahasiswa')
            ->where('periode', $periode)
            ->orderBy('peringkat')
            ->paginate(20);

        $metodePembagian = Pengaturan::get('metode_golongan', 'persentil');

        return view('laporan.index', compact('hasil', 'distribusi', 'periode', 'metodePembagian'));
    }

    public function export($format)
    {
        $periode = Pengaturan::get('periode_aktif', '2024/2025');

        if ($format === 'pdf') {
            $hasil      = HasilUkt::with('mahasiswa')
                ->where('periode', $periode)
                ->orderBy('peringkat')
                ->get();
            $distribusi      = $hasil->groupBy('golongan_ukt')->map->count();
            $kriterias       = Kriteria::aktif()->get();
            $ahpHasil        = $this->getAhpHasil($periode);
            $metodePembagian = Pengaturan::get('metode_golongan', 'persentil');

            $pdf = Pdf::loadView('laporan.pdf', compact(
                'hasil', 'distribusi', 'kriterias',
                'ahpHasil', 'periode', 'metodePembagian'
            ))->setPaper('a4', 'portrait');

            $namaFile = 'laporan_ukt_' . str_replace('/', '-', $periode);
return $pdf->download("{$namaFile}.pdf");
        }

        if ($format === 'excel') {
            return Excel::download(
                new HasilUktExport($periode),
                "laporan_ukt_" . str_replace('/', '-', $periode) . ".xlsx"
            );
        }

        return back()->with('error', 'Format tidak dikenali.');
    }
}