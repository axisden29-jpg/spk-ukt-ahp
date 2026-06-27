@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <span class="text-on-surface font-medium">Mahasiswa</span>
@endsection

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-display font-bold text-on-surface">Data Mahasiswa</h1>
        <p class="text-text-muted text-sm mt-1">Kelola data mahasiswa untuk periode {{ $periode }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('mahasiswa.template') }}"
            class="border border-gray-300 text-text-muted px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2 hover:bg-gray-50 transition-colors">
            <span class="material-symbols-outlined text-base">download</span> Template
        </a>
        <button onclick="document.getElementById('modalImport').classList.remove('hidden')"
            class="border border-primary text-primary px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2 hover:bg-primary-light transition-colors">
            <span class="material-symbols-outlined text-base">upload_file</span> Import
        </button>
        <a href="{{ route('mahasiswa.status') }}"
            class="border border-yellow-400 text-yellow-700 px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2 hover:bg-yellow-50 transition-colors">
            <span class="material-symbols-outlined text-base">analytics</span> Status
        </a>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
            class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2 hover:bg-primary-dark transition-colors">
            <span class="material-symbols-outlined text-base">person_add</span> Tambah
        </button>
    </div>
</div>

{{-- Toolbar Pencarian & Filter --}}
<form id="filterForm" method="GET" action="{{ route('mahasiswa.index') }}" class="card p-4 mb-4">
    <div class="flex flex-wrap gap-3 items-center">

        {{-- Search --}}
        <div class="relative flex-1 min-w-[200px]">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base">search</span>
            <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                placeholder="Cari nama, NIM, atau program studi..."
                onkeypress="if(event.keyCode==13) { this.form.submit(); }"
                class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
        </div>

        {{-- Filter Status Nilai --}}
        <select name="filter_nilai" id="filterNilai" onchange="this.form.submit()"
            class="border border-gray-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary bg-white dark:bg-slate-800 dark:text-white transition-colors">
            <option value="">Semua Status Nilai</option>
            <option value="lengkap" {{ request('filter_nilai') == 'lengkap' ? 'selected' : '' }}>✓ Nilai Lengkap</option>
            <option value="sebagian" {{ request('filter_nilai') == 'sebagian' ? 'selected' : '' }}>⚡ Sebagian</option>
            <option value="belum" {{ request('filter_nilai') == 'belum' ? 'selected' : '' }}>✗ Belum Ada Nilai</option>
        </select>

        {{-- Sort --}}
        <select name="sort_by" id="sortBy" onchange="document.getElementById('sortField').value=''; document.getElementById('dirField').value=''; this.form.submit()"
            class="border border-gray-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary bg-white dark:bg-slate-800 dark:text-white transition-colors">
            <option value="">Urutkan...</option>
            <option value="nama-asc" {{ request('sort_by') == 'nama-asc' || (request('sort') == 'nama' && request('dir') == 'asc') ? 'selected' : '' }}>Nama A–Z</option>
            <option value="nama-desc" {{ request('sort_by') == 'nama-desc' || (request('sort') == 'nama' && request('dir') == 'desc') ? 'selected' : '' }}>Nama Z–A</option>
            <option value="nim-asc" {{ request('sort_by') == 'nim-asc' || (request('sort') == 'nim' && request('dir') == 'asc') ? 'selected' : '' }}>NIM Terkecil</option>
            <option value="nim-desc" {{ request('sort_by') == 'nim-desc' || (request('sort') == 'nim' && request('dir') == 'desc') ? 'selected' : '' }}>NIM Terbesar</option>
            <option value="angkatan-desc" {{ request('sort_by') == 'angkatan-desc' || (request('sort') == 'angkatan' && request('dir') == 'desc') ? 'selected' : '' }}>Angkatan Terbaru</option>
            <option value="angkatan-asc" {{ request('sort_by') == 'angkatan-asc' || (request('sort') == 'angkatan' && request('dir') == 'asc') ? 'selected' : '' }}>Angkatan Terlama</option>
            <option value="nilai-desc" {{ request('sort_by') == 'nilai-desc' || (request('sort') == 'nilai' && request('dir') == 'desc') ? 'selected' : '' }}>Nilai Terlengkap</option>
            <option value="nilai-asc" {{ request('sort_by') == 'nilai-asc' || (request('sort') == 'nilai' && request('dir') == 'asc') ? 'selected' : '' }}>Nilai Tersedikit</option>
        </select>

        <input type="hidden" name="sort" id="sortField" value="{{ request('sort') }}">
        <input type="hidden" name="dir" id="dirField" value="{{ request('dir') }}">

        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-primary-dark transition-colors">
            Cari
        </button>

        {{-- Reset --}}
        <a href="{{ route('mahasiswa.index') }}"
            class="border border-gray-200 text-text-muted px-3 py-2 rounded-xl text-sm hover:bg-gray-50 flex items-center gap-1 transition-colors">
            <span class="material-symbols-outlined text-base">filter_alt_off</span> Reset
        </a>

        {{-- Counter --}}
        <span id="counterLabel" class="text-xs text-text-muted ml-auto">
            Menampilkan {{ $mahasiswas->total() }} mahasiswa
        </span>
    </div>
