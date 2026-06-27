<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Portal Mahasiswa') — SPK UKT</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#006b3f',
                        'primary-dark': '#004d2c',
                        'primary-light': '#e8f5ee',
                        'surface': '#f8f9fa',
                        'on-surface': '#191c1d',
                        'text-muted': '#636E72',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    </style>
</head>
<body class="bg-surface min-h-screen">

{{-- NAVBAR --}}
<nav class="bg-primary shadow-lg sticky top-0 z-40">
    <div class="max-w-4xl mx-auto px-4">

        {{-- Baris 1: Logo + Info User + Logout --}}
        <div class="flex items-center justify-between h-14">

            {{-- Logo --}}
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-white text-base">school</span>
                </div>
                <div>
                    <p class="text-white font-display font-bold text-sm leading-none">Portal Mahasiswa</p>
                    <p class="text-white/50 text-xs leading-none mt-0.5">SPK UKT — AHP</p>
                </div>
            </div>

            {{-- User + Logout --}}
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 bg-white/10 rounded-xl px-3 py-1.5">
                    <span class="material-symbols-outlined text-white/70 text-base">badge</span>
                    <div>
                        <p class="text-white text-xs font-semibold leading-none">
                            {{ Str::limit(auth()->user()->mahasiswa->nama ?? auth()->user()->name, 20) }}
                        </p>
                        <p class="text-white/50 text-xs leading-none mt-0.5">
                            {{ auth()->user()->mahasiswa->nim ?? '' }}
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-1.5 text-white/70 hover:text-white hover:bg-white/10 text-xs px-3 py-1.5 rounded-xl transition-colors">
                        <span class="material-symbols-outlined text-base">logout</span>
                        <span class="hidden sm:inline font-medium">Keluar</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Baris 2: Nav Links --}}
        <div class="flex gap-1 pb-2">
            <a href="{{ route('mahasiswa.portal.dashboard') }}"
                class="flex items-center gap-1.5 text-xs px-4 py-2 rounded-xl font-medium transition-colors
                {{ request()->routeIs('mahasiswa.portal.dashboard')
                    ? 'bg-white text-primary shadow-sm'
                    : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <span class="material-symbols-outlined text-sm">dashboard</span>
                Dashboard
            </a>
            <a href="{{ route('mahasiswa.portal.isiNilai') }}"
                class="flex items-center gap-1.5 text-xs px-4 py-2 rounded-xl font-medium transition-colors
                {{ request()->routeIs('mahasiswa.portal.isiNilai')
                    ? 'bg-white text-primary shadow-sm'
                    : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <span class="material-symbols-outlined text-sm">edit_note</span>
                Isi Data
            </a>
            <a href="{{ route('mahasiswa.portal.hasil') }}"
                class="flex items-center gap-1.5 text-xs px-4 py-2 rounded-xl font-medium transition-colors
                {{ request()->routeIs('mahasiswa.portal.hasil')
                    ? 'bg-white text-primary shadow-sm'
                    : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <span class="material-symbols-outlined text-sm">leaderboard</span>
                Hasil UKT
            </a>
            <a href="{{ route('mahasiswa.portal.profil') }}"
    class="flex items-center gap-1.5 text-xs px-4 py-2 rounded-xl font-medium transition-colors
    {{ request()->routeIs('mahasiswa.portal.profil')
        ? 'bg-white text-primary shadow-sm'
        : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
    <span class="material-symbols-outlined text-sm">manage_accounts</span>
    Profil
</a>
        </div>

    </div>
</nav>

{{-- CONTENT --}}
<main class="max-w-4xl mx-auto px-4 py-6">

    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 flex items-center gap-2 text-sm">
        <span class="material-symbols-outlined text-green-600 text-base">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 flex items-center gap-2 text-sm">
        <span class="material-symbols-outlined text-red-600 text-base">error</span>
        {{ session('error') }}
    </div>
    @endif

    @if(session('warning'))
    <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl px-4 py-3 flex items-center gap-2 text-sm">
        <span class="material-symbols-outlined text-yellow-600 text-base">warning</span>
        {{ session('warning') }}
    </div>
    @endif

    @yield('content')
</main>

@stack('scripts')
</body>
</html>