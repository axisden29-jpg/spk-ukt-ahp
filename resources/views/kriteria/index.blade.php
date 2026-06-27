@extends('layouts.app')

@section('title', 'Manajemen Kriteria')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <span class="text-on-surface font-medium">Kriteria</span>
@endsection

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-display font-bold text-on-surface">Manajemen Kriteria</h1>
        <p class="text-text-muted text-sm mt-1">Kelola kriteria penilaian UKT yang digunakan dalam perhitungan AHP</p>
    </div>
    <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
        class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2 hover:bg-primary-dark transition-colors">
        <span class="material-symbols-outlined text-base">add</span> Tambah Kriteria
    </button>
</div>

{{-- Tabel Kriteria --}}
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th class="table-header">No</th>
                    <th class="table-header">Nama Kriteria</th>
                    <th class="table-header">Kode</th>
                    <th class="table-header">Skala</th>
                    <th class="table-header">Urutan</th>
                    <th class="table-header">Status</th>
                    <th class="table-header">Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($kriterias as $i => $k)
                <tr class="table-row">
                    <td class="table-cell">{{ $i + 1 }}</td>
                    <td class="table-cell">
                        <p class="font-medium">{{ $k->nama }}</p>
                        @if($k->deskripsi)
                            <p class="text-xs text-text-muted dark:text-gray-400 mt-0.5">{{ Str::limit($k->deskripsi, 60) }}</p>
                        @endif
                    </td>
                    <td class="table-cell"><code class="bg-gray-100 dark:bg-slate-700 px-2 py-0.5 rounded text-xs">{{ $k->kode }}</code></td>
                    <td class="table-cell text-text-muted dark:text-gray-400">{{ $k->skala_min }} – {{ $k->skala_max }}</td>
                    <td class="table-cell text-text-muted dark:text-gray-400">{{ $k->urutan }}</td>
                    <td class="table-cell">
                        @if($k->is_aktif)
                            <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold px-2 py-1 rounded-full">Aktif</span>
                        @else
                            <span class="bg-gray-100 dark:bg-slate-700/50 text-gray-500 dark:text-gray-400 text-xs font-semibold px-2 py-1 rounded-full">Nonaktif</span>
                        @endif
                    </td>
                    <td class="table-cell">
                        <div class="flex items-center gap-2 opacity-60 hover:opacity-100 transition-opacity">
                            {{-- Toggle --}}
                            <form method="POST" action="{{ route('kriteria.toggle', $k) }}">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $k->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}"
                                    class="p-1.5 rounded-lg {{ $k->is_aktif ? 'text-yellow-600 dark:text-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/30' : 'text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/30' }}">
                                    <span class="material-symbols-outlined text-base block">{{ $k->is_aktif ? 'toggle_on' : 'toggle_off' }}</span>
                                </button>
                            </form>
                            {{-- Edit --}}
                            <button onclick="openEdit({{ $k->id }}, '{{ addslashes($k->nama) }}', '{{ addslashes($k->deskripsi) }}', {{ $k->skala_min }}, {{ $k->skala_max }}, {{ $k->urutan }})"
                                class="p-1.5 rounded-lg text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30">
                                <span class="material-symbols-outlined text-base block">edit</span>
                            </button>
                            {{-- Hapus --}}
                            <form method="POST" action="{{ route('kriteria.destroy', $k) }}" id="formHapusKriteria{{ $k->id }}">
                                @csrf @method('DELETE')
                                <button type="button" 
                                    onclick="window.dispatchEvent(new CustomEvent('open-confirm', { detail: { title: 'Hapus Kriteria', message: 'Hapus kriteria ini? Data nilai mahasiswa terkait juga akan terhapus.', type: 'danger', confirmText: 'Hapus', onConfirm: 'formHapusKriteria{{ $k->id }}' } }))"
                                    class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30">
                                    <span class="material-symbols-outlined text-base block">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-40 h-40 text-gray-200 dark:text-slate-700 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <h3 class="text-lg font-display font-semibold text-on-surface dark:text-white mb-1">Belum Ada Kriteria</h3>
                            <p class="text-text-muted dark:text-gray-400 text-sm max-w-sm mb-6">Tambahkan kriteria penilaian untuk digunakan dalam perhitungan AHP.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Info AHP Warning --}}
