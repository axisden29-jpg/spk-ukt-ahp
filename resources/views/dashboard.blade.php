@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <span class="text-on-surface font-medium">Dashboard</span>
@endsection

@section('content')
@php
    $periode        = \App\Models\Pengaturan::get('periode_aktif', '2024/2025');
    $totalKriteria  = \App\Models\Kriteria::aktif()->count();
    $totalMahasiswa = \App\Models\Mahasiswa::count();
    $totalHasil     = \App\Models\HasilUkt::where('periode', $periode)->count();
    $distribusi     = \App\Models\HasilUkt::where('periode', $periode)->get()->groupBy('golongan_ukt');

    // Status pengisian
    $mahasiswaIds      = \App\Models\Mahasiswa::pluck('id');
    $sudahIsi          = \App\Models\NilaiMahasiswa::where('periode', $periode)
                            ->whereIn('mahasiswa_id', $mahasiswaIds)
                            ->distinct('mahasiswa_id')->count('mahasiswa_id');
    $belumIsi          = $totalMahasiswa - $sudahIsi;

    // CR dari matriks terakhir
    $matriksAda = \App\Models\MatriksAhp::where('periode', $periode)->exists();
@endphp

{{-- HEADER BANNER --}}
<div class="relative overflow-hidden bg-gradient-to-br from-primary to-primary-dark rounded-3xl p-8 mb-8 shadow-xl shadow-primary/20">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 blur-3xl rounded-full -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 right-32 w-40 h-40 bg-white opacity-10 blur-2xl rounded-full -mb-10"></div>
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1.5px, transparent 1.5px); background-size: 24px 24px;"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            @php
                $hour = now()->format('H');
                $greeting = 'Selamat Pagi';
                if ($hour >= 12 && $hour < 15) $greeting = 'Selamat Siang';
                elseif ($hour >= 15 && $hour < 18) $greeting = 'Selamat Sore';
                elseif ($hour >= 18) $greeting = 'Selamat Malam';
                $firstName = explode(' ', auth()->user()->name)[0];
            @endphp
            <h1 class="text-3xl font-display font-bold text-white mb-2">{{ $greeting }}, {{ $firstName }}</h1>
            <p class="text-primary-light text-sm max-w-lg leading-relaxed">
                Periode aktif sistem saat ini adalah <strong>{{ $periode }}</strong>. 
                @if($belumIsi > 0)
                    Ada <strong class="text-white">{{ $belumIsi }} mahasiswa</strong> yang masih menunggu pengisian kelengkapan data.
                @else
                    Semua data mahasiswa telah lengkap, siap untuk kalkulasi hasil UKT.
                @endif
            </p>
        </div>
        <div class="hidden lg:flex bg-white/10 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-2xl p-4 items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-white/20 dark:bg-white/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-2xl">calendar_month</span>
            </div>
            <div>
                <p class="text-white font-bold text-lg font-display tracking-wide">{{ now()->translatedFormat('d M Y') }}</p>
                <p class="text-primary-light text-xs uppercase tracking-wider font-semibold">Hari Ini</p>
            </div>
        </div>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="card p-5 relative group overflow-hidden">
        <div class="flex items-center justify-between mb-4 relative z-10">
            <div class="w-10 h-10 bg-primary-light rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <span class="material-symbols-outlined text-primary text-xl">checklist</span>
            </div>
            <span class="text-xs text-primary bg-primary-light px-2.5 py-1 rounded-full font-semibold">Kriteria</span>
        </div>
        <div class="flex items-end justify-between relative z-10">
            <div>
                <p class="text-3xl font-bold font-display text-on-surface">{{ $totalKriteria }}</p>
                <p class="text-xs text-text-muted mt-1">Aktif & Digunakan</p>
            </div>
            <div class="w-20" id="spark1"></div>
        </div>
    </div>

    <div class="card p-5 relative group overflow-hidden">
        <div class="flex items-center justify-between mb-4 relative z-10">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <span class="material-symbols-outlined text-blue-600 text-xl">group</span>
            </div>
            <span class="text-xs text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full font-semibold">Total</span>
        </div>
        <div class="flex items-end justify-between relative z-10">
            <div>
                <p class="text-3xl font-bold font-display text-on-surface">{{ $totalMahasiswa }}</p>
                <p class="text-xs text-text-muted mt-1">Mahasiswa Terdaftar</p>
            </div>
            <div class="w-20" id="spark2"></div>
        </div>
    </div>

    <div class="card p-5 relative group overflow-hidden">
        <div class="flex items-center justify-between mb-4 relative z-10">
            <div class="w-10 h-10 {{ $sudahIsi == $totalMahasiswa && $totalMahasiswa > 0 ? 'bg-green-100' : 'bg-yellow-100' }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <span class="material-symbols-outlined {{ $sudahIsi == $totalMahasiswa && $totalMahasiswa > 0 ? 'text-green-600' : 'text-yellow-600' }} text-xl">edit_note</span>
            </div>
            <span class="text-xs {{ $belumIsi > 0 ? 'text-yellow-600 bg-yellow-50' : 'text-green-600 bg-green-50' }} px-2.5 py-1 rounded-full font-semibold">
                {{ $belumIsi > 0 ? $belumIsi . ' belum' : 'Lengkap' }}
            </span>
        </div>
        <div class="flex items-end justify-between relative z-10">
            <div>
                <p class="text-3xl font-bold font-display text-on-surface">{{ $sudahIsi }}</p>
                <p class="text-xs text-text-muted mt-1">Sudah Mengisi Data</p>
            </div>
            <div class="w-20" id="spark3"></div>
        </div>
    </div>

    <div class="card p-5 relative group overflow-hidden">
        <div class="flex items-center justify-between mb-4 relative z-10">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <span class="material-symbols-outlined text-purple-600 text-xl">workspace_premium</span>
            </div>
            <span class="text-xs text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full font-semibold">Selesai</span>
        </div>
        <div class="flex items-end justify-between relative z-10">
            <div>
                <p class="text-3xl font-bold font-display text-on-surface">{{ $totalHasil }}</p>
                <p class="text-xs text-text-muted mt-1">Sudah Ditetapkan</p>
            </div>
            <div class="w-20" id="spark4"></div>
        </div>
    </div>