</form>

{{-- Tabel --}}
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="tableMahasiswa">
            <thead>
                <tr>
                    <th class="table-header w-12">No</th>
                    <th class="table-header">
                        <button onclick="toggleSort('nim')" class="flex items-center gap-1 hover:text-primary transition-colors">
                            NIM <span class="material-symbols-outlined text-sm" id="sort-nim">unfold_more</span>
                        </button>
                    </th>
                    <th class="table-header">
                        <button onclick="toggleSort('nama')" class="flex items-center gap-1 hover:text-primary transition-colors">
                            Nama <span class="material-symbols-outlined text-sm" id="sort-nama">unfold_more</span>
                        </button>
                    </th>
                    <th class="table-header">
                        <button onclick="toggleSort('prodi')" class="flex items-center gap-1 hover:text-primary transition-colors">
                            Program Studi <span class="material-symbols-outlined text-sm" id="sort-prodi">unfold_more</span>
                        </button>
                    </th>
                    <th class="table-header text-center">
                        <button onclick="toggleSort('angkatan')" class="flex items-center justify-center gap-1 hover:text-primary transition-colors">
                            Angkatan <span class="material-symbols-outlined text-sm" id="sort-angkatan">unfold_more</span>
                        </button>
                    </th>
                    <th class="table-header">
                        <button onclick="toggleSort('nilai')" class="flex items-center gap-1 hover:text-primary transition-colors">
                            Nilai <span class="material-symbols-outlined text-sm" id="sort-nilai">unfold_more</span>
                        </button>
                    </th>
                    <th class="table-header">Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($mahasiswas as $i => $m)
                <tr class="table-row row-data"
                    data-nama="{{ strtolower($m->nama) }}"
                    data-nim="{{ strtolower($m->nim) }}"
                    data-prodi="{{ strtolower($m->program_studi ?? '') }}"
                    data-angkatan="{{ $m->angkatan ?? 0 }}"
                    data-nilai="{{ $m->nilai_count }}"
                    data-status="{{ $m->nilai_count >= $totalKriteria && $totalKriteria > 0 ? 'lengkap' : ($m->nilai_count > 0 ? 'sebagian' : 'belum') }}">

                    <td class="table-cell" data-no="{{ $mahasiswas->firstItem() + $i }}">
                        {{ $mahasiswas->firstItem() + $i }}
                    </td>
                    <td class="table-cell font-mono">{{ $m->nim }}</td>
                    <td class="table-cell font-medium">{{ $m->nama }}</td>
                    <td class="table-cell">{{ $m->program_studi ?? '-' }}</td>
                    <td class="table-cell text-center">{{ $m->angkatan ?? '-' }}</td>
                    <td class="table-cell">
                        @if($m->nilai_count >= $totalKriteria && $totalKriteria > 0)
                            <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1 w-max">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Lengkap
                            </span>
                        @elseif($m->nilai_count > 0)
                            <div class="flex items-center gap-2 w-32">
                                <div class="flex-1 bg-gray-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-yellow-400 h-full rounded-full"
                                        style="width:{{ $totalKriteria > 0 ? round($m->nilai_count/$totalKriteria*100) : 0 }}%"></div>
                                </div>
                                <span class="text-xs text-yellow-700 dark:text-yellow-500 font-medium whitespace-nowrap">{{ $m->nilai_count }}/{{ $totalKriteria }}</span>
                            </div>
                        @else
                            <span class="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1 w-max">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Belum Ada
                            </span>
                        @endif
                    </td>
                    <td class="table-cell">
                        <div class="flex items-center gap-1 opacity-60 hover:opacity-100 transition-opacity">
                            <a href="{{ route('mahasiswa.nilai', $m) }}"
                                class="p-1.5 rounded-lg text-primary hover:bg-primary/10" title="Input Nilai">
                                <span class="material-symbols-outlined text-base block">edit_note</span>
                            </a>
                            <button onclick="openEditMhs({{ $m->id }}, '{{ addslashes($m->nim) }}', '{{ addslashes($m->nama) }}', '{{ addslashes($m->program_studi) }}', '{{ $m->angkatan }}')"
                                class="p-1.5 rounded-lg text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30" title="Edit">
                                <span class="material-symbols-outlined text-base block">edit</span>
                            </button>
                            <button type="button"
                                onclick="konfirmasiReset({{ $m->id }}, '{{ addslashes($m->nama) }}', '{{ addslashes($m->nim) }}')"
                                class="p-1.5 rounded-lg text-yellow-600 dark:text-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/30" title="Reset Password">
                                <span class="material-symbols-outlined text-base block">lock_reset</span>
                            </button>
                            <button type="button"
                                onclick="konfirmasiHapus({{ $m->id }}, '{{ addslashes($m->nama) }}')"
                                class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30" title="Hapus">
                                <span class="material-symbols-outlined text-base block">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-48 h-48 text-gray-200 dark:text-slate-700 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="text-lg font-display font-semibold text-on-surface dark:text-white mb-1">Tidak Ada Data Mahasiswa</h3>
                            <p class="text-text-muted dark:text-gray-400 text-sm max-w-sm mb-6">Belum ada data mahasiswa atau tidak ada hasil yang cocok dengan kata kunci pencarian Anda.</p>
                            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                                class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-medium hover:bg-primary-dark transition-all duration-300 shadow-lg shadow-primary/30 hover:-translate-y-0.5">
                                Tambah Mahasiswa Baru
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($mahasiswas->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 dark:border-slate-700 bg-gray-50/30 dark:bg-slate-800/50">
        {{ $mahasiswas->links() }}
    </div>
    @endif
</div>

{{-- Form Hapus & Reset (tersembunyi) --}}
<form id="formHapus" method="POST" class="hidden">@csrf @method('DELETE')</form>
<form id="formReset" method="POST" class="hidden">@csrf</form>

{{-- Modal Tambah --}}
<div id="modalTambah" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-md shadow-2xl border border-white/20 dark:border-slate-700 transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between bg-gray-50/50 dark:bg-slate-800/50 rounded-t-2xl">
            <h3 class="font-display font-bold text-on-surface dark:text-white">Tambah Mahasiswa</h3>
            <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <span class="material-symbols-outlined block">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('mahasiswa.store') }}" class="px-6 py-5 space-y-5">
            @csrf
            <div class="floating-input-group">
                <input type="text" name="nim" id="addNim" required placeholder=" " class="floating-input">
                <label for="addNim" class="floating-label">NIM <span class="text-red-500">*</span></label>
            </div>
            <div class="floating-input-group">
                <input type="text" name="nama" id="addNama" required placeholder=" " class="floating-input">
                <label for="addNama" class="floating-label">Nama Lengkap <span class="text-red-500">*</span></label>
            </div>
            <div class="floating-input-group">
                <input type="text" name="program_studi" id="addProdi" placeholder=" " class="floating-input">
                <label for="addProdi" class="floating-label">Program Studi</label>
            </div>
            <div class="floating-input-group">
                <input type="number" name="angkatan" id="addAngkatan" placeholder=" " class="floating-input">
                <label for="addAngkatan" class="floating-label">Angkatan</label>
            </div>
            <div class="bg-primary/10 border border-primary/20 rounded-xl px-4 py-3 text-xs text-primary flex gap-2">
                <span class="material-symbols-outlined text-base shrink-0">info</span>
                <p>Akun login otomatis dibuat. Password default = NIM mahasiswa.</p>
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
<div id="modalEditMhs" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-md shadow-2xl border border-white/20 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between bg-gray-50/50 dark:bg-slate-800/50 rounded-t-2xl">
            <h3 class="font-display font-bold text-on-surface dark:text-white">Edit Mahasiswa</h3>
            <button onclick="document.getElementById('modalEditMhs').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <span class="material-symbols-outlined block">close</span>
            </button>
        </div>
        <form method="POST" id="formEditMhs" class="px-6 py-5 space-y-5">
            @csrf @method('PUT')
            <div class="floating-input-group">
                <input type="text" name="nim" id="editMhsNim" required placeholder=" " class="floating-input">
                <label for="editMhsNim" class="floating-label">NIM <span class="text-red-500">*</span></label>
            </div>
            <div class="floating-input-group">
                <input type="text" name="nama" id="editMhsNama" required placeholder=" " class="floating-input">
                <label for="editMhsNama" class="floating-label">Nama Lengkap <span class="text-red-500">*</span></label>
            </div>
            <div class="floating-input-group">
                <input type="text" name="program_studi" id="editMhsProdi" placeholder=" " class="floating-input">
                <label for="editMhsProdi" class="floating-label">Program Studi</label>
            </div>
            <div class="floating-input-group">
                <input type="number" name="angkatan" id="editMhsAngkatan" placeholder=" " class="floating-input">
                <label for="editMhsAngkatan" class="floating-label">Angkatan</label>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalEditMhs').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-text-muted hover:bg-gray-100 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-primary text-white rounded-xl hover:bg-primary-dark">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Import --}}
