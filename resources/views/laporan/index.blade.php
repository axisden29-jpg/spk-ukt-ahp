@extends('layouts.app')

@section('title', 'Laporan Distribusi UKT')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <span class="text-on-surface font-medium">Laporan</span>
@endsection

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-display font-bold text-on-surface">Laporan Distribusi UKT</h1>
        <p class="text-text-muted text-sm mt-1">Periode: {{ $periode }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('laporan.export', 'pdf') }}" class="border border-red-400 text-red-600 px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2 hover:bg-red-50">
            <span class="material-symbols-outlined text-base">picture_as_pdf</span> Export PDF
        </a>
        <a href="{{ route('laporan.export', 'excel') }}" class="border border-green-500 text-green-700 px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2 hover:bg-green-50">
            <span class="material-symbols-outlined text-base">table_view</span> Export Excel
        </a>
    </div>
</div>

@if($hasil->isEmpty())
<div class="card p-12 text-center text-text-muted">
    <span class="material-symbols-outlined text-5xl text-gray-300 block mb-3">summarize</span>
    <p>Belum ada data laporan. Jalankan perhitungan terlebih dahulu.</p>
</div>
@else

{{-- Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="card p-6">
        <h2 class="font-display font-semibold text-on-surface mb-4">Distribusi per Golongan (Bar)</h2>
        <div id="chartBar"></div>
    </div>
    <div class="card p-6">
        <h2 class="font-display font-semibold text-on-surface mb-4">Proporsi Golongan (Pie)</h2>
        <div id="chartPie"></div>
    </div>
</div>

{{-- Tabel Rekap --}}
<div class="card p-6 mb-6">
    <h2 class="font-display font-semibold text-on-surface mb-4">Rekap per Golongan</h2>
    <table class="w-full text-sm">
        <thead>
            <tr>
                <th class="table-header text-left">Golongan</th>
                <th class="table-header text-center">Nominal</th>
                <th class="table-header text-center">Jumlah</th>
                <th class="table-header text-center">Persentase</th>
            </tr>
        </thead>
        <tbody id="tableBodyRekap">
            @php $total = \App\Models\HasilUkt::where('periode', $periode)->count(); @endphp
            @foreach([1=>'Sangat Tidak Mampu',2=>'Tidak Mampu',3=>'Menengah ke Bawah',4=>'Menengah',5=>'Mampu'] as $g => $labelG)
            @php
                $count = $distribusi->get($g, 0);
                $nom   = \App\Models\Pengaturan::nominalUkt($g);
            @endphp
            <tr class="table-row row-data">
                <td class="table-cell font-medium">{{ $nom['label'] }} — {{ $labelG }}</td>
                <td class="table-cell text-center font-mono font-semibold">
                    {{ \App\Models\Pengaturan::formatRupiah($nom['nominal']) }}
                </td>
                <td class="table-cell text-center font-bold">{{ $count }}</td>
                <td class="table-cell text-center text-primary font-semibold">
                    {{ $total > 0 ? number_format($count/$total*100, 1) : 0 }}%
                </td>
            </tr>
            @endforeach
            <tr class="bg-gray-50 dark:bg-slate-800/80 font-bold border-t border-gray-100 dark:border-slate-700">
                <td class="table-cell" colspan="2">Total</td>
                <td class="table-cell text-center">{{ $total }}</td>
                <td class="table-cell text-center">100%</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Tabel Detail --}}
<div class="card overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h2 class="font-display font-semibold text-on-surface">Detail Seluruh Mahasiswa</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th class="table-header text-left">Peringkat</th>
                    <th class="table-header text-left">NIM</th>
                    <th class="table-header text-left">Nama</th>
                    <th class="table-header text-center">Skor</th>
                    <th class="table-header text-center">Golongan</th>
                </tr>
            </thead>
            <tbody id="tableBodyDetail">
                @foreach($hasil as $h)
                @php $g = $h->golongan_ukt; @endphp
                <tr class="table-row row-data">
                    <td class="table-cell text-text-muted">#{{ $h->peringkat }}</td>
                    <td class="table-cell font-mono text-xs">{{ $h->mahasiswa->nim }}</td>
                    <td class="table-cell font-medium">{{ $h->mahasiswa->nama }}</td>
                    <td class="table-cell text-center font-mono text-primary">{{ number_format($h->skor_total, 4) }}</td>
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
</div>

<div class="mt-4">
    {{ $hasil->links() }}
</div>
@endif
@endsection

@push('scripts')
@if($hasil->isNotEmpty())
<script>
    const distribusi = @json($distribusi);
    const labels  = ['UKT 1', 'UKT 2', 'UKT 3', 'UKT 4', 'UKT 5'];
    const data    = [1, 2, 3, 4, 5].map(g => distribusi[g] || 0);
    const colors  = ['#22c55e', '#3b82f6', '#eab308', '#f97316', '#ef4444']; // Tailwind colors for consistency

    const isDark = document.documentElement.classList.contains('dark');
    
    // ApexCharts Bar
    const barOptions = {
        theme: { mode: isDark ? 'dark' : 'light' },
        series: [{ name: 'Mahasiswa', data: data }],
        chart: { type: 'bar', height: 250, toolbar: { show: false }, fontFamily: 'Inter, sans-serif', background: 'transparent' },
        plotOptions: { bar: { borderRadius: 4, distributed: true, columnWidth: '50%' } },
        colors: colors,
        xaxis: { categories: labels, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { formatter: (val) => Math.floor(val) } },
        dataLabels: { enabled: true, style: { fontSize: '12px' } },
        legend: { show: false },
        grid: { borderColor: isDark ? '#1b3d30' : '#f1f5f9', strokeDashArray: 4 }
    };
    const chartBar = new ApexCharts(document.getElementById('chartBar'), barOptions);
    chartBar.render();

    // ApexCharts Donut
    const pieOptions = {
        theme: { mode: isDark ? 'dark' : 'light' },
        series: data,
        chart: { type: 'donut', height: 250, fontFamily: 'Inter, sans-serif', background: 'transparent' },
        labels: labels,
        colors: colors,
        plotOptions: { pie: { donut: { size: '65%' } } },
        dataLabels: { enabled: true, dropShadow: { enabled: false } },
        stroke: { width: 0 },
        legend: { position: 'bottom', markers: { radius: 4 } }
    };
    const chartPie = new ApexCharts(document.getElementById('chartPie'), pieOptions);
    chartPie.render();

    // Theme listener for charts
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === "class") {
                const newIsDark = document.documentElement.classList.contains('dark');
                chartBar.updateOptions({
                    theme: { mode: newIsDark ? 'dark' : 'light' },
                    grid: { borderColor: newIsDark ? '#1b3d30' : '#f1f5f9' }
                });
                chartPie.updateOptions({
                    theme: { mode: newIsDark ? 'dark' : 'light' }
                });
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true });
</script>
@endif
@endpush  