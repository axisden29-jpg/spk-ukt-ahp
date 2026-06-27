<?php

use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\AhpController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\HasilController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\MahasiswaPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/dashboard'));

// =====================
// ROUTES ADMIN
// =====================
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Kriteria
    Route::get('/kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');
    Route::post('/kriteria', [KriteriaController::class, 'store'])->name('kriteria.store');
    Route::put('/kriteria/{kriteria}', [KriteriaController::class, 'update'])->name('kriteria.update');
    Route::delete('/kriteria/{kriteria}', [KriteriaController::class, 'destroy'])->name('kriteria.destroy');
    Route::patch('/kriteria/{kriteria}/toggle', [KriteriaController::class, 'toggle'])->name('kriteria.toggle');

    // AHP
    Route::get('/ahp/matriks', [AhpController::class, 'matriks'])->name('ahp.matriks');
    Route::post('/ahp/matriks', [AhpController::class, 'simpanMatriks'])->name('ahp.simpanMatriks');
    Route::get('/ahp/bobot', [AhpController::class, 'bobot'])->name('ahp.bobot');

    // Mahasiswa
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::post('/mahasiswa', [MahasiswaController::class, 'store'])->name('mahasiswa.store');
    Route::get('/mahasiswa/template', [MahasiswaController::class, 'downloadTemplate'])->name('mahasiswa.template');
    Route::post('/mahasiswa/import', [MahasiswaController::class, 'import'])->name('mahasiswa.import');
    Route::put('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'update'])->name('mahasiswa.update');
    Route::delete('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'destroy'])->name('mahasiswa.destroy');
    Route::post('/mahasiswa/{mahasiswa}/reset-password', [MahasiswaController::class, 'resetPassword'])->name('mahasiswa.resetPassword');
    Route::get('/mahasiswa/status-pengisian', [MahasiswaController::class, 'statusPengisian'])->name('mahasiswa.status');
    Route::get('/mahasiswa/{mahasiswa}/nilai', [MahasiswaController::class, 'nilai'])->name('mahasiswa.nilai');
    Route::post('/mahasiswa/{mahasiswa}/nilai', [MahasiswaController::class, 'simpanNilai'])->name('mahasiswa.simpanNilai');

    // Hasil
    Route::post('/perhitungan/proses', [HasilController::class, 'proses'])->name('perhitungan.proses');
    Route::get('/hasil', [HasilController::class, 'index'])->name('hasil.index');
    Route::get('/hasil/tahapan', [HasilController::class, 'tahapan'])->name('hasil.tahapan');
    Route::get('/hasil/mahasiswa/{mahasiswa}', [HasilController::class, 'detailMahasiswa'])->name('hasil.detailMahasiswa');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export/{format}', [LaporanController::class, 'export'])->name('laporan.export');

    // Pengaturan
    Route::get('/pengaturan/golongan', [PengaturanController::class, 'golongan'])->name('pengaturan.golongan');
    Route::post('/pengaturan/golongan', [PengaturanController::class, 'simpan'])->name('pengaturan.simpan');
    Route::get('/tentang', fn() => view('tentang'))->name('tentang');
});

// =====================
// ROUTES MAHASISWA PORTAL
// =====================
Route::middleware(['auth', 'role:mahasiswa'])->prefix('portal')->name('mahasiswa.portal.')->group(function () {
    Route::get('/dashboard', [MahasiswaPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/isi-nilai', [MahasiswaPortalController::class, 'isiNilai'])->name('isiNilai');
    Route::post('/isi-nilai', [MahasiswaPortalController::class, 'simpanNilai'])->name('simpanNilai');
    Route::get('/hasil', [MahasiswaPortalController::class, 'hasil'])->name('hasil');
    Route::get('/profil', [MahasiswaPortalController::class, 'profil'])->name('profil');
Route::post('/profil', [MahasiswaPortalController::class, 'updateProfil'])->name('updateProfil');
Route::post('/profil/password', [MahasiswaPortalController::class, 'updatePassword'])->name('updatePassword');
});

// Redirect setelah login berdasarkan role
Route::middleware('auth')->get('/redirect-after-login', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('dashboard')
        : redirect()->route('mahasiswa.portal.dashboard');
})->name('redirect.after.login');

require __DIR__.'/auth.php';