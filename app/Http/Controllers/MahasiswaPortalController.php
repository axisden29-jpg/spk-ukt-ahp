<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\NilaiMahasiswa;
use App\Models\HasilUkt;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class MahasiswaPortalController extends Controller
{
    private function getMahasiswa()
    {
        return auth()->user()->mahasiswa;
    }

    public function dashboard()
    {
        $mahasiswa = $this->getMahasiswa();
        $periode   = Pengaturan::get('periode_aktif', '2024/2025');
        $kriterias = Kriteria::aktif()->get();

        $nilaiSudahDiisi = NilaiMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('periode', $periode)
            ->count();

        $hasilUkt = HasilUkt::where('mahasiswa_id', $mahasiswa->id)
            ->where('periode', $periode)
            ->first();

        return view('portal.dashboard', compact(
            'mahasiswa', 'periode', 'kriterias',
            'nilaiSudahDiisi', 'hasilUkt'
        ));
    }

    public function isiNilai()
    {
        $mahasiswa = $this->getMahasiswa();
        $periode   = Pengaturan::get('periode_aktif', '2024/2025');
        $kriterias = Kriteria::aktif()->get();

        $nilaiExisting = NilaiMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('periode', $periode)
            ->get()->keyBy('kriteria_id');

        return view('portal.isi-nilai', compact(
            'mahasiswa', 'kriterias', 'nilaiExisting', 'periode'
        ));
    }

    public function simpanNilai(Request $request)
    {
        $mahasiswa = $this->getMahasiswa();
        $periode   = Pengaturan::get('periode_aktif', '2024/2025');
        $kriterias = Kriteria::aktif()->get();

        $rules = [];
        foreach ($kriterias as $k) {
            $rules["nilai.{$k->id}"] = "required|numeric|min:{$k->skala_min}|max:{$k->skala_max}";
        }
        $request->validate($rules);

        foreach ($kriterias as $k) {
            NilaiMahasiswa::updateOrCreate(
                [
                    'mahasiswa_id' => $mahasiswa->id,
                    'kriteria_id'  => $k->id,
                    'periode'      => $periode,
                ],
                ['nilai' => $request->input("nilai.{$k->id}")]
            );
        }

        return redirect()->route('mahasiswa.portal.dashboard')
            ->with('success', 'Data nilai berhasil disimpan. Terima kasih!');
    }

    public function hasil()
    {
        $mahasiswa = $this->getMahasiswa();
        $periode   = Pengaturan::get('periode_aktif', '2024/2025');

        $hasilUkt = HasilUkt::where('mahasiswa_id', $mahasiswa->id)
            ->where('periode', $periode)
            ->first();

        $kriterias = Kriteria::aktif()->get();
        $nilaiArr  = NilaiMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('periode', $periode)
            ->get()->keyBy('kriteria_id');

        return view('portal.hasil', compact(
            'mahasiswa', 'hasilUkt', 'kriterias', 'nilaiArr', 'periode'
        ));
    }
    public function profil()
{
    $mahasiswa = $this->getMahasiswa();
    $user      = auth()->user();
    return view('portal.profil', compact('mahasiswa', 'user'));
}

public function updateProfil(Request $request)
{
    $request->validate([
        'nama' => 'required|string|max:150',
    ]);

    $mahasiswa = $this->getMahasiswa();
    $mahasiswa->update(['nama' => $request->nama]);
    auth()->user()->update(['name' => $request->nama]);

    return back()->with('success', 'Profil berhasil diperbarui.');
}

public function updatePassword(Request $request)
{
    $request->validate([
        'password_lama'          => 'required',
        'password_baru'          => 'required|min:6|confirmed',
        'password_baru_confirmation' => 'required',
    ]);

    if (!\Illuminate\Support\Facades\Hash::check($request->password_lama, auth()->user()->password)) {
        return back()->with('error', 'Password lama tidak sesuai.');
    }

    auth()->user()->update(['password' => bcrypt($request->password_baru)]);
    return back()->with('success', 'Password berhasil diubah.');
}
}