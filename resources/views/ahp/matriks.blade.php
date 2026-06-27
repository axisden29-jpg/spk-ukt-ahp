@extends('layouts.app')

@section('title', 'Matriks Perbandingan AHP')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <a href="{{ route('ahp.matriks') }}" class="text-on-surface font-medium">Matriks AHP</a>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-on-surface">Matriks Perbandingan Berpasangan</h1>
    <p class="text-text-muted text-sm mt-1">
        Isi nilai perbandingan pada <strong>segitiga atas</strong> — seberapa penting <em>baris</em> dibanding <em>kolom</em>.
        Segitiga bawah terisi otomatis sebagai nilai kebalikan.
    </p>
</div>

{{-- Skala Referensi --}}
<div class="card p-4 mb-6">
    <p class="text-xs font-semibold text-text-muted uppercase tracking-wider mb-3">Referensi Skala Saaty</p>
    <div class="flex flex-wrap gap-2 text-xs">
        @foreach([1=>'Sama penting',3=>'Sedikit lebih penting',5=>'Lebih penting',7=>'Sangat lebih penting',9=>'Mutlak lebih penting'] as $val => $label)
        <span class="bg-primary-light dark:bg-primary/20 text-primary dark:text-primary-light px-2 py-1 rounded-lg font-medium">{{ $val }} = {{ $label }}</span>
        @endforeach
        <span class="bg-gray-100 dark:bg-slate-700 text-text-muted dark:text-gray-300 px-2 py-1 rounded-lg">2,4,6,8 = Nilai kompromi</span>
    </div>
    <p class="text-xs text-text-muted dark:text-gray-300 mt-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg px-3 py-2">
        💡 <strong>Cara baca:</strong> Nilai pada sel (baris i, kolom j) berarti
        "seberapa penting <em>kriteria baris</em> dibanding <em>kriteria kolom</em>".
        Contoh: Penghasilan vs Pekerjaan = <strong>3</strong> artinya
        Penghasilan <em>sedikit lebih penting</em> dari Pekerjaan.
    </p>
</div>

@if($kriterias->count() < 2)
    <div class="card p-8 text-center text-text-muted">
        <span class="material-symbols-outlined text-5xl text-gray-300 block mb-2">grid_off</span>
        <p>Minimal 2 kriteria aktif diperlukan untuk membuat matriks.</p>
        <a href="{{ route('kriteria.index') }}" class="mt-3 inline-block text-primary text-sm font-medium hover:underline">Kelola Kriteria →</a>
    </div>
@else

@php $kriteriaList = $kriterias->values(); $n = $kriteriaList->count(); @endphp

