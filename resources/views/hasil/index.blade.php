@extends('layouts.app')

@section('title', 'Hasil Golongan UKT')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <span class="text-on-surface font-medium">Hasil & Golongan UKT</span>
@endsection

@section('content')
@php
$warna = [1=>'green',2=>'blue',3=>'yellow',4=>'orange',5=>'red'];
$labelGolongan = [1=>'Sangat Tidak Mampu',2=>'Tidak Mampu',3=>'Menengah ke Bawah',4=>'Menengah',5=>'Mampu'];
@endphp

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-display font-bold text-on-surface">Hasil & Golongan UKT</h1>
        <p class="text-text-muted text-sm mt-1">Periode: {{ $periode }}</p>
    </div>
    <div class="flex gap-3 flex-wrap">
        <a href="{{ route('hasil.tahapan') }}" class="border border-primary text-primary px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2 hover:bg-primary-light transition-colors">
            <span class="material-symbols-outlined text-base">analytics</span> Lihat Tahapan AHP
        </a>
        <form method="POST" action="{{ route('perhitungan.proses') }}" id="formHitung">
            @csrf
            <button type="button" class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2 hover:bg-primary-dark transition-colors"
                onclick="window.dispatchEvent(new CustomEvent('open-confirm', { detail: { title: 'Jalankan Perhitungan', message: 'Jalankan ulang perhitungan? Hasil lama periode ini akan ditimpa.', type: 'warning', confirmText: 'Jalankan', onConfirm: 'formHitung' } }))">
                <span class="material-symbols-outlined text-base">calculate</span> Jalankan Perhitungan
            </button>
        </form>
    </div>
</div>

@if($hasil->isEmpty())
<div class="card p-16 text-center">
    <div class="flex flex-col items-center justify-center">
        <svg class="w-48 h-48 text-gray-200 dark:text-slate-700 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <h3 class="text-xl font-display font-semibold text-on-surface dark:text-white mb-2">Belum Ada Hasil Perhitungan</h3>
        <p class="text-text-muted dark:text-gray-400 text-sm max-w-md mb-8">Pastikan matriks AHP sudah dikonfigurasi dan mahasiswa sudah memiliki nilai, lalu klik "Jalankan Perhitungan".</p>
        
        <form method="POST" action="{{ route('perhitungan.proses') }}" id="formHitungEmpty">
            @csrf
            <button type="button" class="bg-primary text-white px-8 py-3 rounded-xl font-medium flex items-center justify-center gap-2 hover:bg-primary-dark transition-all duration-300 shadow-lg shadow-primary/30 hover:-translate-y-1 mx-auto"
                onclick="window.dispatchEvent(new CustomEvent('open-confirm', { detail: { title: 'Jalankan Perhitungan', message: 'Jalankan ulang perhitungan? Hasil lama periode ini akan ditimpa.', type: 'warning', confirmText: 'Jalankan', onConfirm: 'formHitungEmpty' } }))">
                <span class="material-symbols-outlined text-xl">calculate</span> Jalankan Perhitungan Sekarang
            </button>
        </form>
    </div>
</div>
@else

{{-- Summary Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
    @foreach([1,2,3,4,5] as $g)
    @php $count = $distribusi[$g] ?? 0; @endphp
    <div class="card p-4 text-center">
        <div class="w-12 h-12 rounded-2xl mx-auto mb-3 flex items-center justify-center shadow-inner
            {{ $g==1?'bg-green-100 dark:bg-green-900/30':($g==2?'bg-blue-100 dark:bg-blue-900/30':($g==3?'bg-yellow-100 dark:bg-yellow-900/30':($g==4?'bg-orange-100 dark:bg-orange-900/30':'bg-red-100 dark:bg-red-900/30'))) }}">
            <span class="font-bold text-xl
                {{ $g==1?'text-green-700 dark:text-green-400':($g==2?'text-blue-700 dark:text-blue-400':($g==3?'text-yellow-700 dark:text-yellow-400':($g==4?'text-orange-700 dark:text-orange-400':'text-red-700 dark:text-red-400'))) }}">{{ $g }}</span>
        </div>
        <p class="text-3xl font-display font-bold text-on-surface dark:text-white">{{ $count }}</p>
        <p class="text-xs text-text-muted dark:text-gray-400 mt-1 uppercase tracking-wider font-semibold">UKT {{ $g }}</p>
    </div>
    @endforeach
</div>

{{-- Tabel Hasil --}}
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th class="table-header">Peringkat</th>
                    <th class="table-header">NIM</th>
                    <th class="table-header">Nama</th>
                    <th class="table-header">Program Studi</th>
                    <th class="table-header text-center">Skor Total</th>
                    <th class="table-header text-center">Golongan UKT</th>
                    <th class="table-header text-center">Detail</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @foreach($hasil as $h)
                <tr class="table-row">
                    <td class="table-cell font-bold text-text-muted dark:text-gray-400">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 dark:bg-slate-700 text-xs">
                            #{{ $h->peringkat }}
                        </div>
                    </td>
                    <td class="table-cell font-mono">{{ $h->mahasiswa->nim }}</td>
                    <td class="table-cell font-medium">{{ $h->mahasiswa->nama }}</td>
                    <td class="table-cell">{{ $h->mahasiswa->program_studi ?? '-' }}</td>
                    <td class="table-cell text-center font-mono font-semibold text-primary dark:text-primary-light">{{ number_format($h->skor_total, 4) }}</td>
                    <td class="table-cell text-center">
                        @php
                            $g = $h->golongan_ukt;
                            $nom = \App\Models\Pengaturan::nominalUkt($g);
                        @endphp
                        <span class="text-xs font-bold px-3 py-1 rounded-full block mb-1
                            {{ $g==1?'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400':($g==2?'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400':($g==3?'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400':($g==4?'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400':'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'))) }}">
                            {{ $nom['label'] }}
                        </span>
                        <span class="text-xs text-text-muted dark:text-gray-400 font-mono">
                            {{ \App\Models\Pengaturan::formatRupiah($nom['nominal']) }}
                        </span>
                    </td>
                    <td class="table-cell text-center">
                        <a href="{{ route('hasil.detailMahasiswa', $h->mahasiswa) }}" class="text-primary hover:text-primary-dark dark:text-primary-light p-2 rounded-lg hover:bg-primary/10 transition-colors inline-block">
                            <span class="material-symbols-outlined text-base">open_in_new</span>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $hasil->links() }}
</div>
@endif
@endsection