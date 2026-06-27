@extends('layouts.app')

@section('title', 'Tahapan Perhitungan AHP')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <a href="{{ route('hasil.index') }}" class="hover:text-primary">Hasil</a>
    <span class="text-gray-400">/</span>
    <span class="text-on-surface font-medium">Tahapan AHP</span>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-on-surface">Transparansi Tahapan Perhitungan AHP</h1>
    <p class="text-text-muted text-sm mt-1">Periode: {{ $periode }} — Seluruh langkah perhitungan ditampilkan untuk keperluan audit dan dokumentasi</p>
</div>

@php $n = $ahpHasil['n']; @endphp

{{-- TAHAP 1 --}}
<div class="card p-6 mb-4">
    <h2 class="font-display font-semibold text-on-surface flex items-center gap-2 mb-4">
        <span class="bg-primary text-white w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold">1</span>
        Matriks Perbandingan Berpasangan
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th class="table-header text-left">Kriteria</th>
                    @foreach($kriterias as $k)
                    <th class="table-header text-center">{{ Str::limit($k->nama, 10) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($kriterias as $i => $baris)
                <tr class="table-row">
                    <td class="table-cell font-medium">{{ Str::limit($baris->nama, 15) }}</td>
                    @foreach($kriterias as $j => $kolom)
                    <td class="table-cell text-center font-mono {{ $i === $j ? 'bg-gray-100 dark:bg-slate-800' : '' }}">
                        {{ number_format($ahpHasil['matriks'][$i][$j], 4) }}
                    </td>
                    @endforeach
                </tr>
                @endforeach
                <tr class="bg-primary-light/40 dark:bg-primary-900/30 font-semibold text-primary">
                    <td class="table-cell">Jumlah Kolom</td>
                    @foreach($ahpHasil['jumlah_kolom'] as $jk)
                    <td class="table-cell text-center font-mono">{{ number_format($jk, 4) }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- TAHAP 2 --}}
<div class="card p-6 mb-4">
    <h2 class="font-display font-semibold text-on-surface flex items-center gap-2 mb-4">
        <span class="bg-primary text-white w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold">2</span>
        Matriks Ternormalisasi
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th class="table-header text-left">Kriteria</th>
                    @foreach($kriterias as $k)
                    <th class="table-header text-center">{{ Str::limit($k->nama, 10) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($kriterias as $i => $baris)
                <tr class="table-row">
                    <td class="table-cell font-medium">{{ Str::limit($baris->nama, 15) }}</td>
                    @foreach($kriterias as $j => $kolom)
                    <td class="table-cell text-center font-mono">
                        {{ number_format($ahpHasil['matriks_normal'][$i][$j], 4) }}
                    </td>
                    @endforeach
                </tr>
                @endforeach
                <tr class="bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 font-semibold">
                    <td class="table-cell">Total Kolom</td>
                    @for($j = 0; $j < $n; $j++)
                    <td class="table-cell text-center font-mono">1.0000</td>
                    @endfor
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- TAHAP 3 --}}
<div class="card p-6 mb-4">
    <h2 class="font-display font-semibold text-on-surface flex items-center gap-2 mb-4">
        <span class="bg-primary text-white w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold">3</span>
        Bobot Prioritas (Priority Vector)
    </h2>
    <table class="w-full text-sm max-w-2xl">
        <thead>
            <tr>
                <th class="table-header text-left">Kriteria</th>
                <th class="table-header text-center">Bobot</th>
                <th class="table-header text-center">%</th>
            </tr>
        </thead>
        <tbody>
            @php $maxBobot = max($ahpHasil['bobot']); @endphp
            @foreach($kriterias as $i => $k)
            <tr class="table-row {{ $ahpHasil['bobot'][$i] == $maxBobot ? 'bg-primary-light/30 dark:bg-primary-900/30 font-semibold' : '' }}">
                <td class="table-cell">{{ $k->nama }}</td>
                <td class="table-cell text-center font-mono">{{ number_format($ahpHasil['bobot'][$i], 4) }}</td>
                <td class="table-cell text-center text-primary font-semibold">{{ number_format($ahpHasil['bobot'][$i]*100, 2) }}%</td>
            </tr>
            @endforeach
            <tr class="bg-gray-50 dark:bg-slate-800/80 font-bold border-t border-gray-100 dark:border-slate-700">
                <td class="table-cell">Total</td>
                <td class="table-cell text-center font-mono">{{ number_format(array_sum($ahpHasil['bobot']), 4) }}</td>
                <td class="table-cell text-center">100.00%</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- TAHAP 4 --}}
