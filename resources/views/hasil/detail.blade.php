@extends('layouts.app')

@section('title', 'Detail Skor Mahasiswa')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <a href="{{ route('hasil.index') }}" class="hover:text-primary">Hasil</a>
    <span class="text-gray-400">/</span>
    <span class="text-on-surface font-medium">Detail Mahasiswa</span>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-on-surface">Detail Skor — {{ $mahasiswa->nama }}</h1>
    <p class="text-text-muted text-sm mt-1">NIM: {{ $mahasiswa->nim }} | Periode: {{ $periode }}</p>
</div>

@if($hasilUkt)
@php $g = $hasilUkt->golongan_ukt; @endphp
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="card p-5 text-center">
        <p class="text-xs text-text-muted mb-1">Skor Total</p>
        <p class="text-3xl font-bold font-mono text-primary">{{ number_format($hasilUkt->skor_total, 4) }}</p>
    </div>
    <div class="card p-5 text-center">
        <p class="text-xs text-text-muted mb-1">Peringkat</p>
        <p class="text-3xl font-bold text-on-surface">#{{ $hasilUkt->peringkat }}</p>
    </div>
    <div class="card p-5 text-center">
        <p class="text-xs text-text-muted mb-1">Golongan UKT</p>
        <span class="text-2xl font-bold px-4 py-1 rounded-full
            {{ $g==1?'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400':($g==2?'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400':($g==3?'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-500':($g==4?'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400':'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'))) }}">
            UKT {{ $g }}
        </span>
    </div>
</div>
@endif

<div class="card p-6">
    <h2 class="font-display font-semibold text-on-surface mb-4">Breakdown Kontribusi per Kriteria</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th class="table-header text-left">Kriteria</th>
                    <th class="table-header text-center">Nilai Input</th>
                    <th class="table-header text-center">Bobot</th>
                    <th class="table-header text-center">Kontribusi (Nilai × Bobot)</th>
                </tr>
            </thead>
            <tbody>
                @php $totalSkor = 0; @endphp
                @foreach($kriterias as $i => $k)
                @php
                    $nilai = $nilaiArr[$k->id]->nilai ?? 0;
                    $bobot = $ahpHasil['bobot'][$i] ?? 0;
                    $kontribusi = $nilai * $bobot;
                    $totalSkor += $kontribusi;
                @endphp
                <tr class="table-row">
                    <td class="table-cell font-medium">{{ $k->nama }}</td>
                    <td class="table-cell text-center font-bold text-on-surface">{{ $nilai }}</td>
                    <td class="table-cell text-center font-mono">{{ number_format($bobot, 4) }}</td>
                    <td class="table-cell text-center font-mono font-semibold text-primary">{{ number_format($kontribusi, 6) }}</td>
                </tr>
                @endforeach
                <tr class="bg-primary-light/40 dark:bg-primary-900/30 font-bold border-t border-gray-100 dark:border-slate-700">
                    <td class="table-cell text-primary" colspan="3">Skor Total</td>
                    <td class="table-cell text-center font-mono text-primary text-base">{{ number_format($totalSkor, 6) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('hasil.index') }}" class="text-sm text-primary font-medium flex items-center gap-1 w-fit hover:underline">
        ← Kembali ke Hasil
    </a>
</div>
@endsection