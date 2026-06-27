@extends('layouts.app')

@section('title', 'Status Pengisian Nilai')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <a href="{{ route('mahasiswa.index') }}" class="hover:text-primary">Mahasiswa</a>
    <span class="text-gray-400">/</span>
    <span class="text-on-surface font-medium">Status Pengisian</span>
@endsection

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-display font-bold text-on-surface">Status Pengisian Nilai</h1>
        <p class="text-text-muted text-sm mt-1">Pantau siapa saja yang sudah dan belum mengisi data — Periode: {{ $periode }}</p>
    </div>
    <a href="{{ route('mahasiswa.index') }}"
        class="border border-gray-300 text-text-muted px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2 hover:bg-gray-50">
        <span class="material-symbols-outlined text-base">arrow_back</span> Kembali
    </a>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="card p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
        </div>
        <div>
            <p class="text-text-muted text-xs">Data Lengkap</p>
            <p class="text-2xl font-bold text-on-surface">{{ $sudahLengkap }}</p>
            <p class="text-xs text-green-600 font-medium">mahasiswa</p>
        </div>
    </div>
    <div class="card p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-red-500 text-2xl">pending</span>
        </div>
        <div>
            <p class="text-text-muted text-xs">Belum Lengkap</p>
            <p class="text-2xl font-bold text-on-surface">{{ $belumLengkap }}</p>
            <p class="text-xs text-red-500 font-medium">mahasiswa</p>
        </div>
    </div>
    <div class="card p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-primary-light rounded-xl flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-primary text-2xl">leaderboard</span>
        </div>
        <div>
            <p class="text-text-muted text-xs">Sudah Dihitung</p>
            <p class="text-2xl font-bold text-on-surface">{{ $sudahDihitung }}</p>
            <p class="text-xs text-primary font-medium">mahasiswa</p>
        </div>
    </div>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('mahasiswa.status') }}" class="card p-4 mb-4 flex flex-wrap gap-2 items-center">
    @if($filterAktif !== 'semua')
        <input type="hidden" name="filter_status" value="{{ $filterAktif }}">
    @endif
    <!-- Filter Status -->
    <button type="submit" name="filter_status" value="semua"
        class="filter-btn px-4 py-1.5 rounded-xl text-xs font-semibold transition-colors {{ $filterAktif === 'semua' ? 'bg-primary text-white' : 'bg-gray-100 text-text-muted hover:bg-gray-200' }}">
        Semua ({{ $sudahLengkap + $belumLengkap }})
    </button>
    <button type="submit" name="filter_status" value="lengkap"
        class="filter-btn px-4 py-1.5 rounded-xl text-xs font-semibold transition-colors {{ $filterAktif === 'lengkap' ? 'bg-green-600 text-white' : 'bg-gray-100 text-text-muted hover:bg-green-100 hover:text-green-700' }}">
        ✓ Lengkap ({{ $sudahLengkap }})
    </button>
    <button type="submit" name="filter_status" value="belum"
        class="filter-btn px-4 py-1.5 rounded-xl text-xs font-semibold transition-colors {{ $filterAktif === 'belum' ? 'bg-red-500 text-white' : 'bg-gray-100 text-text-muted hover:bg-red-100 hover:text-red-600' }}">
        ✗ Belum Lengkap ({{ $belumLengkap }})
    </button>
    
    <!-- Input Search -->
    <div class="ml-auto flex gap-2 items-center">
        <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Cari nama / NIM..."
            class="border border-gray-200 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-primary/30 w-48">
        <button type="submit" class="bg-primary text-white px-3 py-1.5 rounded-xl text-xs font-semibold hover:bg-primary-dark">
            Cari
        </button>
        @if(request()->has('search') || request()->has('filter_status'))
            <a href="{{ route('mahasiswa.status') }}" class="text-xs text-text-muted hover:text-red-500 underline">Reset</a>
        @endif
    </div>
</form>

{{-- Tabel --}}
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th class="table-header text-left">Mahasiswa</th>
                    <th class="table-header text-left">Program Studi</th>
                    <th class="table-header text-center">Progress</th>
                    <th class="table-header text-center">Status</th>
                    <th class="table-header text-center">Golongan</th>
                    <th class="table-header text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($mahasiswas as $item)
                @php $m = $item['mahasiswa']; $g = $item['hasil_ukt']?->golongan_ukt; @endphp
                <tr class="table-row row-data">
                    <td class="table-cell">
                        <p class="font-semibold text-on-surface text-sm">{{ $m->nama }}</p>
                        <p class="text-xs text-text-muted font-mono mt-0.5">{{ $m->nim }}</p>
                    </td>
                    <td class="table-cell text-xs text-text-muted">{{ $m->program_studi ?? '-' }}</td>
                    <td class="table-cell">
                        <div class="flex items-center gap-2 justify-center">
                            <div class="w-24 bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all {{ $item['lengkap'] ? 'bg-green-500' : ($item['persen'] > 0 ? 'bg-yellow-400' : 'bg-gray-400') }}"
                                    style="width: {{ $item['persen'] }}%"></div>
                            </div>
                            <span class="text-xs font-mono text-text-muted w-12 text-center">
                                {{ $item['terisi'] }}/{{ $item['total'] }}
                            </span>
                        </div>
                        @if(!$item['lengkap'] && $item['belum_terisi']->isNotEmpty())
                        <p class="text-xs text-red-500 dark:text-red-400 mt-1 text-center" title="{{ $item['belum_terisi']->join(', ') }}">
                            Belum: {{ Str::limit($item['belum_terisi']->join(', '), 40) }}
                        </p>
                        @endif
                    </td>
                    <td class="table-cell text-center">
                        @if($item['lengkap'])
                            <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold px-3 py-1 rounded-full">✓ Lengkap</span>
                        @elseif($item['terisi'] > 0)
                            <span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-500 text-xs font-bold px-3 py-1 rounded-full">Sebagian</span>
                        @else
                            <span class="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold px-3 py-1 rounded-full">Belum Isi</span>
                        @endif
                    </td>
                    <td class="table-cell text-center">
                        @if($item['hasil_ukt'])
                            @php $nom = \App\Models\Pengaturan::nominalUkt($g); @endphp
                            <span class="text-xs font-bold px-3 py-1 rounded-full
                                {{ $g==1?'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400':($g==2?'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400':($g==3?'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-500':($g==4?'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400':'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'))) }}">
                                {{ $nom['label'] }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400 dark:text-slate-500">—</span>
                        @endif
                    </td>
                    <td class="table-cell text-center">
                        <a href="{{ route('mahasiswa.nilai', $m) }}"
                            class="text-primary hover:bg-primary-light dark:hover:bg-primary/20 p-2 rounded-xl text-xs font-medium flex items-center gap-1 justify-center transition-colors w-max mx-auto">
                            <span class="material-symbols-outlined text-base">edit_note</span>
                            Input Nilai
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-48 h-48 text-gray-200 dark:text-slate-700 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="text-lg font-display font-semibold text-on-surface dark:text-white mb-1">Tidak Ada Data Mahasiswa</h3>
                            <p class="text-text-muted dark:text-gray-400 text-sm max-w-sm mb-6">Belum ada data mahasiswa atau tidak ada hasil yang cocok dengan kata kunci pencarian Anda.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $mahasiswas->links() }}
</div>
@endsection

@push('scripts')
@endpush