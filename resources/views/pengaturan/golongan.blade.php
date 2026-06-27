@extends('layouts.app')

@section('title', 'Pengaturan Golongan')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <span class="text-on-surface font-medium">Pengaturan</span>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-on-surface">Pengaturan Sistem</h1>
    <p class="text-text-muted text-sm mt-1">Konfigurasi periode aktif dan metode pembagian golongan UKT</p>
</div>

<form method="POST" action="{{ route('pengaturan.simpan') }}" class="space-y-6 max-w-2xl">
    @csrf

    {{-- Periode Aktif --}}
    <div class="card p-6">
        <h2 class="font-display font-semibold text-on-surface mb-4">Periode Akademik Aktif</h2>
        <div>
            <label class="block text-sm font-medium text-on-surface mb-1">Periode <span class="text-red-500">*</span></label>
            <input type="text" name="periode_aktif" value="{{ $periodeAktif }}" required
                placeholder="contoh: 2024/2025"
                class="w-full border border-gray-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary max-w-xs">
            <p class="text-xs text-text-muted mt-1">Format: YYYY/YYYY, misal 2024/2025</p>
        </div>
    </div>

    {{-- Metode Golongan --}}
    <div class="card p-6">
        <h2 class="font-display font-semibold text-on-surface mb-4">Metode Pembagian Golongan UKT</h2>
        <div class="space-y-3">
            <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors
                {{ $metode === 'persentil' ? 'border-primary bg-primary-light/30 dark:bg-primary/20' : 'border-gray-200 dark:border-slate-700 dark:bg-slate-800/50 hover:border-gray-300 dark:hover:border-slate-600' }}">
                <input type="radio" name="metode_golongan" value="persentil" {{ $metode === 'persentil' ? 'checked' : '' }} class="mt-0.5">
                <div>
                    <p class="font-semibold text-on-surface text-sm">Metode Persentil</p>
                    <p class="text-xs text-text-muted mt-0.5">Mahasiswa dibagi proporsional ke 5 golongan berdasarkan urutan skor. Golongan 1 = 20% skor terendah, dst.</p>
                </div>
            </label>
            <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors
                {{ $metode === 'ambang_batas' ? 'border-primary bg-primary-light/30 dark:bg-primary/20' : 'border-gray-200 dark:border-slate-700 dark:bg-slate-800/50 hover:border-gray-300 dark:hover:border-slate-600' }}">
                <input type="radio" name="metode_golongan" value="ambang_batas" {{ $metode === 'ambang_batas' ? 'checked' : '' }} class="mt-0.5">
                <div>
                    <p class="font-semibold text-on-surface text-sm">Metode Ambang Batas</p>
                    <p class="text-xs text-text-muted mt-0.5">Admin menetapkan batas skor untuk setiap golongan secara manual.</p>
                </div>
            </label>
        </div>

        {{-- Ambang Batas Detail --}}
        <div id="ambangBatasForm" class="{{ $metode === 'ambang_batas' ? '' : 'hidden' }} mt-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-gray-50 dark:bg-slate-800/80">
                        <tr>
                            <th class="border border-gray-200 dark:border-slate-700 px-4 py-2 text-left">Golongan</th>
                            <th class="border border-gray-200 dark:border-slate-700 px-4 py-2 text-center">Skor Min</th>
                            <th class="border border-gray-200 dark:border-slate-700 px-4 py-2 text-center">Skor Max</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach([1=>'Sangat Tidak Mampu',2=>'Tidak Mampu',3=>'Menengah ke Bawah',4=>'Menengah',5=>'Mampu'] as $g => $label)
                        <tr class="dark:bg-slate-800/50">
                            <td class="border border-gray-200 dark:border-slate-700 px-4 py-2 font-medium">UKT {{ $g }} — {{ $label }}</td>
                            <td class="border border-gray-200 dark:border-slate-700 px-4 py-2 text-center">
                                <input type="number" name="ambang[{{ $g }}][min]" step="0.01" min="0" max="1"
                                    value="{{ $ambangBatas[$g]['min'] ?? 0 }}"
                                    class="w-20 border border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-2 py-1 text-center text-xs focus:outline-none focus:ring-2 focus:ring-primary/30">
                            </td>
                            <td class="border border-gray-200 dark:border-slate-700 px-4 py-2 text-center">
                                <input type="number" name="ambang[{{ $g }}][max]" step="0.01" min="0" max="1"
                                    value="{{ $ambangBatas[$g]['max'] ?? 0 }}"
                                    class="w-20 border border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-2 py-1 text-center text-xs focus:outline-none focus:ring-2 focus:ring-primary/30">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Nominal UKT --}}
    @php $nominalUkt = json_decode(\App\Models\Pengaturan::get('nominal_ukt', '{}'), true); @endphp
    <div class="card p-6">
        <h2 class="font-display font-semibold text-on-surface mb-1">Nominal Rupiah per Golongan UKT</h2>
        <p class="text-xs text-text-muted mb-4">Sesuaikan dengan kebijakan kampus Anda.</p>
        <div class="space-y-3">
            @foreach([1=>'Sangat Tidak Mampu',2=>'Tidak Mampu',3=>'Menengah ke Bawah',4=>'Menengah',5=>'Mampu'] as $g => $labelG)
            @php $nom = $nominalUkt[$g] ?? ['label'=>'UKT '.$g,'nominal'=>0]; @endphp
            <div class="flex items-center gap-3 bg-gray-50 dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700 p-3 rounded-xl transition-colors">
                <span class="w-16 text-sm font-bold text-on-surface shrink-0">UKT {{ $g }}</span>
                <input type="text" name="nominal_label[{{ $g }}]"
                    value="{{ $nom['label'] }}"
                    placeholder="Label (contoh: UKT 1)"
                    class="w-32 border border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                <span class="text-text-muted text-sm shrink-0">Rp</span>
                <input type="number" name="nominal_ukt[{{ $g }}]"
                    value="{{ $nom['nominal'] }}"
                    placeholder="0"
                    min="0"
                    step="50000"
                    class="flex-1 border border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                <span class="text-xs text-text-muted shrink-0 w-32 text-right">{{ $labelG }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-primary-dark transition-colors">
        <span class="material-symbols-outlined text-base">save</span> Simpan Pengaturan
    </button>
</form>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('input[name="metode_golongan"]').forEach(r => {
        r.addEventListener('change', () => {
            const form = document.getElementById('ambangBatasForm');
            form.classList.toggle('hidden', r.value !== 'ambang_batas');
        });
    });
</script>
@endpush