<div id="modalImport" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-md shadow-2xl border border-white/20 dark:border-slate-700 transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between bg-gray-50/50 dark:bg-slate-800/50 rounded-t-2xl">
            <h3 class="font-display font-bold text-on-surface dark:text-white">Import Data Mahasiswa</h3>
            <button onclick="document.getElementById('modalImport').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <span class="material-symbols-outlined block">close</span>
            </button>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800/50 rounded-xl px-4 py-3 text-sm text-blue-800 dark:text-blue-300">
                <p class="font-semibold mb-1">Format: .xlsx, .xls</p>
                <p>Kolom: <code class="bg-blue-100 dark:bg-blue-800/50 px-1 rounded">nim</code>, <code class="bg-blue-100 dark:bg-blue-800/50 px-1 rounded">nama</code>, <code class="bg-blue-100 dark:bg-blue-800/50 px-1 rounded">program_studi</code>, <code class="bg-blue-100 dark:bg-blue-800/50 px-1 rounded">angkatan</code></p>
                <p class="mt-1 text-xs opacity-80">NIM duplikat akan dilewati otomatis.</p>
            </div>
            <a href="{{ route('mahasiswa.template') }}"
                class="flex items-center gap-2 text-sm text-primary font-medium hover:underline">
                <span class="material-symbols-outlined text-base">download</span> Download Template Excel
            </a>
            <form method="POST" action="{{ route('mahasiswa.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-xl p-6 text-center hover:border-primary dark:hover:border-primary/50 transition-colors cursor-pointer"
                    onclick="document.getElementById('fileImport').click()">
                    <span class="material-symbols-outlined text-3xl text-gray-400 dark:text-slate-500 block mb-2">upload_file</span>
                    <p class="text-sm text-text-muted dark:text-gray-300">Klik untuk pilih file</p>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">.xlsx, .xls — maks 2MB</p>
                    <p id="namaFile" class="text-sm text-primary font-medium mt-2 hidden"></p>
                </div>
                <input type="file" id="fileImport" name="file" accept=".xlsx,.xls" class="hidden"
                    onchange="tampilkanNamaFile(this)">
                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-text-muted hover:bg-gray-100 dark:hover:bg-slate-700/50 rounded-xl">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 text-sm bg-primary text-white rounded-xl hover:bg-primary-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">upload</span> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── SORT STATE ──
let sortCol = '{{ request('sort') }}';
let sortDir = '{{ request('dir') }}';
@if(request('sort_by') && !request('sort'))
    const sortBy = '{{ request('sort_by') }}'.split('-');
    if(sortBy.length === 2) {
        sortCol = sortBy[0];
        sortDir = sortBy[1];
    }
@endif

let sortState = { col: sortCol || null, dir: sortDir || 'asc' };

function toggleSort(col) {
    if (sortState.col === col) {
        sortState.dir = sortState.dir === 'asc' ? 'desc' : 'asc';
    } else {
        sortState.col = col;
        sortState.dir = 'asc';
    }
    
    document.getElementById('sortField').value = sortState.col;
    document.getElementById('dirField').value = sortState.dir;
    // Reset select sort_by
    document.getElementById('sortBy').value = '';
    
    document.getElementById('filterForm').submit();
}

function updateSortIcons() {
    ['nim','nama','prodi','angkatan','nilai'].forEach(col => {
        const el = document.getElementById('sort-' + col);
        if (!el) return;
        if (sortState.col === col) {
            el.textContent = sortState.dir === 'asc' ? 'arrow_upward' : 'arrow_downward';
            el.classList.add('text-primary');
        } else {
            el.textContent = 'unfold_more';
            el.classList.remove('text-primary');
        }
    });
}

