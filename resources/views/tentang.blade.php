@extends('layouts.app')

@section('title', 'Tentang Sistem')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <span class="text-on-surface font-medium">Tentang Sistem</span>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-on-surface">Tentang Sistem</h1>
    <p class="text-text-muted text-sm mt-1">Panduan dan dokumentasi metode AHP yang digunakan</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Konten Utama --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Deskripsi Sistem --}}
        <div class="card p-6">
            <h2 class="font-display font-bold text-on-surface text-lg mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">info</span>
                Tentang Sistem
            </h2>
            <p class="text-sm text-text-muted leading-relaxed mb-3">
                Sistem Pendukung Keputusan (SPK) Penentuan Golongan UKT ini dibangun menggunakan
                metode <strong class="text-on-surface">Analytical Hierarchy Process (AHP)</strong>
                untuk membantu perguruan tinggi dalam menetapkan golongan Uang Kuliah Tunggal
                secara objektif, transparan, dan konsisten berdasarkan kondisi ekonomi mahasiswa.
            </p>
            <p class="text-sm text-text-muted leading-relaxed">
                Sistem ini dikembangkan sebagai bagian dari penelitian skripsi dengan tujuan
                mengurangi subjektivitas dalam proses penetapan UKT dan memberikan hasil
                yang dapat dipertanggungjawabkan secara ilmiah.
            </p>
        </div>

        {{-- Apa itu AHP --}}
        <div class="card p-6">
            <h2 class="font-display font-bold text-on-surface text-lg mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">psychology</span>
                Apa itu Metode AHP?
            </h2>
            <p class="text-sm text-text-muted leading-relaxed mb-4">
                <strong class="text-on-surface">Analytical Hierarchy Process (AHP)</strong> adalah
                metode pengambilan keputusan multikriteria yang dikembangkan oleh
                <strong class="text-on-surface">Thomas L. Saaty</strong> pada tahun 1970-an.
                AHP memecah masalah kompleks menjadi hierarki kriteria yang lebih sederhana,
                lalu membandingkan setiap kriteria secara berpasangan untuk mendapatkan bobot prioritas.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="bg-primary-light/50 rounded-xl p-4 text-center">
                    <span class="material-symbols-outlined text-primary text-2xl block mb-2">account_tree</span>
                    <p class="text-xs font-semibold text-on-surface">Dekomposisi</p>
                    <p class="text-xs text-text-muted mt-1">Memecah masalah menjadi hierarki kriteria</p>
                </div>
                <div class="bg-primary-light/50 rounded-xl p-4 text-center">
                    <span class="material-symbols-outlined text-primary text-2xl block mb-2">compare_arrows</span>
                    <p class="text-xs font-semibold text-on-surface">Perbandingan</p>
                    <p class="text-xs text-text-muted mt-1">Membandingkan kriteria secara berpasangan</p>
                </div>
                <div class="bg-primary-light/50 rounded-xl p-4 text-center">
                    <span class="material-symbols-outlined text-primary text-2xl block mb-2">verified</span>
                    <p class="text-xs font-semibold text-on-surface">Konsistensi</p>
                    <p class="text-xs text-text-muted mt-1">Uji CR ≤ 0.1 untuk validasi penilaian</p>
                </div>
            </div>
        </div>

        {{-- Tahapan Perhitungan --}}
        <div class="card p-6">
            <h2 class="font-display font-bold text-on-surface text-lg mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">timeline</span>
                Tahapan Perhitungan AHP
            </h2>
            <div class="space-y-4">
                @foreach([
                    ['1', 'Membuat Matriks Perbandingan Berpasangan', 'Admin mengisi nilai perbandingan antar kriteria menggunakan skala Saaty 1–9. Nilai mencerminkan tingkat kepentingan relatif satu kriteria terhadap kriteria lain.', 'grid_on'],
                    ['2', 'Menghitung Jumlah Kolom', 'Setiap kolom pada matriks dijumlahkan untuk normalisasi pada tahap berikutnya.', 'calculate'],
                    ['3', 'Normalisasi Matriks', 'Setiap elemen dibagi dengan jumlah kolomnya masing-masing sehingga total setiap kolom = 1.', 'equalizer'],
                    ['4', 'Menghitung Priority Vector (Bobot)', 'Bobot setiap kriteria dihitung sebagai rata-rata baris dari matriks ternormalisasi.', 'bar_chart'],
                    ['5', 'Uji Konsistensi (CR)', 'Dihitung λmax, CI, RI, dan CR. Jika CR ≤ 0.1 maka matriks dinyatakan konsisten dan dapat digunakan.', 'verified'],
                    ['6', 'Menghitung Skor Mahasiswa', 'Nilai tiap kriteria mahasiswa dikalikan dengan bobot AHP, lalu dijumlahkan menjadi skor total.', 'person'],
                    ['7', 'Penetapan Golongan UKT', 'Mahasiswa diurutkan berdasarkan skor, lalu dibagi ke dalam golongan UKT menggunakan metode persentil atau ambang batas.', 'leaderboard'],
                ] as [$no, $judul, $deskripsi, $icon])
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white text-base">{{ $icon }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-sm text-on-surface">{{ $no }}. {{ $judul }}</p>
                        <p class="text-xs text-text-muted mt-0.5 leading-relaxed">{{ $deskripsi }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Skala Saaty --}}
        <div class="card p-6">
            <h2 class="font-display font-bold text-on-surface text-lg mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">format_list_numbered</span>
                Skala Perbandingan Saaty
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-primary">
                            <th class="px-4 py-2 text-white text-xs font-semibold text-center border border-primary-dark">Nilai</th>
                            <th class="px-4 py-2 text-white text-xs font-semibold text-left border border-primary-dark">Definisi</th>
                            <th class="px-4 py-2 text-white text-xs font-semibold text-left border border-primary-dark">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach([
                            ['1', 'Sama penting', 'Dua elemen mempunyai pengaruh yang sama'],
                            ['3', 'Sedikit lebih penting', 'Satu elemen sedikit lebih penting dari yang lain'],
                            ['5', 'Lebih penting', 'Satu elemen lebih penting dari yang lain'],
                            ['7', 'Sangat lebih penting', 'Satu elemen sangat lebih penting dari yang lain'],
                            ['9', 'Mutlak lebih penting', 'Satu elemen mutlak lebih penting dari yang lain'],
                            ['2,4,6,8', 'Nilai kompromi', 'Nilai diantara dua penilaian yang berdekatan'],
                            ['1/n', 'Kebalikan', 'Jika i dibanding j = n, maka j dibanding i = 1/n'],
                        ] as [$val, $def, $ket])
                        <tr class="{{ $loop->even ? 'bg-gray-50 dark:bg-slate-800/80' : 'bg-white dark:bg-slate-800' }}">
                            <td class="px-4 py-2 text-center font-bold font-mono text-primary border border-gray-200 dark:border-slate-700">{{ $val }}</td>
                            <td class="px-4 py-2 font-medium text-on-surface border border-gray-200 dark:border-slate-700 text-xs">{{ $def }}</td>
                            <td class="px-4 py-2 text-text-muted border border-gray-200 dark:border-slate-700 text-xs">{{ $ket }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Sidebar Info --}}
    <div class="space-y-4">

        {{-- Info Teknis --}}
        <div class="card p-5">
            <h3 class="font-display font-semibold text-on-surface mb-3 text-sm">Info Teknis Sistem</h3>
            <div class="space-y-3">
                @foreach([
                    ['Framework', 'Laravel 13'],
                    ['Bahasa', 'PHP 8.3'],
                    ['Database', 'MySQL'],
                    ['Metode SPK', 'AHP (Saaty)'],
                    ['Export', 'PDF & Excel'],
                    ['Role', 'Admin & Mahasiswa'],
                ] as [$label, $value])
                <div class="flex items-center justify-between text-xs">
                    <span class="text-text-muted">{{ $label }}</span>
                    <span class="font-semibold text-on-surface bg-gray-100 dark:bg-slate-700 px-2 py-0.5 rounded-lg">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tabel RI --}}
        <div class="card p-5">
            <h3 class="font-display font-semibold text-on-surface mb-3 text-sm">Tabel Random Index (RI)</h3>
            <p class="text-xs text-text-muted mb-3">Nilai RI berdasarkan jumlah kriteria (n):</p>
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-primary">
                        <th class="px-2 py-1.5 text-white border border-primary-dark text-center">n</th>
                        <th class="px-2 py-1.5 text-white border border-primary-dark text-center">RI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([1=>0.00,2=>0.00,3=>0.58,4=>0.90,5=>1.12,6=>1.24,7=>1.32,8=>1.41,9=>1.45,10=>1.49] as $n => $ri)
                    <tr class="{{ $loop->even ? 'bg-gray-50 dark:bg-slate-800/80' : 'bg-white dark:bg-slate-800' }}">
                        <td class="px-2 py-1.5 text-center font-bold border border-gray-200 dark:border-slate-700">{{ $n }}</td>
                        <td class="px-2 py-1.5 text-center font-mono border border-gray-200 dark:border-slate-700">{{ number_format($ri, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Rumus CR --}}
        <div class="card p-5">
            <h3 class="font-display font-semibold text-on-surface mb-3 text-sm">Rumus Konsistensi</h3>
            <div class="space-y-2 text-xs">
                <div class="bg-gray-50 dark:bg-slate-800/50 rounded-lg px-3 py-2">
                    <p class="text-text-muted">Consistency Index (CI)</p>
                    <p class="font-mono font-bold text-primary mt-1">CI = (λmax − n) / (n − 1)</p>
                </div>
                <div class="bg-gray-50 dark:bg-slate-800/50 rounded-lg px-3 py-2">
                    <p class="text-text-muted">Consistency Ratio (CR)</p>
                    <p class="font-mono font-bold text-primary mt-1">CR = CI / RI</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-900/50 rounded-lg px-3 py-2">
                    <p class="font-semibold text-green-700 dark:text-green-400">✓ Konsisten jika CR ≤ 0.1</p>
                    <p class="text-green-600 dark:text-green-500 mt-0.5">Artinya penilaian dapat dipercaya dan digunakan untuk perhitungan.</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection