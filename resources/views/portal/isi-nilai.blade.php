@extends('layouts.portal')
@section('title', 'Isi Data Ekonomi')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-on-surface">Isi Data Ekonomi</h1>
    <p class="text-text-muted text-sm mt-1">
        Pilih kondisi yang paling sesuai dengan keadaan keluarga Anda. Periode: {{ $periode }}
    </p>
</div>

<div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 flex items-start gap-3 mb-6">
    <span class="material-symbols-outlined text-blue-600 text-base mt-0.5">info</span>
    <p class="text-sm text-blue-800">
        Jawab berdasarkan kondisi <strong>saat ini</strong>, bukan kondisi yang diharapkan.
        Data ini bersifat rahasia dan hanya digunakan untuk penentuan golongan UKT.
    </p>
</div>

<form method="POST" action="{{ route('mahasiswa.portal.simpanNilai') }}" class="space-y-5">
    @csrf
    @foreach($kriterias as $k)
    @php
        $keterangan = $k->keterangan_skala ?? [];
        $nilaiSaat  = $nilaiExisting[$k->id]->nilai ?? null;
    @endphp
    <div class="card p-5">
        <div class="mb-4">
            <h3 class="font-semibold text-on-surface">{{ $loop->iteration }}. {{ $k->nama }}</h3>
            @if($k->deskripsi)
                <p class="text-xs text-text-muted mt-0.5">{{ $k->deskripsi }}</p>
            @endif
        </div>
        <div class="space-y-2">
            @for($v = $k->skala_min; $v <= $k->skala_max; $v++)
            <label class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                {{ $nilaiSaat == $v ? 'border-primary bg-primary-light/30' : 'border-gray-100 hover:border-gray-300 hover:bg-gray-50' }}"
                id="label_{{ $k->id }}_{{ $v }}">
                <input type="radio"
                    name="nilai[{{ $k->id }}]"
                    value="{{ $v }}"
                    {{ $nilaiSaat == $v ? 'checked' : '' }}
                    required
                    onchange="highlightPilihan({{ $k->id }}, {{ $k->skala_min }}, {{ $k->skala_max }})"
                    class="mt-0.5 accent-primary shrink-0">
                <div class="flex items-start gap-3 flex-1">
                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-sm font-bold shrink-0
                        {{ $nilaiSaat == $v ? 'bg-primary text-white' : 'bg-gray-100 text-text-muted' }}"
                        id="badge_{{ $k->id }}_{{ $v }}">{{ $v }}</span>
                    <p class="text-sm {{ empty($keterangan[$v]) ? 'text-text-muted italic' : 'text-on-surface' }}">
                        {{ $keterangan[$v] ?? 'Nilai ' . $v }}
                    </p>
                </div>
            </label>
            @endfor
        </div>
        @error("nilai.{$k->id}")
            <p class="text-red-500 text-xs mt-2">Pilihan ini wajib diisi.</p>
        @enderror
    </div>
    @endforeach

    <div class="card p-5 flex justify-between items-center">
        <p class="text-xs text-text-muted">Pastikan semua pertanyaan sudah dijawab sebelum menyimpan.</p>
        <button type="submit"
            class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-primary-dark transition-colors">
            <span class="material-symbols-outlined text-base">save</span> Simpan Data
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
function highlightPilihan(kriteriaId, min, max) {
    for (let v = min; v <= max; v++) {
        const label = document.getElementById(`label_${kriteriaId}_${v}`);
        const badge = document.getElementById(`badge_${kriteriaId}_${v}`);
        const radio = label?.querySelector('input[type=radio]');
        if (!label || !badge || !radio) continue;
        if (radio.checked) {
            label.classList.replace('border-gray-100', 'border-primary');
            label.classList.add('bg-primary-light/30');
            badge.classList.replace('bg-gray-100', 'bg-primary');
            badge.classList.replace('text-text-muted', 'text-white');
        } else {
            label.classList.replace('border-primary', 'border-gray-100');
            label.classList.remove('bg-primary-light/30');
            badge.classList.replace('bg-primary', 'bg-gray-100');
            badge.classList.replace('text-white', 'text-text-muted');
        }
    }
}
</script>
@endpush