@extends('layouts.app')

@section('title', 'Bobot & Konsistensi AHP')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <span class="text-on-surface font-medium">Bobot & Konsistensi</span>
@endsection

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-display font-bold text-on-surface">Hasil Perhitungan AHP</h1>
        <p class="text-text-muted text-sm mt-1">Periode: {{ $periode }} — Dua matriks lengkap beserta uji konsistensi</p>
    </div>
    <a href="{{ route('ahp.matriks') }}" class="text-sm text-primary font-medium flex items-center gap-1 hover:underline">
        <span class="material-symbols-outlined text-base">edit</span> Edit Matriks
    </a>
</div>

@php $n = $hasil['n']; @endphp

{{-- STATUS KONSISTENSI --}}
<div class="card p-5 mb-6 flex items-center gap-4 border-l-4 {{ $hasil['konsisten'] ? 'border-green-500' : 'border-red-500' }}">
    <span class="material-symbols-outlined text-3xl {{ $hasil['konsisten'] ? 'text-green-500' : 'text-red-500' }}">
        {{ $hasil['konsisten'] ? 'check_circle' : 'error' }}
    </span>
    <div>
        <p class="font-semibold {{ $hasil['konsisten'] ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
            {{ $hasil['konsisten'] ? 'Matriks Konsisten — Perhitungan dapat dilanjutkan' : 'Matriks Tidak Konsisten — Harap perbaiki matriks' }}
        </p>
        <p class="text-sm text-text-muted dark:text-gray-400 mt-0.5">
            CR = <strong>{{ number_format($hasil['cr'], 4) }}</strong>
            &nbsp;|&nbsp; CI = {{ number_format($hasil['ci'], 4) }}
            &nbsp;|&nbsp; RI = {{ number_format($hasil['ri'], 2) }}
            &nbsp;|&nbsp; λmax = {{ number_format($hasil['lambda_max'], 4) }}
        </p>
    </div>
</div>

