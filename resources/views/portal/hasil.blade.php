@extends('layouts.portal')
@section('title', 'Hasil Golongan UKT')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-on-surface">Hasil Golongan UKT</h1>
    <p class="text-text-muted text-sm mt-1">Periode: {{ $periode }}</p>
</div>

@if(!$hasilUkt)
<div class="card p-12 text-center">
    <span class="material-symbols-outlined text-5xl text-gray-300 block mb-3">hourglass_empty</span>
    <p class="font-semibold text-on-surface">Hasil belum tersedia</p>
    <p class="text-sm text-text-muted mt-1">Admin belum menjalankan perhitungan. Pastikan Anda sudah mengisi semua data terlebih dahulu.</p>
    <a href="{{ route('mahasiswa.portal.isiNilai') }}"
        class="mt-4 inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-primary-dark">
        <span class="material-symbols-outlined text-base">edit_note</span> Cek Data Saya
    </a>
</div>
@else
@php
    $g = $hasilUkt->golongan_ukt;
    $warna = [1=>'green',2=>'blue',3=>'yellow',4=>'orange',5=>'red'];
    $label = [1=>'Sangat Tidak Mampu',2=>'Tidak Mampu',3=>'Menengah ke Bawah',4=>'Menengah',5=>'Mampu'];
    $w = $warna[$g];
@endphp

{{-- Hasil Utama --}}
<div class="card p-8 text-center mb-6">
    <p class="text-text-muted text-sm mb-2">Golongan UKT Anda</p>
    <div class="w-24 h-24 rounded-full mx-auto flex items-center justify-center mb-4
        {{ $g==1?'bg-green-100':($g==2?'bg-blue-100':($g==3?'bg-yellow-100':($g==4?'bg-orange-100':'bg-red-100'))) }}">
        <span class="text-4xl font-bold font-display
            {{ $g==1?'text-green-700':($g==2?'text-blue-700':($g==3?'text-yellow-700':($g==4?'text-orange-700':'text-red-700'))) }}">
            {{ $g }}
        </span>
    </div>
    @php
        $nom = \App\Models\Pengaturan::nominalUkt($g);
    @endphp
    <h2 class="text-2xl font-display font-bold text-on-surface">{{ $nom['label'] }}</h2>
    <p class="text-text-muted mt-1">{{ $label[$g] }}</p>
    <div class="mt-3 inline-block bg-primary-light rounded-xl px-6 py-2">
        <p class="text-xs text-text-muted">Nominal UKT per Semester</p>
        <p class="text-xl font-bold text-primary font-mono">
            {{ \App\Models\Pengaturan::formatRupiah($nom['nominal']) }}
        </p>
    </div>
    <div class="mt-4 flex justify-center gap-4 text-sm">
        <div class="text-center">
            <p class="text-text-muted text-xs">Skor Total</p>
            <p class="font-mono font-bold text-primary">{{ number_format($hasilUkt->skor_total, 4) }}</p>
        </div>
        <div class="text-center">
            <p class="text-text-muted text-xs">Peringkat</p>
            <p class="font-bold text-on-surface">#{{ $hasilUkt->peringkat }}</p>
        </div>
    </div>
</div>

{{-- Detail Nilai yang Diisi --}}
<div class="card p-6">
    <h2 class="font-display font-semibold text-on-surface mb-4">Data yang Anda Masukkan</h2>
    <div class="space-y-3">
        @foreach($kriterias as $k)
        @php
            $nilai = $nilaiArr[$k->id]->nilai ?? null;
            $keterangan = $k->keterangan_skala ?? [];
        @endphp
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
            <div>
                <p class="text-sm font-medium text-on-surface">{{ $k->nama }}</p>
                @if($nilai && !empty($keterangan[$nilai]))
                    <p class="text-xs text-text-muted mt-0.5">{{ $keterangan[$nilai] }}</p>
                @endif
            </div>
            @if($nilai)
            <span class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center text-sm font-bold shrink-0">
                {{ $nilai }}
            </span>
            @else
            <span class="text-xs text-red-500">Belum diisi</span>
            @endif
        </div>
        @endforeach
    </div>
    <div class="mt-4 pt-4 border-t border-gray-100">
        <a href="{{ route('mahasiswa.portal.isiNilai') }}"
            class="text-sm text-primary font-medium flex items-center gap-1 hover:underline">
            <span class="material-symbols-outlined text-base">edit</span>
            Ubah Data Saya
        </a>
    </div>
</div>
@endif
@endsection