<div class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700/50 rounded-xl px-4 py-3 flex items-start gap-3">
    <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-500 text-base mt-0.5">warning</span>
    <p class="text-yellow-800 dark:text-yellow-200 text-sm">Setiap perubahan kriteria (tambah, edit, nonaktifkan) mengharuskan Anda memperbarui <a href="{{ route('ahp.matriks') }}" class="font-semibold underline">Matriks Perbandingan AHP</a>.</p>
</div>

{{-- Modal Tambah --}}
<div id="modalTambah" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] flex flex-col border border-white/20 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between bg-gray-50/50 dark:bg-slate-800/50 rounded-t-2xl">
            <h3 class="font-display font-bold text-on-surface dark:text-white">Tambah Kriteria</h3>
            <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <span class="material-symbols-outlined block">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('kriteria.store') }}" class="px-6 py-5 space-y-5 overflow-y-auto">
            @csrf
            <div class="floating-input-group">
                <input type="text" name="nama" id="tambahNama" required placeholder=" " class="floating-input">
                <label for="tambahNama" class="floating-label">Nama Kriteria <span class="text-red-500">*</span></label>
            </div>
            <div class="floating-input-group">
                <textarea name="deskripsi" id="tambahDeskripsi" rows="2" placeholder=" " class="floating-input"></textarea>
                <label for="tambahDeskripsi" class="floating-label">Deskripsi</label>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="floating-input-group">
                    <input type="number" name="skala_min" id="tambahMin" value="1" min="1"
                        onchange="renderKeteranganTambah()" placeholder=" " class="floating-input">
                    <label for="tambahMin" class="floating-label">Skala Min</label>
                </div>
                <div class="floating-input-group">
                    <input type="number" name="skala_max" id="tambahMax" value="5" min="2"
                        onchange="renderKeteranganTambah()" placeholder=" " class="floating-input">
                    <label for="tambahMax" class="floating-label">Skala Max</label>
                </div>
                <div class="floating-input-group">
                    <input type="number" name="urutan" id="tambahUrutan" value="{{ $kriterias->count() + 1 }}" min="1" placeholder=" " class="floating-input">
                    <label for="tambahUrutan" class="floating-label">Urutan</label>
                </div>
            </div>

            {{-- Keterangan Skala --}}
            <div>
                <label class="block text-sm font-medium text-on-surface mb-2">
                    Keterangan Tiap Nilai Skala
                    <span class="text-xs text-text-muted font-normal ml-1">(opsional, tapi sangat dianjurkan)</span>
                </label>
                <div id="keteranganTambah" class="space-y-2">
                    {{-- Dirender oleh JS --}}
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-text-muted hover:bg-gray-100 dark:hover:bg-slate-700 rounded-xl transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-primary text-white rounded-xl hover:bg-primary-dark shadow-md transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEdit" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] flex flex-col border border-white/20 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between bg-gray-50/50 dark:bg-slate-800/50 rounded-t-2xl">
            <h3 class="font-display font-bold text-on-surface dark:text-white">Edit Kriteria</h3>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <span class="material-symbols-outlined block">close</span>
            </button>
        </div>
        <form method="POST" id="formEdit" class="px-6 py-5 space-y-5 overflow-y-auto">
            @csrf @method('PUT')
            <div class="floating-input-group">
                <input type="text" name="nama" id="editNama" required placeholder=" " class="floating-input">
                <label for="editNama" class="floating-label">Nama Kriteria <span class="text-red-500">*</span></label>
            </div>
            <div class="floating-input-group">
                <textarea name="deskripsi" id="editDeskripsi" rows="2" placeholder=" " class="floating-input"></textarea>
                <label for="editDeskripsi" class="floating-label">Deskripsi</label>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="floating-input-group">
                    <input type="number" name="skala_min" id="editMin"
                        onchange="renderKeteranganEdit()" placeholder=" " class="floating-input">
                    <label for="editMin" class="floating-label">Skala Min</label>
                </div>
                <div class="floating-input-group">
                    <input type="number" name="skala_max" id="editMax"
                        onchange="renderKeteranganEdit()" placeholder=" " class="floating-input">
                    <label for="editMax" class="floating-label">Skala Max</label>
                </div>
                <div class="floating-input-group">
                    <input type="number" name="urutan" id="editUrutan" placeholder=" " class="floating-input">
                    <label for="editUrutan" class="floating-label">Urutan</label>
                </div>
            </div>

            {{-- Keterangan Skala --}}
            <div>
                <label class="block text-sm font-medium text-on-surface mb-2">
                    Keterangan Tiap Nilai Skala
                    <span class="text-xs text-text-muted font-normal ml-1">(opsional, tapi sangat dianjurkan)</span>
                </label>
                <div id="keteranganEdit" class="space-y-2">
                    {{-- Dirender oleh JS --}}
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-text-muted hover:bg-gray-100 dark:hover:bg-slate-700 rounded-xl transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-primary text-white rounded-xl hover:bg-primary-dark shadow-md transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Data keterangan existing dari DB (untuk modal edit)
const keteranganDb = @json($kriterias->mapWithKeys(fn($k) => [
    $k->id => $k->keterangan_skala ?? []
]));