<form method="POST" action="{{ route('ahp.simpanMatriks') }}">
    @csrf
    <div class="card overflow-hidden mb-4">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse" id="matriksTable">
                <thead>
                    <tr class="bg-primary dark:bg-primary-dark">
                        <th class="px-3 py-3 text-left text-xs font-semibold text-white border border-primary-dark dark:border-slate-700 sticky left-0 bg-primary dark:bg-primary-dark z-10 min-w-[160px]">
                            Baris / Kolom
                        </th>
                        @foreach($kriteriaList as $idx => $k)
                        <th class="px-3 py-3 text-center text-xs font-semibold text-white border border-primary-dark dark:border-slate-700 min-w-[110px]">
                            <span class="block">K{{ $idx + 1 }}</span>
                            <span class="block font-normal text-white/70 text-xs mt-0.5" title="{{ $k->nama }}">
                                {{ Str::limit($k->nama, 14) }}
                            </span>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($kriteriaList as $i => $baris)
                    <tr class="{{ $i % 2 === 0 ? 'bg-white dark:bg-slate-800' : 'bg-gray-50/60 dark:bg-slate-800/80' }}">

                        <td class="px-3 py-2 font-semibold text-on-surface dark:text-white border border-gray-200 dark:border-slate-700 sticky left-0 bg-inherit z-10 text-xs">
                            K{{ $i + 1 }}. {{ Str::limit($baris->nama, 22) }}
                        </td>

                        @foreach($kriteriaList as $j => $kolom)
                        <td class="border border-gray-200 dark:border-slate-700 px-2 py-2 text-center">

                            @if($i === $j)
                                {{-- DIAGONAL --}}
                                <div class="flex items-center justify-center">
                                    <span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 font-bold w-10 h-10 rounded-lg flex items-center justify-center text-sm border border-yellow-200 dark:border-yellow-700">
                                        1
                                    </span>
                                </div>
                                <input type="hidden" name="matriks[{{ $i }}][{{ $j }}]" value="1">

                            @elseif($i < $j)
                                {{-- SEGITIGA ATAS — input user --}}
                                @php
                                    $keyUp   = $baris->id . '_' . $kolom->id;
                                    $keyDown = $kolom->id . '_' . $baris->id;
                                    $existing = 1;
                                    if ($matriksDb->has($keyUp)) {
                                        $raw = (float) $matriksDb[$keyUp]->nilai;
                                        $existing = $raw >= 1 ? (int) round($raw) : (int) round(1 / $raw);
                                    } elseif ($matriksDb->has($keyDown)) {
                                        $raw = (float) $matriksDb[$keyDown]->nilai;
                                        $existing = $raw <= 1 ? (int) round(1 / $raw) : (int) round($raw);
                                    }
                                @endphp
                                <select
                                    name="matriks[{{ $i }}][{{ $j }}]"
                                    id="cell_{{ $i }}_{{ $j }}"
                                    onchange="updateOtomatis({{ $i }}, {{ $j }})"
                                    class="border border-primary/40 dark:border-primary/60 rounded-lg px-1 py-1.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary w-full bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 cursor-pointer text-center">
                                    @foreach([1,2,3,4,5,6,7,8,9] as $v)
                                        <option value="{{ $v }}" {{ $existing == $v ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>

                            @else
                                {{-- SEGITIGA BAWAH — kebalikan otomatis --}}
                                <div id="display_{{ $i }}_{{ $j }}"
                                    class="text-gray-500 dark:text-gray-400 text-xs italic bg-gray-100 dark:bg-slate-700/50 rounded-lg px-2 py-2 min-h-[36px] flex items-center justify-center font-mono border border-gray-200 dark:border-slate-600">
                                </div>
                            @endif

                        </td>
                        @endforeach
                    </tr>
                    @endforeach

                    {{-- Baris Jumlah Kolom --}}
                    <tr class="bg-primary-light/40 dark:bg-primary-dark/20">
                        <td class="px-3 py-2 font-bold text-primary dark:text-primary-light border border-gray-200 dark:border-slate-700 sticky left-0 bg-primary-light/40 dark:bg-slate-800 z-10 text-xs">
                            Jumlah Kolom
                        </td>
                        @foreach($kriteriaList as $j => $kolom)
                        <td class="border border-gray-200 dark:border-slate-700 px-2 py-2 text-center">
                            <div id="sum_{{ $j }}" class="font-mono font-bold text-primary dark:text-primary-light text-xs">—</div>
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Keterangan Warna --}}
    <div class="flex flex-wrap gap-4 text-xs text-text-muted dark:text-gray-400 mb-4">
        <span class="flex items-center gap-1.5">
            <span class="w-4 h-4 bg-yellow-100 dark:bg-yellow-900/50 border border-yellow-300 dark:border-yellow-700 rounded"></span>
            Diagonal (= 1, otomatis)
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-4 h-4 bg-blue-50 dark:bg-blue-900/50 border border-blue-300 dark:border-blue-700 rounded"></span>
            Segitiga atas (isi oleh Anda)
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-4 h-4 bg-gray-100 dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded"></span>
            Segitiga bawah (kebalikan otomatis)
        </span>
    </div>

    <div class="flex justify-between items-center">
        <p class="text-xs text-text-muted">Jumlah kolom dihitung otomatis saat Anda mengubah nilai.</p>
        <button type="submit"
            class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-medium flex items-center gap-2 hover:bg-primary-dark transition-colors">
            <span class="material-symbols-outlined text-base">save</span> Simpan & Lihat Bobot
        </button>
    </div>
</form>
@endif
@endsection

@push('scripts')
<script>
const N = {{ $n }};

function getNilai(i, j) {
    if (i === j) return 1;
    if (i < j) {
        // Segitiga atas — dari select
        const el = document.getElementById(`cell_${i}_${j}`);
        return el ? parseFloat(el.value) : 1;
    } else {
        // Segitiga bawah — kebalikan dari segitiga atas
        const el = document.getElementById(`cell_${j}_${i}`);
        return el ? 1 / parseFloat(el.value) : 1;
    }
}

function updateOtomatis(i, j) {
    // i < j selalu (segitiga atas yang diubah)
    const val = parseFloat(document.getElementById(`cell_${i}_${j}`).value);

    // Update tampilan segitiga bawah [j][i] = 1/val
    const display = document.getElementById(`display_${j}_${i}`);
    if (display) {
        display.textContent = val === 1 ? '1' : `1/${val}`;
    }

    updateSemuaJumlah();
}

function updateSemuaJumlah() {
    for (let j = 0; j < N; j++) {
        let sum = 0;
        for (let i = 0; i < N; i++) {
            sum += getNilai(i, j);
        }
        const el = document.getElementById(`sum_${j}`);
        if (el) el.textContent = sum.toFixed(4);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Isi segitiga bawah dari nilai segitiga atas yang sudah ada di DB
    for (let i = 0; i < N; i++) {
        for (let j = i + 1; j < N; j++) {
            // [i][j] = segitiga atas (ada select)
            // [j][i] = segitiga bawah (display)
            const el = document.getElementById(`cell_${i}_${j}`);
            const val = el ? parseFloat(el.value) : 1;
            const display = document.getElementById(`display_${j}_${i}`);
            if (display) {
                display.textContent = val === 1 ? '1' : `1/${val}`;
            }
        }
    }
    updateSemuaJumlah();
});
</script>
@endpush