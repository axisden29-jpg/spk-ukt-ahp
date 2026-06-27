@extends('layouts.portal')
@section('title', 'Profil Saya')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-on-surface">Profil Saya</h1>
    <p class="text-text-muted text-sm mt-1">Kelola informasi akun Anda</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Info Profil --}}
    <div class="card p-6">
        <h2 class="font-display font-semibold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">person</span>
            Informasi Akun
        </h2>

        <div class="flex items-center gap-4 mb-6 p-4 bg-primary-light/40 rounded-xl">
            <div class="w-14 h-14 bg-primary rounded-2xl flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-white text-2xl">person</span>
            </div>
            <div>
                <p class="font-bold text-on-surface">{{ $mahasiswa->nama }}</p>
                <p class="text-sm text-text-muted font-mono">{{ $mahasiswa->nim }}</p>
                <p class="text-xs text-text-muted mt-0.5">{{ $mahasiswa->program_studi ?? '-' }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('mahasiswa.portal.updateProfil') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-on-surface mb-1">Nama Lengkap</label>
                <input type="text" name="nama" value="{{ $mahasiswa->nama }}" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                @error('nama')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-on-surface mb-1">NIM</label>
                <input type="text" value="{{ $mahasiswa->nim }}" disabled
                    class="w-full border border-gray-100 rounded-xl px-3 py-2 text-sm bg-gray-50 text-text-muted cursor-not-allowed">
                <p class="text-xs text-text-muted mt-1">NIM tidak dapat diubah</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-on-surface mb-1">Program Studi</label>
                <input type="text" value="{{ $mahasiswa->program_studi ?? '-' }}" disabled
                    class="w-full border border-gray-100 rounded-xl px-3 py-2 text-sm bg-gray-50 text-text-muted cursor-not-allowed">
            </div>
            <button type="submit"
                class="w-full bg-primary text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-primary-dark transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-base">save</span> Simpan Perubahan
            </button>
        </form>
    </div>

    {{-- Ganti Password --}}
    <div class="card p-6">
        <h2 class="font-display font-semibold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">lock</span>
            Ganti Password
        </h2>

        <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 mb-5 text-xs text-yellow-800">
            <p class="font-semibold mb-0.5">⚠ Perhatian</p>
            <p>Password default Anda adalah NIM. Sangat disarankan untuk menggantinya.</p>
        </div>

        <form method="POST" action="{{ route('mahasiswa.portal.updatePassword') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-on-surface mb-1">Password Lama</label>
                <input type="password" name="password_lama" required
                    placeholder="Masukkan password saat ini"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-on-surface mb-1">Password Baru</label>
                <input type="password" name="password_baru" required
                    placeholder="Minimal 6 karakter"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-on-surface mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_baru_confirmation" required
                    placeholder="Ulangi password baru"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </div>
            <button type="submit"
                class="w-full bg-primary text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-primary-dark transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-base">lock_reset</span> Ganti Password
            </button>
        </form>
    </div>

</div>
@endsection