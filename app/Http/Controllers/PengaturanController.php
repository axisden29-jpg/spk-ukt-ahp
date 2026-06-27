<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function golongan()
    {
        $metode       = Pengaturan::get('metode_golongan', 'persentil');
        $periodeAktif = Pengaturan::get('periode_aktif', '2024/2025');
        $ambangBatas  = json_decode(Pengaturan::get('ambang_batas', '{}'), true);

        return view('pengaturan.golongan', compact('metode', 'periodeAktif', 'ambangBatas'));
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'metode_golongan' => 'required|in:persentil,ambang_batas',
            'periode_aktif'   => 'required|string|max:20',
        ]);

        Pengaturan::set('metode_golongan', $request->metode_golongan, 'Metode pembagian golongan UKT');
        Pengaturan::set('periode_aktif', $request->periode_aktif, 'Periode akademik aktif');

        if ($request->metode_golongan === 'ambang_batas') {
            $ambang = [];
            for ($g = 1; $g <= 5; $g++) {
                $ambang[$g] = [
                    'min' => (float) $request->input("ambang.{$g}.min", 0),
                    'max' => (float) $request->input("ambang.{$g}.max", 0),
                ];
            }
            Pengaturan::set('ambang_batas', json_encode($ambang), 'Ambang batas skor per golongan');
        }

        // Simpan nominal UKT
        $nominalData = [];
        for ($g = 1; $g <= 5; $g++) {
            $nominalData[$g] = [
                'label'   => $request->input("nominal_label.{$g}", "UKT {$g}"),
                'nominal' => (int) $request->input("nominal_ukt.{$g}", 0),
            ];
        }
        Pengaturan::set('nominal_ukt', json_encode($nominalData), 'Nominal rupiah per golongan UKT');

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}