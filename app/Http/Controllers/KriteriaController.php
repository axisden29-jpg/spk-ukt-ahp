<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriterias = Kriteria::orderBy('urutan')->get();
        return view('kriteria.index', compact('kriterias'));
    }

    public function store(Request $request)
{
    $request->validate([
        'nama'      => 'required|string|max:150',
        'deskripsi' => 'nullable|string',
        'skala_min' => 'required|integer|min:1',
        'skala_max' => 'required|integer|gt:skala_min',
        'urutan'    => 'nullable|integer',
    ]);

    // Susun keterangan_skala dari input
    $keteranganSkala = [];
    $min = (int) $request->skala_min;
    $max = (int) $request->skala_max;
    for ($v = $min; $v <= $max; $v++) {
        $keteranganSkala[$v] = $request->input("keterangan_skala.{$v}", '');
    }

    Kriteria::create([
        'nama'             => $request->nama,
        'kode'             => Str::slug($request->nama, '_'),
        'deskripsi'        => $request->deskripsi,
        'skala_min'        => $min,
        'skala_max'        => $max,
        'keterangan_skala' => $keteranganSkala,
        'urutan'           => $request->urutan ?? Kriteria::max('urutan') + 1,
        'is_aktif'         => true,
    ]);

    return back()->with('success', 'Kriteria berhasil ditambahkan. Harap perbarui matriks AHP.');
}

public function update(Request $request, Kriteria $kriteria)
{
    $request->validate([
        'nama'      => 'required|string|max:150',
        'deskripsi' => 'nullable|string',
        'skala_min' => 'required|integer|min:1',
        'skala_max' => 'required|integer|gt:skala_min',
        'urutan'    => 'nullable|integer|min:1',
    ]);

    $urutanLama = (int) $kriteria->urutan;
    $urutanBaru = (int) ($request->urutan ?? $urutanLama);
    $totalKriteria = Kriteria::count();

    // Pastikan urutan baru tidak melebihi total kriteria
    $urutanBaru = max(1, min($urutanBaru, $totalKriteria));

    if ($urutanBaru !== $urutanLama) {
        if ($urutanBaru > $urutanLama) {
            // Pindah ke bawah: geser kriteria di antara ke atas
            Kriteria::where('id', '!=', $kriteria->id)
                ->whereBetween('urutan', [$urutanLama + 1, $urutanBaru])
                ->decrement('urutan');
        } else {
            // Pindah ke atas: geser kriteria di antara ke bawah
            Kriteria::where('id', '!=', $kriteria->id)
                ->whereBetween('urutan', [$urutanBaru, $urutanLama - 1])
                ->increment('urutan');
        }
    }

    $keteranganSkala = [];
    $min = (int) $request->skala_min;
    $max = (int) $request->skala_max;
    for ($v = $min; $v <= $max; $v++) {
        $keteranganSkala[$v] = $request->input("keterangan_skala.{$v}", '');
    }

    $kriteria->update([
        'nama'             => $request->nama,
        'kode'             => \Illuminate\Support\Str::slug($request->nama, '_'),
        'deskripsi'        => $request->deskripsi,
        'skala_min'        => $min,
        'skala_max'        => $max,
        'keterangan_skala' => $keteranganSkala,
        'urutan'           => $urutanBaru,
    ]);

    return back()->with('success', 'Kriteria berhasil diperbarui. Urutan telah disesuaikan.');
}


    public function destroy(Kriteria $kriteria)
    {
        $kriteria->delete();
        return back()->with('success', 'Kriteria berhasil dihapus.');
    }

    public function toggle(Kriteria $kriteria)
    {
        $kriteria->update(['is_aktif' => !$kriteria->is_aktif]);
        $status = $kriteria->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('warning', "Kriteria berhasil {$status}. Harap perbarui matriks AHP.");
    }
}