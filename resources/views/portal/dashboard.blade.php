@extends('layouts.portal')
@section('title', 'Dashboard Mahasiswa')

@section('content')
@php
    $totalKriteria   = $kriterias->count();
    $sudahLengkap    = $nilaiSudahDiisi >= $totalKriteria && $totalKriteria > 0;
    $g               = $hasilUkt?->golongan_ukt;
    $warnaGolongan   = match($g) {
        1 => 'green', 2 => 'blue', 3 => 'yellow', 4 => 'orange', 5 => 'red', default => 'gray'
    };
    $labelGolongan = [1=>'Sangat Tidak Mampu',2=>'Tidak Mampu',3=>'Menengah ke Bawah',4=>'Menengah',5=>'Mampu'];
    $nom = $g ? \App\Models\Pengaturan::nominalUkt($g) : null;
@endphp

{{-- HERO GREETING --}}
<div class="bg-primary rounded-2xl px-6 py-5 mb-6 flex items-center justify-between gap-4">
    <div>
        <p class="text-white/70 text-sm mb-1">Selamat datang,</p>
        <h1 class="text-white font-display font-bold text-xl leading-tight">{{ $mahasiswa->nama }}</h1>
        <div class="flex items-center gap-3 mt-2 flex-wrap">
            <span class="bg-white/20 text-white text-xs px-3 py-1 rounded-full font-mono">
                NIM: {{ $mahasiswa->nim }}
            </span>
            @if($mahasiswa->program_studi)
            <span class="bg-white/20 text-white text-xs px-3 py-1 rounded-full">
                {{ $mahasiswa->program_studi }}
            </span>
            @endif
            <span class="bg-white/20 text-white text-xs px-3 py-1 rounded-full">
                📅 Periode {{ $periode }}
            </span>
        </div>
    </div>
    <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center shrink-0">
        <span class="material-symbols-outlined text-white text-3xl">person</span>
    </div>
</div>