</div>

{{-- MAIN CONTENT --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Distribusi & Chart --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Chart Distribusi --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display font-semibold text-on-surface">Distribusi Golongan UKT</h2>
                @if($totalHasil > 0)
                <a href="{{ route('laporan.index') }}"
                    class="text-xs text-primary font-medium hover:underline flex items-center gap-1">
                    Lihat Laporan <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
                @endif
            </div>
            @if($totalHasil > 0)
                <div id="chartDistribusi" class="min-h-[250px]"></div>
                {{-- Mini summary --}}
                <div class="flex gap-2 mt-4 flex-wrap">
                    @foreach([1=>'green',2=>'blue',3=>'yellow',4=>'orange',5=>'red'] as $g => $w)
                    @php $count = $distribusi->get($g, collect())->count(); $nom = \App\Models\Pengaturan::nominalUkt($g); @endphp
                    <div class="flex-1 min-w-0 text-center bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700 rounded-xl p-2">
                        <p class="text-xs font-bold text-on-surface">{{ $count }}</p>
                        <p class="text-xs text-text-muted truncate">{{ $nom['label'] }}</p>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-text-muted">
                    <svg class="w-32 h-32 text-gray-200 dark:text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-sm">Belum ada data perhitungan</p>
                    <a href="{{ route('hasil.index') }}"
                        class="mt-3 text-primary text-sm font-medium hover:underline">
                        Jalankan Perhitungan →
                    </a>
                </div>
            @endif
        </div>

        {{-- Status Checklist --}}
        <div class="card p-6">
            <h2 class="font-display font-semibold text-on-surface mb-4">Status Kelengkapan Sistem</h2>
            <div class="space-y-3">
                @php
                    $checks = [
                        ['label' => 'Kriteria aktif tersedia', 'ok' => $totalKriteria >= 2, 'hint' => 'Minimal 2 kriteria aktif', 'link' => route('kriteria.index')],
                        ['label' => 'Matriks AHP sudah dikonfigurasi', 'ok' => $matriksAda, 'hint' => 'Matriks perbandingan berpasangan', 'link' => route('ahp.matriks')],
                        ['label' => 'Data mahasiswa tersedia', 'ok' => $totalMahasiswa > 0, 'hint' => 'Minimal 1 mahasiswa terdaftar', 'link' => route('mahasiswa.index')],
                        ['label' => 'Semua mahasiswa sudah mengisi data', 'ok' => $totalMahasiswa > 0 && $belumIsi === 0, 'hint' => $belumIsi . ' mahasiswa belum mengisi', 'link' => route('mahasiswa.status')],
                        ['label' => 'Perhitungan golongan UKT sudah dijalankan', 'ok' => $totalHasil > 0, 'hint' => 'Jalankan dari halaman Hasil', 'link' => route('hasil.index')],
                    ];
                @endphp
                @foreach($checks as $check)
                <div class="flex items-center gap-3 p-3 rounded-xl {{ $check['ok'] ? 'bg-green-50 dark:bg-green-900/20' : 'bg-gray-50 dark:bg-slate-800/50' }}">
                    <span class="material-symbols-outlined text-lg shrink-0 {{ $check['ok'] ? 'text-green-500' : 'text-gray-300 dark:text-slate-600' }}">
                        {{ $check['ok'] ? 'check_circle' : 'radio_button_unchecked' }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium {{ $check['ok'] ? 'text-green-800' : 'text-text-muted' }}">
                            {{ $check['label'] }}
                        </p>
                        @if(!$check['ok'])
                        <p class="text-xs text-text-muted mt-0.5">{{ $check['hint'] }}</p>
                        @endif
                    </div>
                    @if(!$check['ok'])
                    <a href="{{ $check['link'] }}"
                        class="text-xs text-primary font-medium hover:underline shrink-0">
                        Perbaiki →
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Sidebar Kanan --}}
    <div class="space-y-5">

        {{-- Quick Actions --}}
        <div class="card p-5">
            <h2 class="font-display font-semibold text-on-surface mb-3">Aksi Cepat</h2>
            <div class="space-y-2">
                @foreach([
                    ['Kelola Kriteria', 'checklist', route('kriteria.index'), 'text-primary bg-primary-light dark:bg-primary/20'],
                    ['Input Matriks AHP', 'grid_on', route('ahp.matriks'), 'text-primary bg-primary-light dark:bg-primary/20'],
                    ['Tambah Mahasiswa', 'person_add', route('mahasiswa.index'), 'text-blue-600 bg-blue-50 dark:bg-blue-900/20'],
                    ['Status Pengisian', 'analytics', route('mahasiswa.status'), 'text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20'],
                    ['Jalankan Perhitungan', 'calculate', route('hasil.index'), 'text-green-600 bg-green-50 dark:bg-green-900/20'],
                    ['Lihat Laporan', 'summarize', route('laporan.index'), 'text-purple-600 bg-purple-50 dark:bg-purple-900/20'],
                ] as [$label, $icon, $href, $color])
                <a href="{{ $href }}"
                    class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $color }}">
                        <span class="material-symbols-outlined text-base">{{ $icon }}</span>
                    </div>
                    <span class="text-sm font-medium text-on-surface dark:text-gray-200 group-hover:text-primary transition-colors">{{ $label }}</span>
                    <span class="material-symbols-outlined text-gray-300 dark:text-slate-600 text-base ml-auto group-hover:text-primary transition-colors">chevron_right</span>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Info Periode --}}
        <div class="card p-5">
            <h2 class="font-display font-semibold text-on-surface mb-3">Periode Aktif</h2>
            <div class="bg-primary-light dark:bg-primary/20 rounded-xl p-4 text-center mb-3">
                <span class="material-symbols-outlined text-primary text-3xl block mb-1">calendar_month</span>
                <p class="text-xl font-bold font-display text-primary">{{ $periode }}</p>
            </div>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-text-muted">Total Mahasiswa</span>
                    <span class="font-semibold text-on-surface">{{ $totalMahasiswa }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-muted">Sudah Isi Data</span>
                    <span class="font-semibold {{ $sudahIsi == $totalMahasiswa && $totalMahasiswa > 0 ? 'text-green-600' : 'text-yellow-600' }}">{{ $sudahIsi }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-muted">Sudah Ditetapkan</span>
                    <span class="font-semibold text-primary">{{ $totalHasil }}</span>
                </div>
            </div>
            <a href="{{ route('pengaturan.golongan') }}"
                class="mt-3 text-xs text-primary font-medium flex items-center gap-1 hover:underline">
                <span class="material-symbols-outlined text-sm">settings</span> Ubah Pengaturan
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
@if($totalHasil > 0)
<script>
    const distribusi = @json($distribusi->map->count());
    const data = [1, 2, 3, 4, 5].map(g => distribusi[g] || 0);

    const isDark = document.documentElement.classList.contains('dark');
    
    const options = {
        theme: {
            mode: isDark ? 'dark' : 'light'
        },
        series: [{
            name: 'Jumlah Mahasiswa',
            data: data
        }],
        chart: {
            type: 'bar',
            height: 250,
            fontFamily: "'Inter', sans-serif",
            toolbar: { show: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: { enabled: true, delay: 150 },
                dynamicAnimation: { enabled: true, speed: 350 }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '45%',
                distributed: true,
            }
        },
        colors: ['#10b981', '#3b82f6', '#f59e0b', '#f97316', '#ef4444'], // Emerald, Blue, Amber, Orange, Red
        dataLabels: {
            enabled: false
        },
        legend: {
            show: false
        },
        xaxis: {
            categories: ['UKT 1', 'UKT 2', 'UKT 3', 'UKT 4', 'UKT 5'],
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    colors: '#64748b',
                    fontSize: '12px',
                    fontWeight: 500
                }
            }
        },
        yaxis: {
            labels: {
                style: { colors: '#94a3b8' },
                formatter: (value) => value.toFixed(0)
            }
        },
        grid: {
            borderColor: isDark ? '#1b3d30' : '#f1f5f9', // #1b3d30 is our new dark green slate-700
            strokeDashArray: 4, // creates beautiful dashed background lines
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } },
            padding: { top: 0, right: 0, bottom: 0, left: 10 }
        },
        tooltip: {
            theme: 'dark', // modern sleek dark tooltip
            style: {
                fontSize: '13px',
                fontFamily: "'Inter', sans-serif"
            },
            y: {
                formatter: function (val) {
                    return val + " mahasiswa"
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#chartDistribusi"), options);
    chart.render();

    // Listen for theme changes to update chart
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === "class") {
                const newIsDark = document.documentElement.classList.contains('dark');
                chart.updateOptions({
                    theme: { mode: newIsDark ? 'dark' : 'light' },
                    grid: { borderColor: newIsDark ? '#1b3d30' : '#f1f5f9' }
                });
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true });
</script>
@endif

<script>
    // Sparklines Configuration
    const commonSparklineOptions = {
        chart: {
            type: 'area',
            height: 45,
            sparkline: { enabled: true },
            animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0, stops: [0, 100] }
        },
        tooltip: {
            fixed: { enabled: false },
            x: { show: false },
            y: { title: { formatter: function (seriesName) { return '' } } },
            marker: { show: false }
        }
    };

    new ApexCharts(document.querySelector("#spark1"), {
        ...commonSparklineOptions,
        series: [{ data: [{{ max(0, $totalKriteria-5) }}, {{ max(0, $totalKriteria-2) }}, {{ $totalKriteria }}] }],
        colors: ['#006b3f']
    }).render();

    new ApexCharts(document.querySelector("#spark2"), {
        ...commonSparklineOptions,
        series: [{ data: [{{ max(0, $totalMahasiswa-20) }}, {{ max(0, $totalMahasiswa-5) }}, {{ $totalMahasiswa }}] }],
        colors: ['#2563eb']
    }).render();

    new ApexCharts(document.querySelector("#spark3"), {
        ...commonSparklineOptions,
        series: [{ data: [{{ max(0, $sudahIsi-10) }}, {{ max(0, $sudahIsi-3) }}, {{ $sudahIsi }}] }],
        colors: ['{{ $sudahIsi == $totalMahasiswa && $totalMahasiswa > 0 ? "#16a34a" : "#ca8a04" }}']
    }).render();

    new ApexCharts(document.querySelector("#spark4"), {
        ...commonSparklineOptions,
        series: [{ data: [{{ max(0, $totalHasil-10) }}, {{ max(0, $totalHasil-2) }}, {{ $totalHasil }}] }],
        colors: ['#9333ea']
    }).render();
</script>
@endpush