document.addEventListener('DOMContentLoaded', updateSortIcons);

// ── MODAL HELPERS ──
function openEditMhs(id, nim, nama, prodi, angkatan) {
    document.getElementById('formEditMhs').action = '/mahasiswa/' + id;
    document.getElementById('editMhsNim').value      = nim;
    document.getElementById('editMhsNama').value     = nama;
    document.getElementById('editMhsProdi').value    = prodi;
    document.getElementById('editMhsAngkatan').value = angkatan;
    document.getElementById('modalEditMhs').classList.remove('hidden');
}

function konfirmasiHapus(id, nama) {
    window.dispatchEvent(new CustomEvent('open-confirm', {
        detail: {
            title: 'Hapus Mahasiswa',
            message: `Hapus mahasiswa "${nama}"? Data nilai dan akun login juga akan terhapus.`,
            type: 'danger',
            confirmText: 'Hapus Data',
            onConfirm: () => {
                const form = document.getElementById('formHapus');
                form.action = `/mahasiswa/${id}`;
                form.submit();
            }
        }
    }));
}

function konfirmasiReset(id, nama, nim) {
    window.dispatchEvent(new CustomEvent('open-confirm', {
        detail: {
            title: 'Reset Password',
            message: `Reset password "${nama}" ke NIM (${nim})?`,
            type: 'warning',
            confirmText: 'Reset Password',
            onConfirm: () => {
                const form = document.getElementById('formReset');
                form.action = `/mahasiswa/${id}/reset-password`;
                form.submit();
            }
        }
    }));
}

function tampilkanNamaFile(input) {
    const label = document.getElementById('namaFile');
    if (input.files && input.files[0]) {
        label.textContent = '✓ ' + input.files[0].name;
        label.classList.remove('hidden');
    }
}
</script>
@endpush