{{-- MATRIKS 1: PERBANDINGAN BERPASANGAN --}}
<div class="card p-6 mb-6">
    <h2 class="font-display font-semibold text-on-surface mb-1">Matriks 1 — Perbandingan Berpasangan</h2>
    <p class="text-xs text-text-muted mb-4">Nilai input dari admin. Diagonal = 1, segitiga bawah = nilai kebalikan otomatis.</p>
    <div class="overflow-x-auto">
        <table class="text-xs border-collapse w-full">
            <thead>
                <tr>
                    <th class="border border-gray-300 dark:border-slate-700 bg-primary dark:bg-primary-dark text-white px-3 py-2 text-left min-w-[140px]">Kriteria</th>
                    @foreach($kriterias as $k)
                    <th class="border border-gray-300 dark:border-slate-700 bg-primary dark:bg-primary-dark text-white px-3 py-2 text-center min-w-[80px]">
                        {{ Str::limit($k->nama, 12) }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($kriterias as $i => $baris)
                <tr class="{{ $i % 2 === 0 ? 'bg-white dark:bg-slate-800' : 'bg-gray-50/60 dark:bg-slate-800/80' }}">
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 font-semibold text-on-surface dark:text-white">{{ $baris->nama }}</td>
                    @foreach($kriterias as $j => $kolom)
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 text-center font-mono
                        {{ $i === $j ? 'bg-yellow-50 dark:bg-yellow-900/30 font-bold text-yellow-700 dark:text-yellow-400' : '' }}
                        {{ $i < $j ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : '' }}
                        {{ $i > $j ? 'text-gray-500 dark:text-gray-400 italic' : '' }}">
                        @if($hasil['matriks'][$i][$j] >= 1)
                            {{ number_format($hasil['matriks'][$i][$j], 0) == $hasil['matriks'][$i][$j] ? number_format($hasil['matriks'][$i][$j], 0) : number_format($hasil['matriks'][$i][$j], 4) }}
                        @else
                            1/{{ number_format(1 / $hasil['matriks'][$i][$j], 0) }}
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
                {{-- Baris Total --}}
                <tr class="bg-primary-light/40 dark:bg-primary-dark/20 font-bold">
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 text-primary dark:text-primary-light font-bold">Total / Jumlah Kolom</td>
                    @foreach($hasil['jumlah_kolom'] as $jk)
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 text-center font-mono text-primary dark:text-primary-light font-bold">
                        {{ number_format($jk, 4) }}
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-3 flex flex-wrap gap-3 text-xs text-text-muted dark:text-gray-400">
        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-yellow-100 dark:bg-yellow-900/50 border border-yellow-300 dark:border-yellow-700 rounded inline-block"></span> Diagonal (= 1)</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-100 dark:bg-blue-900/50 border border-blue-300 dark:border-blue-700 rounded inline-block"></span> Segitiga atas (nilai input)</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded inline-block"></span> Segitiga bawah (nilai kebalikan)</span>
    </div>
</div>

{{-- MATRIKS 2: TERNORMALISASI + PRIORITAS + EIGEN VALUE --}}
<div class="card p-6 mb-6">
    <h2 class="font-display font-semibold text-on-surface mb-1">Matriks 2 — Matriks Ternormalisasi & Prioritas</h2>
    <p class="text-xs text-text-muted mb-4">Setiap elemen dibagi jumlah kolomnya. Prioritas = rata-rata tiap baris. Eigen Value = Prioritas × λmax per baris.</p>
    <div class="overflow-x-auto">
        <table class="text-xs border-collapse w-full">
            <thead>
                <tr>
                    <th class="border border-gray-300 dark:border-slate-700 bg-primary dark:bg-primary-dark text-white px-3 py-2 text-left min-w-[140px]">Kriteria</th>
                    @foreach($kriterias as $k)
                    <th class="border border-gray-300 dark:border-slate-700 bg-primary dark:bg-primary-dark text-white px-3 py-2 text-center min-w-[80px]">
                        {{ Str::limit($k->nama, 12) }}
                    </th>
                    @endforeach
                    <th class="border border-gray-300 dark:border-slate-700 bg-orange-500 text-white px-3 py-2 text-center font-bold">Jumlah</th>
                    <th class="border border-gray-300 dark:border-slate-700 bg-orange-600 text-white px-3 py-2 text-center font-bold">Prioritas</th>
                    <th class="border border-gray-300 dark:border-slate-700 bg-yellow-500 text-white px-3 py-2 text-center font-bold">Eigen Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kriterias as $i => $baris)
                @php
                    $jumlahBaris = array_sum($hasil['matriks_normal'][$i]);
                    $prioritas   = $hasil['bobot'][$i];
                    // Eigen value = weighted sum method: sum(matriks_asli[i][j] * bobot[j])
                    $eigenValue  = 0;
                    for ($j = 0; $j < $n; $j++) {
                        $eigenValue += $hasil['matriks'][$i][$j] * $hasil['bobot'][$j];
                    }
                @endphp
                <tr class="{{ $i % 2 === 0 ? 'bg-white dark:bg-slate-800' : 'bg-gray-50/60 dark:bg-slate-800/80' }}">
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 font-semibold text-on-surface dark:text-white">{{ $baris->nama }}</td>
                    @foreach($kriterias as $j => $kolom)
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 text-center font-mono">
                        {{ number_format($hasil['matriks_normal'][$i][$j], 4) }}
                    </td>
                    @endforeach
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 text-center font-mono font-bold bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400">
                        {{ number_format($jumlahBaris, 4) }}
                    </td>
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 text-center font-mono font-bold bg-orange-50 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300">
                        {{ number_format($prioritas, 4) }}
                    </td>
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 text-center font-mono font-bold bg-yellow-50 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400">
                        {{ number_format($eigenValue, 4) }}
                    </td>
                </tr>
                @endforeach
                {{-- Baris Total --}}
                @php
                    $totalEigen = 0;
                    for ($i = 0; $i < $n; $i++) {
                        for ($j = 0; $j < $n; $j++) {
                            $totalEigen += $hasil['matriks'][$i][$j] * $hasil['bobot'][$j];
                        }
                    }
                @endphp
                <tr class="font-bold bg-orange-50 dark:bg-orange-900/30">
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 text-orange-700 dark:text-orange-400">Total</td>
                    @for($j = 0; $j < $n; $j++)
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 text-center font-mono text-green-700 dark:text-green-400">1.0000</td>
                    @endfor
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 text-center font-mono text-orange-700 dark:text-orange-400">
                        {{ number_format($n, 4) }}
                    </td>
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 text-center font-mono text-orange-800 dark:text-orange-300">1.0000</td>
                    <td class="border border-gray-300 dark:border-slate-700 px-3 py-2 text-center font-mono text-yellow-800 dark:text-yellow-400">
                        {{ number_format($totalEigen, 4) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- TABEL KONSISTENSI --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Ringkasan CI/RI/CR --}}
    <div class="card p-6">
        <h2 class="font-display font-semibold text-on-surface mb-4">Uji Konsistensi</h2>
        <table class="w-full text-sm border-collapse">
            <tbody>
                <tr class="border-b border-gray-100 dark:border-slate-700">
                    <td class="py-2 text-text-muted dark:text-gray-400">Jumlah Kriteria (n)</td>
                    <td class="py-2 font-mono font-bold text-right dark:text-white">{{ $n }}</td>
                </tr>
                <tr class="border-b border-gray-100 dark:border-slate-700">
                    <td class="py-2 text-text-muted dark:text-gray-400">λmax (Maks Eigen Value)</td>
                    <td class="py-2 font-mono font-bold text-right dark:text-white">{{ number_format($hasil['lambda_max'], 4) }}</td>
                </tr>
                <tr class="border-b border-gray-100 dark:border-slate-700">
                    <td class="py-2 text-text-muted dark:text-gray-400">CI = (λmax − n) / (n − 1)</td>
                    <td class="py-2 font-mono font-bold text-right dark:text-white">{{ number_format($hasil['ci'], 4) }}</td>
                </tr>
                <tr class="border-b border-gray-100 dark:border-slate-700">
                    <td class="py-2 text-text-muted dark:text-gray-400">RI (n = {{ $n }})</td>
                    <td class="py-2 font-mono font-bold text-right dark:text-white">{{ number_format($hasil['ri'], 2) }}</td>
                </tr>
                <tr class="border-b border-gray-100 dark:border-slate-700">
                    <td class="py-2 text-text-muted dark:text-gray-400">CR = CI / RI</td>
                    <td class="py-2 font-mono font-bold text-right {{ $hasil['cr'] <= 0.1 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ number_format($hasil['cr'], 4) }}
                    </td>
                </tr>
                <tr>
                    <td class="py-3 font-semibold dark:text-white">Status</td>
                    <td class="py-3 text-right">
                        @if($hasil['konsisten'])
                            <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-1 rounded-full text-xs font-bold">✓ KONSISTEN (CR ≤ 0.1)</span>
                        @else
                            <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-3 py-1 rounded-full text-xs font-bold">✗ TIDAK KONSISTEN (CR > 0.1)</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Tabel RI Saaty --}}
    <div class="card p-6">
        <h2 class="font-display font-semibold text-on-surface mb-4">Tabel Random Index (RI) Saaty</h2>
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-slate-800">
                    <th class="border border-gray-200 dark:border-slate-700 px-3 py-2 text-center text-xs font-semibold text-text-muted dark:text-gray-400">n</th>
                    <th class="border border-gray-200 dark:border-slate-700 px-3 py-2 text-center text-xs font-semibold text-text-muted dark:text-gray-400">RI</th>
                    <th class="border border-gray-200 dark:border-slate-700 px-3 py-2 text-center text-xs font-semibold text-text-muted dark:text-gray-400">n</th>
                    <th class="border border-gray-200 dark:border-slate-700 px-3 py-2 text-center text-xs font-semibold text-text-muted dark:text-gray-400">RI</th>
                </tr>
            </thead>
            <tbody>
                @php
                $riTable = [1=>0.00,2=>0.00,3=>0.58,4=>0.90,5=>1.12,6=>1.24,7=>1.32,8=>1.41,9=>1.45,10=>1.49,11=>1.51,12=>1.54,13=>1.56,14=>1.57,15=>1.58];
                $riKeys  = array_keys($riTable);
                $half    = ceil(count($riKeys)/2);
                @endphp
                @for($idx = 0; $idx < $half; $idx++)
                @php
                    $n1 = $riKeys[$idx];
                    $n2 = $riKeys[$idx + $half] ?? null;
                @endphp
                <tr class="{{ $n1 == $hasil['n'] || $n2 == $hasil['n'] ? 'bg-primary-light/40 dark:bg-primary-dark/20 font-bold' : ($idx%2==0?'bg-white dark:bg-slate-900':'bg-gray-50 dark:bg-slate-800') }}">
                    <td class="border border-gray-200 dark:border-slate-700 px-3 py-1.5 text-center {{ $n1 == $hasil['n'] ? 'text-primary dark:text-primary-light font-bold' : 'dark:text-gray-300' }}">{{ $n1 }}</td>
                    <td class="border border-gray-200 dark:border-slate-700 px-3 py-1.5 text-center font-mono {{ $n1 == $hasil['n'] ? 'text-primary dark:text-primary-light font-bold' : 'dark:text-gray-300' }}">{{ number_format($riTable[$n1], 2) }}</td>
                    @if($n2)
                    <td class="border border-gray-200 dark:border-slate-700 px-3 py-1.5 text-center {{ $n2 == $hasil['n'] ? 'text-primary dark:text-primary-light font-bold' : 'dark:text-gray-300' }}">{{ $n2 }}</td>
                    <td class="border border-gray-200 dark:border-slate-700 px-3 py-1.5 text-center font-mono {{ $n2 == $hasil['n'] ? 'text-primary dark:text-primary-light font-bold' : 'dark:text-gray-300' }}">{{ number_format($riTable[$n2], 2) }}</td>
                    @else
                    <td class="border border-gray-200 dark:border-slate-700 px-3 py-1.5" colspan="2"></td>
                    @endif
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>

{{-- TOMBOL HITUNG --}}
@if($hasil['konsisten'])
<div class="text-center">
    <form method="POST" action="{{ route('perhitungan.proses') }}">
        @csrf
        <button type="submit"
            class="bg-primary text-white px-8 py-3 rounded-xl font-semibold flex items-center gap-2 mx-auto hover:bg-primary-dark transition-colors">
            <span class="material-symbols-outlined">calculate</span>
            Jalankan Perhitungan & Tetapkan Golongan UKT
        </button>
    </form>
    <p class="text-xs text-text-muted mt-2">Pastikan semua mahasiswa sudah memiliki nilai kriteria sebelum menjalankan perhitungan.</p>
</div>
@endif

@endsection