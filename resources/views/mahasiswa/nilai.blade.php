@extends('layouts.app')

@section('title', 'Input Nilai Kriteria')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <a href="{{ route('mahasiswa.index') }}" class="hover:text-primary">Mahasiswa</a>
    <span class="text-gray-400">/</span>
    <span class="text-on-surface font-medium">Input Nilai</span>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-on-surface">Input Nilai Kriteria</h1>
    <div class="flex items-center gap-2 mt-2">
        <span class="material-symbols-outlined text-primary text-base">person</span>
        <p class="text-text-muted text-sm">
            <strong class="text-on-surface">{{ $mahasiswa->nama }}</strong>
            — {{ $mahasiswa->nim }} | Periode: {{ $periode }}
        </p>
    </div>
</div>

<div class="card p-6 max-w-2xl">
    <form method="POST" action="{{ route('mahasiswa.simpanNilai', $mahasiswa) }}" class="space-y-6">
        @csrf
        @foreach($kriterias as $k)
        @php
            $keterangan = $k->keterangan_skala ?? [];
            $nilaiSaat  = $nilaiExisting[$k->id]->nilai ?? null;
        @endphp
        <div class="border border-gray-100 dark:border-slate-700 rounded-xl p-4 hover:border-primary/30 dark:hover:border-primary/50 transition-colors">
            {{-- Header Kriteria --}}
            <div class="mb-3">
                <label class="block text-sm font-semibold text-on-surface">
                    {{ $k->nama }}
                    <span class="text-text-muted font-normal">(Skala {{ $k->skala_min }}–{{ $k->skala_max }})</span>
                </label>
                @if($k->deskripsi)
                    <p class="text-xs text-text-muted mt-0.5">{{ $k->deskripsi }}</p>
                @endif
            </div>

            {{-- Pilihan Nilai sebagai Radio Button --}}
            <div class="space-y-2">
                @for($v = $k->skala_min; $v <= $k->skala_max; $v++)
                <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                    {{ $nilaiSaat == $v ? 'border-primary bg-primary-light/30 dark:bg-primary-900/30' : 'border-gray-100 dark:border-slate-700 hover:border-gray-300 dark:hover:border-slate-500 hover:bg-gray-50 dark:hover:bg-slate-700' }}"
                    id="label_{{ $k->id }}_{{ $v }}">
                    <input type="radio"
                        name="nilai[{{ $k->id }}]"
                        value="{{ $v }}"
                        {{ $nilaiSaat == $v ? 'checked' : '' }}
                        required
                        onchange="highlightPilihan({{ $k->id }}, {{ $k->skala_min }}, {{ $k->skala_max }})"
                        class="sr-only">
                    <div class="flex items-center gap-3 flex-1">
                        <span class="w-7 h-7 rounded-lg flex items-center justify-center text-sm font-bold shrink-0
                            {{ $nilaiSaat == $v ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-slate-700 text-text-muted dark:text-gray-400' }}"
                            id="badge_{{ $k->id }}_{{ $v }}">
                            {{ $v }}
                        </span>
                        <div>
                            @if(!empty($keterangan[$v]))
                                <p class="text-sm font-medium text-on-surface">{{ $keterangan[$v] }}</p>
                            @else
                                <p class="text-sm text-text-muted italic">Nilai {{ $v }}</p>
                            @endif
                        </div>
                    </div>
                </label>
                @endfor
            </div>

            @error("nilai.{$k->id}")
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>
        @endforeach

        <div class="flex justify-between pt-2">
            <a href="{{ route('mahasiswa.index') }}"
                class="px-4 py-2 text-sm text-text-muted hover:bg-gray-100 dark:hover:bg-slate-700/50 rounded-xl">← Kembali</a>
            <button type="submit"
                class="bg-primary text-white px-6 py-2 rounded-xl text-sm font-medium hover:bg-primary-dark transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-base">save</span> Simpan Nilai
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function highlightPilihan(kriteriaId, min, max) {
    for (let v = min; v <= max; v++) {
        const label = document.getElementById(`label_${kriteriaId}_${v}`);
        const badge = document.getElementById(`badge_${kriteriaId}_${v}`);
        const radio = label ? label.querySelector('input[type=radio]') : null;
        if (!label || !badge || !radio) continue;

        if (radio.checked) {
            label.classList.remove('border-gray-100', 'hover:border-gray-300', 'hover:bg-gray-50', 'dark:border-slate-700', 'dark:hover:border-slate-500', 'dark:hover:bg-slate-700');
            label.classList.add('border-primary', 'bg-primary-light/30', 'dark:bg-primary-900/30');
            badge.classList.remove('bg-gray-100', 'text-text-muted', 'dark:bg-slate-700', 'dark:text-gray-400');
            badge.classList.add('bg-primary', 'text-white');
        } else {
            label.classList.remove('border-primary', 'bg-primary-light/30', 'dark:bg-primary-900/30');
            label.classList.add('border-gray-100', 'hover:border-gray-300', 'hover:bg-gray-50', 'dark:border-slate-700', 'dark:hover:border-slate-500', 'dark:hover:bg-slate-700');
            badge.classList.remove('bg-primary', 'text-white');
            badge.classList.add('bg-gray-100', 'text-text-muted', 'dark:bg-slate-700', 'dark:text-gray-400');
        }
    }
}
</script>
@endpush