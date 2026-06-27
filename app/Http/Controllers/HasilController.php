<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Mahasiswa;
use App\Models\MatriksAhp;
use App\Models\NilaiMahasiswa;
use App\Models\HasilUkt;
use App\Models\Pengaturan;
use App\Services\AhpService;
use Illuminate\Http\Request;

class HasilController extends Controller
{
    public function __construct(private AhpService $ahpService) {}

    public function index()
    {
        $periode = Pengaturan::get('periode_aktif', '2024/2025');
        $distribusi = HasilUkt::where('periode', $periode)
            ->groupBy('golongan_ukt')
            ->selectRaw('golongan_ukt, count(*) as count')
            ->pluck('count', 'golongan_ukt');

        $hasil = HasilUkt::with('mahasiswa')
            ->where('periode', $periode)
            ->orderBy('peringkat')
            ->paginate(20);

        return view('hasil.index', compact('hasil', 'periode', 'distribusi'));
    }

    public function proses(Request $request)
    {
        $periode   = Pengaturan::get('periode_aktif', '2024/2025');
        $kriterias = Kriteria::aktif()->get();
        $n         = $kriterias->count();
        $ids       = $kriterias->pluck('id')->toArray();

        // Validasi: matriks AHP ada
        $matriksDb = MatriksAhp::where('periode', $periode)->get()
            ->keyBy(fn($m) => $m->kriteria_baris_id . '_' . $m->kriteria_kolom_id);

        if ($n == 0) {
            return back()->with('error', 'Belum ada kriteria aktif.');
        }

        if ($matriksDb->isEmpty()) {
            return back()->with('error', 'Matriks AHP belum dikonfigurasi.');
        }

        if ($matriksDb->count() < ($n * $n)) {
            return back()->with('error', 'Matriks AHP tidak sinkron dengan jumlah kriteria saat ini. Harap simpan ulang matriks AHP terlebih dahulu.');
        }

        // Susun matriks numerik
        $matriks = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $key = $ids[$i] . '_' . $ids[$j];
                $matriks[$i][$j] = $matriksDb->has($key) ? (float) $matriksDb[$key]->nilai : ($i === $j ? 1.0 : 0.0);
            }
        }

        $ahpHasil = $this->ahpService->hitung($matriks, $kriterias->toArray());

        if (!$ahpHasil['konsisten']) {
            return back()->with('error', 'Matriks AHP tidak konsisten (CR > 0.1). Harap perbaiki matriks terlebih dahulu.');
        }

        // Hitung skor setiap mahasiswa
        $mahasiswas = Mahasiswa::with(['nilaiMahasiswas' => function($q) use ($periode) {
            $q->where('periode', $periode);
        }])->get();
        $skorList   = [];

        foreach ($mahasiswas as $mhs) {
            $nilaiArr = $mhs->nilaiMahasiswas->keyBy('kriteria_id');

            if ($nilaiArr->count() < $n) continue; // skip jika nilai belum lengkap

            $nilaiKriteria = [];
            foreach ($ids as $idx => $kriteriaId) {
                $nilaiKriteria[$idx] = (float) ($nilaiArr[$kriteriaId]->nilai ?? 0);
            }

            $skor = $this->ahpService->hitungSkorMahasiswa($nilaiKriteria, $ahpHasil['bobot']);
            $skorList[] = ['mahasiswa' => $mhs, 'skor' => $skor];
        }

        if (empty($skorList)) {
            return back()->with('error', 'Tidak ada mahasiswa yang memiliki nilai lengkap untuk semua kriteria aktif.');
        }

        // Urutkan skor dari terendah ke tertinggi
        usort($skorList, fn($a, $b) => $a['skor'] <=> $b['skor']);

        // Tentukan golongan
        $metode = Pengaturan::get('metode_golongan', 'persentil');
        $total  = count($skorList);

        // Hapus hasil lama periode ini
        HasilUkt::where('periode', $periode)->delete();

        $insertData = [];
        $now = now();

        foreach ($skorList as $peringkat => $item) {
            if ($metode === 'persentil') {
                $golongan = (int) ceil(($peringkat + 1) / $total * 5);
                $golongan = max(1, min(5, $golongan));
            } else {
                $skor     = $item['skor'];
                $golongan = match (true) {
                    $skor <= 0.20 => 1,
                    $skor <= 0.40 => 2,
                    $skor <= 0.60 => 3,
                    $skor <= 0.80 => 4,
                    default       => 5,
                };
            }

            $insertData[] = [
                'mahasiswa_id'    => $item['mahasiswa']->id,
                'periode'         => $periode,
                'skor_total'      => $item['skor'],
                'peringkat'       => $peringkat + 1,
                'golongan_ukt'    => $golongan,
                'metode_pembagian'=> $metode,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Chunk insert to avoid hitting parameter limits
        foreach (array_chunk($insertData, 500) as $chunk) {
            HasilUkt::insert($chunk);
        }

        return redirect()->route('hasil.index')->with('success', "Perhitungan berhasil! {$total} mahasiswa telah ditetapkan golongan UKT-nya.");
    }

    public function tahapan()
    {
        $periode   = Pengaturan::get('periode_aktif', '2024/2025');
        $kriterias = Kriteria::aktif()->get();
        $n         = $kriterias->count();
        $ids       = $kriterias->pluck('id')->toArray();

        $matriksDb = MatriksAhp::where('periode', $periode)->get()
            ->keyBy(fn($m) => $m->kriteria_baris_id . '_' . $m->kriteria_kolom_id);

        if ($n == 0 || $matriksDb->count() < ($n * $n)) {
            return redirect()->route('ahp.matriks')->with('error', 'Matriks AHP belum lengkap atau kriteria belum diatur.');
        }

        $matriks = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $key = $ids[$i] . '_' . $ids[$j];
                $matriks[$i][$j] = $matriksDb->has($key) ? (float) $matriksDb[$key]->nilai : ($i === $j ? 1.0 : 0.0);
            }
        }

        $ahpHasil = $this->ahpService->hitung($matriks, $kriterias->toArray());
        $hasilUkt = HasilUkt::with('mahasiswa')->where('periode', $periode)->orderBy('peringkat')->paginate(20);

        return view('hasil.tahapan', compact('kriterias', 'ahpHasil', 'hasilUkt', 'periode'));
    }

    public function detailMahasiswa(Mahasiswa $mahasiswa)
    {
        $periode   = Pengaturan::get('periode_aktif', '2024/2025');
        $kriterias = Kriteria::aktif()->get();
        $n         = $kriterias->count();
        $ids       = $kriterias->pluck('id')->toArray();

        $matriksDb = MatriksAhp::where('periode', $periode)->get()
            ->keyBy(fn($m) => $m->kriteria_baris_id . '_' . $m->kriteria_kolom_id);

        if ($n == 0 || $matriksDb->count() < ($n * $n)) {
            return redirect()->route('ahp.matriks')->with('error', 'Matriks AHP belum lengkap atau kriteria belum diatur.');
        }

        $matriks = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $key = $ids[$i] . '_' . $ids[$j];
                $matriks[$i][$j] = $matriksDb->has($key) ? (float) $matriksDb[$key]->nilai : ($i === $j ? 1.0 : 0.0);
            }
        }

        $ahpHasil = $this->ahpService->hitung($matriks, $kriterias->toArray());
        $nilaiArr = NilaiMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('periode', $periode)->get()->keyBy('kriteria_id');
        $hasilUkt = HasilUkt::where('mahasiswa_id', $mahasiswa->id)->where('periode', $periode)->first();

        return view('hasil.detail', compact('mahasiswa', 'kriterias', 'ahpHasil', 'nilaiArr', 'hasilUkt', 'ids', 'periode'));
    }
}