<div class="card p-6 mb-4">
    <h2 class="font-display font-semibold text-on-surface flex items-center gap-2 mb-4">
        <span class="bg-primary text-white w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold">4</span>
        Uji Konsistensi
    </h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
        <div class="bg-gray-50 dark:bg-slate-800/80 rounded-xl p-4 text-center">
            <p class="text-xs text-text-muted mb-1">λmax</p>
            <p class="text-xl font-bold font-mono text-on-surface">{{ number_format($ahpHasil['lambda_max'], 4) }}</p>
        </div>
        <div class="bg-gray-50 dark:bg-slate-800/80 rounded-xl p-4 text-center">
            <p class="text-xs text-text-muted mb-1">CI = (λmax−n)/(n−1)</p>
            <p class="text-xl font-bold font-mono text-on-surface">{{ number_format($ahpHasil['ci'], 4) }}</p>
        </div>
        <div class="bg-gray-50 dark:bg-slate-800/80 rounded-xl p-4 text-center">
            <p class="text-xs text-text-muted mb-1">RI (n={{ $n }})</p>
            <p class="text-xl font-bold font-mono text-on-surface">{{ number_format($ahpHasil['ri'], 2) }}</p>
        </div>
        <div class="{{ $ahpHasil['konsisten'] ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }} rounded-xl p-4 text-center">
            <p class="text-xs text-text-muted mb-1">CR = CI/RI</p>
            <p class="text-xl font-bold font-mono {{ $ahpHasil['konsisten'] ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">{{ number_format($ahpHasil['cr'], 4) }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2 {{ $ahpHasil['konsisten'] ? 'text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20' : 'text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20' }} rounded-xl px-4 py-3">
        <span class="material-symbols-outlined">{{ $ahpHasil['konsisten'] ? 'check_circle' : 'cancel' }}</span>
        <span class="font-semibold">{{ $ahpHasil['konsisten'] ? 'Konsisten — CR ≤ 0.1' : 'Tidak Konsisten — CR > 0.1, harap perbaiki matriks' }}</span>
    </div>
</div>

{{-- TAHAP 5 & 6 --}}
@if($hasilUkt->isNotEmpty())
<div class="card p-6 mb-4">
    <h2 class="font-display font-semibold text-on-surface flex items-center gap-2 mb-4">
        <span class="bg-primary text-white w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold">5</span>
        Peringkat & Golongan UKT Final
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th class="table-header text-left">Peringkat</th>
                    <th class="table-header text-left">NIM</th>
                    <th class="table-header text-left">Nama</th>
                    <th class="table-header text-center">Skor Total</th>
                    <th class="table-header text-center">Golongan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hasilUkt as $h)
                @php $g = $h->golongan_ukt; @endphp
                <tr class="table-row">
                    <td class="table-cell font-bold text-text-muted">#{{ $h->peringkat }}</td>
                    <td class="table-cell font-mono text-xs">{{ $h->mahasiswa->nim }}</td>
                    <td class="table-cell font-medium">{{ $h->mahasiswa->nama }}</td>
                    <td class="table-cell text-center font-mono text-primary font-semibold">{{ number_format($h->skor_total, 6) }}</td>
                    <td class="table-cell text-center">
                        <span class="text-xs font-bold px-3 py-1 rounded-full
                            {{ $g==1?'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400':($g==2?'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400':($g==3?'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-500':($g==4?'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400':'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'))) }}">
                            UKT {{ $g }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $hasilUkt->links() }}
    </div>
</div>
@endif
@endsection