// Render input keterangan skala untuk modal Tambah
function renderKeteranganTambah() {
    const min = parseInt(document.getElementById('tambahMin').value) || 1;
    const max = parseInt(document.getElementById('tambahMax').value) || 5;
    const container = document.getElementById('keteranganTambah');
    container.innerHTML = '';
    if (max < min) return;
    for (let v = min; v <= max; v++) {
        container.innerHTML += `
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center text-sm font-bold shrink-0 shadow-sm">${v}</span>
                <input type="text" name="keterangan_skala[${v}]"
                    placeholder="Deskripsi untuk nilai ${v}..."
                    class="flex-1 bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>`;
    }
}

// Render input keterangan skala untuk modal Edit
function renderKeteranganEdit(existingData = {}) {
    const min = parseInt(document.getElementById('editMin').value) || 1;
    const max = parseInt(document.getElementById('editMax').value) || 5;
    const container = document.getElementById('keteranganEdit');
    container.innerHTML = '';
    if (max < min) return;
    for (let v = min; v <= max; v++) {
        const val = existingData[v] || '';
        container.innerHTML += `
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center text-sm font-bold shrink-0 shadow-sm">${v}</span>
                <input type="text" name="keterangan_skala[${v}]"
                    value="${val}"
                    placeholder="Deskripsi untuk nilai ${v}..."
                    class="flex-1 bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>`;
    }
}

function openEdit(id, nama, deskripsi, min, max, urutan) {
    document.getElementById('formEdit').action = '/kriteria/' + id;
    document.getElementById('editNama').value     = nama;
    document.getElementById('editDeskripsi').value = deskripsi;
    document.getElementById('editMin').value      = min;
    document.getElementById('editMax').value      = max;
    document.getElementById('editUrutan').value   = urutan;

    // Render keterangan dengan data existing dari DB
    const existing = keteranganDb[id] || {};
    renderKeteranganEdit(existing);

    document.getElementById('modalEdit').classList.remove('hidden');
}

// Init modal tambah saat dibuka
document.querySelector('button[onclick*="modalTambah"]').addEventListener('click', () => {
    renderKeteranganTambah();
});
</script>
@endpush