{{-- STATUS CARDS --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    {{-- Status Pengisian --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Status Pengisian</p>
            <span class="material-symbols-outlined text-xl {{ $sudahLengkap ? 'text-green-500' : 'text-yellow-500' }}">
                {{ $sudahLengkap ? 'check_circle' : 'pending' }}
            </span>
        </div>
        <p class="text-2xl font-bold font-display text-on-surface">{{ $nilaiSudahDiisi }}/{{ $totalKriteria }}</p>
        <p class="text-xs text-text-muted mt-1">Kriteria telah diisi</p>
        <div class="mt-3 bg-gray-100 rounded-full h-2">
            <div class="h-2 rounded-full transition-all {{ $sudahLengkap ? 'bg-green-500' : 'bg-yellow-400' }}"
                style="width: {{ $totalKriteria > 0 ? round($nilaiSudahDiisi/$totalKriteria*100) : 0 }}%"></div>
        </div>
    </div>

    {{-- Golongan UKT --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Golongan UKT</p>
            <span class="material-symbols-outlined text-xl {{ $hasilUkt ? 'text-primary' : 'text-gray-300' }}">leaderboard</span>
        </div>
        @if($hasilUkt)
            <p class="text-2xl font-bold font-display text-on-surface">UKT {{ $g }}</p>
            <p class="text-xs text-text-muted mt-1">{{ $labelGolongan[$g] ?? '' }}</p>
            <span class="mt-2 inline-block text-xs font-bold px-2 py-1 rounded-full
                {{ $g==1?'bg-green-100 text-green-700':($g==2?'bg-blue-100 text-blue-700':($g==3?'bg-yellow-100 text-yellow-700':($g==4?'bg-orange-100 text-orange-700':'bg-red-100 text-red-700'))) }}">
                {{ $nom['label'] ?? 'UKT '.$g }}
            </span>
        @else
            <p class="text-2xl font-bold font-display text-gray-300">—</p>
            <p class="text-xs text-text-muted mt-1">Belum dihitung oleh admin</p>
        @endif
    </div>

    {{-- Nominal UKT --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Nominal UKT</p>
            <span class="material-symbols-outlined text-xl {{ $hasilUkt ? 'text-primary' : 'text-gray-300' }}">payments</span>
        </div>
        @if($hasilUkt && $nom)
            <p class="text-lg font-bold font-mono text-primary leading-tight">
                {{ \App\Models\Pengaturan::formatRupiah($nom['nominal']) }}
            </p>
            <p class="text-xs text-text-muted mt-1">per semester</p>
            <p class="text-xs text-primary font-medium mt-2">Peringkat #{{ $hasilUkt->peringkat }}</p>
        @else
            <p class="text-lg font-bold text-gray-300">Rp —</p>
            <p class="text-xs text-text-muted mt-1">Menunggu perhitungan</p>
        @endif
    </div>
</div>

{{-- LANGKAH-LANGKAH --}}
<div class="card p-6 mb-5">
    <h2 class="font-display font-semibold text-on-surface mb-4">Langkah Pengajuan UKT</h2>
    <div class="space-y-3">

        {{-- Langkah 1 --}}
        <div class="flex items-start gap-4 p-4 rounded-xl border-2 transition-colors
            {{ $sudahLengkap ? 'border-green-200 bg-green-50' : 'border-primary/30 bg-primary-light/40' }}">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0
                {{ $sudahLengkap ? 'bg-green-500 text-white' : 'bg-primary text-white' }}">
                {{ $sudahLengkap ? '✓' : '1' }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-on-surface">Isi Data Ekonomi Keluarga</p>
                <p class="text-xs text-text-muted mt-0.5">
                    Jawab {{ $totalKriteria }} pertanyaan sesuai kondisi nyata keluarga Anda.
                    @if($nilaiSudahDiisi > 0 && !$sudahLengkap)
                        <span class="text-yellow-600 font-medium">{{ $totalKriteria - $nilaiSudahDiisi }} pertanyaan lagi.</span>
                    @endif
                </p>
            </div>
            @if(!$sudahLengkap)
                <a href="{{ route('mahasiswa.portal.isiNilai') }}"
                    class="bg-primary text-white text-xs px-4 py-2 rounded-xl font-semibold hover:bg-primary-dark transition-colors shrink-0 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">edit_note</span>
                    {{ $nilaiSudahDiisi > 0 ? 'Lanjutkan' : 'Mulai' }}
                </a>
            @else
                <a href="{{ route('mahasiswa.portal.isiNilai') }}"
                    class="border border-green-400 text-green-700 text-xs px-4 py-2 rounded-xl font-semibold hover:bg-green-100 transition-colors shrink-0">
                    Edit Data
                </a>
            @endif
        </div>

        {{-- Langkah 2 --}}
        <div class="flex items-start gap-4 p-4 rounded-xl border-2 border-gray-100 bg-gray-50">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0
                {{ $hasilUkt ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                {{ $hasilUkt ? '✓' : '2' }}
            </div>
            <div class="flex-1">
                <p class="font-semibold text-sm {{ $hasilUkt ? 'text-on-surface' : 'text-text-muted' }}">
                    Tunggu Proses Perhitungan Admin
                </p>
                <p class="text-xs text-text-muted mt-0.5">
                    Admin akan memproses data seluruh mahasiswa menggunakan metode AHP setelah semua data masuk.
                </p>
            </div>
            @if($hasilUkt)
                <span class="text-xs font-semibold text-primary shrink-0">✓ Selesai</span>
            @endif
        </div>

        {{-- Langkah 3 --}}
        <div class="flex items-start gap-4 p-4 rounded-xl border-2 transition-colors
            {{ $hasilUkt ? 'border-blue-200 bg-blue-50' : 'border-gray-100 bg-gray-50' }}">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0
                {{ $hasilUkt ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                {{ $hasilUkt ? '✓' : '3' }}
            </div>
            <div class="flex-1">
                <p class="font-semibold text-sm {{ $hasilUkt ? 'text-on-surface' : 'text-text-muted' }}">
                    Lihat Hasil Golongan UKT
                </p>
                <p class="text-xs text-text-muted mt-0.5">
                    Cek golongan dan nominal UKT yang telah ditetapkan untuk Anda.
                </p>
            </div>
            @if($hasilUkt)
                <a href="{{ route('mahasiswa.portal.hasil') }}"
                    class="bg-blue-500 text-white text-xs px-4 py-2 rounded-xl font-semibold hover:bg-blue-600 transition-colors shrink-0 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">open_in_new</span> Lihat
                </a>
            @endif
        </div>

    </div>
</div>

{{-- INFO PENTING --}}
<div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 flex items-start gap-3">
    <span class="material-symbols-outlined text-amber-500 text-xl mt-0.5 shrink-0">info</span>
    <div>
        <p class="font-semibold text-amber-800 text-sm">Perhatian</p>
        <p class="text-amber-700 text-xs mt-1 leading-relaxed">
            Isi data dengan <strong>jujur dan sesuai kondisi nyata</strong> keluarga Anda.
            Sistem akan memproses data menggunakan metode AHP untuk menentukan golongan UKT yang adil.
            <strong>Kesalahan pengisian adalah tanggung jawab Anda sendiri.</strong>
        </p>
    </div>
</div>

@endsection