<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\MatriksAhp;
use App\Models\Pengaturan;
use App\Services\AhpService;
use Illuminate\Http\Request;

class AhpController extends Controller
{
    public function __construct(private AhpService $ahpService) {}

    public function matriks()
{
    $kriterias = Kriteria::aktif()->get();
    $periode   = Pengaturan::get('periode_aktif', '2024/2025');

    $matriksDb = MatriksAhp::where('periode', $periode)
        ->get()
        ->keyBy(fn($m) => $m->kriteria_baris_id . '_' . $m->kriteria_kolom_id);

    return view('ahp.matriks', compact('kriterias', 'periode', 'matriksDb'));
}

    public function simpanMatriks(Request $request)
{
    $periode   = Pengaturan::get('periode_aktif', '2024/2025');
    $kriterias = Kriteria::aktif()->get();
    $ids       = $kriterias->pluck('id')->toArray();
    $n         = count($ids);

    MatriksAhp::where('periode', $periode)->delete();

    $insertData = [];
    $now = now();

    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            if ($i === $j) {
                $nilai = 1.0;
            } elseif ($i < $j) {
                // Segitiga atas — nilai langsung dari input user
                $nilai = (float) ($request->input("matriks.{$i}.{$j}") ?? 1);
                if ($nilai <= 0) $nilai = 1;
            } else {
                // Segitiga bawah — kebalikan dari segitiga atas [j][i]
                $atas = (float) ($request->input("matriks.{$j}.{$i}") ?? 1);
                if ($atas <= 0) $atas = 1;
                $nilai = 1 / $atas;
            }

            $insertData[] = [
                'kriteria_baris_id' => $ids[$i],
                'kriteria_kolom_id' => $ids[$j],
                'nilai'             => $nilai,
                'periode'           => $periode,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }
    }

    MatriksAhp::insert($insertData);

    return redirect()->route('ahp.bobot')->with('success', 'Matriks AHP berhasil disimpan.');
}

    public function bobot()
    {
        $kriterias = Kriteria::aktif()->get();
        $periode   = Pengaturan::get('periode_aktif', '2024/2025');
        $n         = $kriterias->count();

        if ($n === 0) {
            return redirect()->route('kriteria.index')->with('error', 'Belum ada kriteria aktif.');
        }

        $ids       = $kriterias->pluck('id')->toArray();
        $matriksDb = MatriksAhp::where('periode', $periode)->get()
            ->keyBy(fn($m) => $m->kriteria_baris_id . '_' . $m->kriteria_kolom_id);

        if ($matriksDb->count() < ($n * $n)) {
            return redirect()->route('ahp.matriks')->with('error', 'Matriks AHP tidak sinkron atau belum lengkap. Harap simpan ulang matriks AHP.');
        }

        // Susun array matriks numerik
        $matriks = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $key = $ids[$i] . '_' . $ids[$j];
                $matriks[$i][$j] = $matriksDb->has($key) ? (float) $matriksDb[$key]->nilai : ($i === $j ? 1.0 : 0.0);
            }
        }

        $hasil = $this->ahpService->hitung($matriks, $kriterias->toArray());

        return view('ahp.bobot', compact('kriterias', 'periode', 'hasil'));
    }
}