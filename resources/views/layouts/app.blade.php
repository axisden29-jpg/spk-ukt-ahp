<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'SPK UKT AHP')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-surface dark:bg-slate-900 transition-colors duration-300">

{{-- Overlay Mobile --}}
<div id="overlay" class="fixed inset-0 bg-black/40 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

{{-- SIDEBAR --}}
<aside id="sidebar" class="sidebar fixed top-0 left-0 glass-sidebar flex flex-col shadow-2xl border-r border-white/10 z-50">

    {{-- Logo --}}
    <div class="px-6 py-5 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-lg">school</span>
            </div>
            <div>
                <p class="text-white font-display font-bold text-sm leading-tight">SPK UKT</p>
                <p class="text-white/60 text-xs">Metode AHP</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

        <p class="text-white/40 text-xs font-semibold uppercase tracking-wider px-3 mb-2">Menu Utama</p>

        <a href="{{ url('/dashboard') }}"
           class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <span class="material-symbols-outlined">dashboard</span> Dashboard
        </a>

        <a href="{{ route('kriteria.index') }}"
           class="nav-link {{ request()->is('kriteria*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">checklist</span> Kriteria
        </a>

        <p class="text-white/40 text-xs font-semibold uppercase tracking-wider px-3 mt-4 mb-2">Perhitungan AHP</p>

        <a href="{{ route('ahp.matriks') }}"
           class="nav-link {{ request()->is('ahp/matriks*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">grid_on</span> Matriks Perbandingan
        </a>

        <a href="{{ route('ahp.bobot') }}"
           class="nav-link {{ request()->is('ahp/bobot*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">bar_chart</span> Bobot & Konsistensi
        </a>

        <p class="text-white/40 text-xs font-semibold uppercase tracking-wider px-3 mt-4 mb-2">Data</p>

        <a href="{{ route('mahasiswa.index') }}"
           class="nav-link {{ request()->is('mahasiswa*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">group</span> Data Mahasiswa
        </a>
        <a href="{{ route('mahasiswa.status') }}"
   class="nav-link {{ request()->is('mahasiswa/status*') ? 'active' : '' }}">
    <span class="material-symbols-outlined">analytics</span> Status Pengisian
</a>

        <p class="text-white/40 text-xs font-semibold uppercase tracking-wider px-3 mt-4 mb-2">Output</p>

        <a href="{{ route('hasil.index') }}"
           class="nav-link {{ request()->is('hasil*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">leaderboard</span> Hasil & Golongan
        </a>

        <a href="{{ route('laporan.index') }}"
           class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">summarize</span> Laporan
        </a>

        <a href="{{ route('pengaturan.golongan') }}"
           class="nav-link {{ request()->is('pengaturan*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">settings</span> Pengaturan
        </a>
        <p class="text-white/40 text-xs font-semibold uppercase tracking-wider px-3 mt-4 mb-2">Lainnya</p>
<a href="{{ route('tentang') }}"
   class="nav-link {{ request()->is('tentang*') ? 'active' : '' }}">
    <span class="material-symbols-outlined">info</span> Tentang Sistem
</a>
    </nav>
    

    {{-- User Info --}}
    <div class="px-4 py-4 border-t border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-sm">person</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-xs font-semibold truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                <p class="text-white/50 text-xs truncate">{{ Auth::user()->email ?? '' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout">
                    <span class="material-symbols-outlined text-white/60 hover:text-white text-lg">logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- MAIN CONTENT --}}
<div class="main-content flex flex-col">

    {{-- Topbar --}}
    <header x-data="{ scrolled: false }" 
            @scroll.window="scrolled = (window.pageYOffset > 10)"
            class="px-6 py-3 flex items-center gap-4 sticky top-0 z-30 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md transition-all duration-300"
            :class="scrolled ? 'shadow-sm' : 'border-b border-gray-100 dark:border-slate-700'">
            
        {{-- Gradient Blur Skirt (Hanya muncul di bawah header saat di-scroll) --}}
        <div class="absolute left-0 right-0 top-full h-10 pointer-events-none transition-opacity duration-300 bg-gradient-to-b from-surface to-transparent"
             :class="scrolled ? 'opacity-100' : 'opacity-0'"
             style="
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                mask-image: linear-gradient(to bottom, black 10%, transparent 100%);
                -webkit-mask-image: linear-gradient(to bottom, black 10%, transparent 100%);
             ">
        </div>

        <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
            <span class="material-symbols-outlined text-gray-600 dark:text-gray-300">menu</span>
        </button>

        {{-- Breadcrumb / Page Title --}}
        <div class="flex items-center gap-2 text-lg text-text-muted font-display tracking-tight">
            <span class="material-symbols-outlined text-2xl text-primary drop-shadow-sm">home</span>
            @yield('breadcrumb')
        </div>

        <div class="ml-auto flex items-center gap-3">
            {{-- Dark Mode Toggle --}}
            <button onclick="toggleTheme()" class="w-9 h-9 rounded-full bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-gray-300 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors">
                <span id="theme-icon" class="material-symbols-outlined text-[20px]">light_mode</span>
            </button>
            
            {{-- Periode Aktif Badge --}}
            @php $periodeAktif = \App\Models\Pengaturan::get('periode_aktif', '2024/2025'); @endphp
            <span class="bg-primary-light dark:bg-primary/20 text-primary dark:text-primary-light text-xs font-semibold px-3 py-1.5 rounded-full flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">calendar_today</span> {{ $periodeAktif }}
            </span>
        </div>
    </header>

    {{-- Page Content --}}
    <main class="flex-1 p-6">

        {{-- Alerts diurus oleh Alpine.js Toast Component di bawah --}}
        @yield('content')
    </main>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('hidden');
    }

    // Theme Toggle Logic
    const themeIcon = document.getElementById('theme-icon');
    if (document.documentElement.classList.contains('dark')) {
        themeIcon.textContent = 'dark_mode';
    } else {
        themeIcon.textContent = 'light_mode';
    }

    function toggleTheme() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            themeIcon.textContent = 'light_mode';
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            themeIcon.textContent = 'dark_mode';
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('toastManager', () => ({
            toasts: [],
            add(toast) {
                const id = Date.now() + Math.random();
                this.toasts.push({ ...toast, id, visible: true });
                setTimeout(() => this.remove(id), 4000);
            },
            remove(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) toast.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300); // Wait for exit transition
            }
        }));
    });
</script>

{{-- Alpine.js Toast Component (Premium Style) --}}
<div x-data="toastManager" 
     @notify.window="add($event.detail)"
     x-init="
        @if(session('success')) add({ type: 'success', title: 'Berhasil', message: '{{ session('success') }}' }); @endif
        @if(session('error')) add({ type: 'error', title: 'Gagal', message: '{{ session('error') }}' }); @endif
        @if(session('warning')) add({ type: 'warning', title: 'Perhatian', message: '{{ session('warning') }}' }); @endif
        @if($errors->any()) add({ type: 'error', title: 'Validasi Gagal', message: '{{ $errors->first() }}' }); @endif
     "
     class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8 scale-95"
             x-transition:enter-end="opacity-100 translate-x-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0 scale-100"
             x-transition:leave-end="opacity-0 translate-x-8 scale-95"
             class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.08)] dark:shadow-none border border-gray-100 dark:border-slate-700 pr-4 pl-2 py-2 flex items-center gap-3 w-80 pointer-events-auto">
            
            {{-- Icon Container --}}
            <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
                 :class="{
                    'bg-green-500': toast.type === 'success',
                    'bg-yellow-500': toast.type === 'warning',
                    'bg-blue-500': toast.type === 'info',
                    'bg-red-500': toast.type === 'error'
                 }">
                 <span class="material-symbols-outlined text-white text-[20px]" 
                       x-text="toast.type === 'success' ? 'check' : (toast.type === 'error' ? 'priority_high' : (toast.type === 'warning' ? 'warning' : 'info'))"
                       style="font-variation-settings: 'FILL' 1, 'wght' 700;">
                 </span>
            </div>

            {{-- Text Content --}}
            <div class="flex-1 min-w-0 py-1">
                <p class="text-[14px] font-semibold text-gray-800 dark:text-gray-100 leading-snug truncate" x-text="toast.title"></p>
                <p class="text-[12px] text-gray-500 dark:text-gray-400 leading-snug truncate mt-0.5" x-text="toast.message" x-show="toast.message"></p>
            </div>

            {{-- Close Button --}}
            <button @click="remove(toast.id)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 shrink-0 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                <span class="material-symbols-outlined text-[18px] block">close</span>
            </button>
        </div>
    </template>
</div>

{{-- Alpine.js Global Confirm Modal (Premium Style) --}}
<div x-data="{
        isOpen: false,
        title: '',
        message: '',
        type: 'danger', // 'danger', 'success', 'warning', 'info'
        confirmText: 'Lanjutkan',
        cancelText: 'Batal',
        onConfirm: null,
        
        openModal(detail) {
            this.title = detail.title || 'Konfirmasi';
            this.message = detail.message || 'Apakah Anda yakin?';
            this.type = detail.type || 'danger';
            this.confirmText = detail.confirmText || 'Lanjutkan';
            this.cancelText = detail.cancelText || 'Batal';
            this.onConfirm = detail.onConfirm || null;
            this.isOpen = true;
        },
        
        closeModal() {
            this.isOpen = false;
        },
        
        executeConfirm() {
            if (typeof this.onConfirm === 'function') {
                this.onConfirm();
            } else if (typeof this.onConfirm === 'string') {
                // If it's a form ID
                const form = document.getElementById(this.onConfirm);
                if(form) form.submit();
            }
            this.closeModal();
        }
     }"
     @open-confirm.window="openModal($event.detail)"
     class="relative z-[150]"
     aria-labelledby="modal-title" role="dialog" aria-modal="true"
     style="display: none;" x-show="isOpen">
     
    {{-- Backdrop --}}
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>

    {{-- Modal Panel --}}
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="isOpen"
                 @click.away="closeModal()"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100 dark:border-slate-700">
                
                {{-- Header --}}
                <div class="px-4 py-3 border-b dark:border-slate-700 flex justify-between items-center"
                     :class="{
                        'bg-red-50 dark:bg-red-900/30 border-red-100 dark:border-red-900/50': type === 'danger',
                        'bg-green-50 dark:bg-green-900/30 border-green-100 dark:border-green-900/50': type === 'success',
                        'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-100 dark:border-yellow-900/50': type === 'warning',
                        'bg-blue-50 dark:bg-blue-900/30 border-blue-100 dark:border-blue-900/50': type === 'info'
                     }">
                     <span class="text-xs font-bold uppercase tracking-wider"
                           :class="{
                               'text-red-700 dark:text-red-400': type === 'danger',
                               'text-green-700 dark:text-green-400': type === 'success',
                               'text-yellow-700 dark:text-yellow-400': type === 'warning',
                               'text-blue-700 dark:text-blue-400': type === 'info'
                           }" x-text="type === 'danger' ? 'Error dialog' : (type === 'success' ? 'Positive dialog' : 'Action dialog')">
                     </span>
                     <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                         <span class="material-symbols-outlined text-sm">close</span>
                     </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-6 flex items-start gap-5">
                    {{-- Large Circular Icon --}}
                    <div class="w-14 h-14 rounded-full border-2 flex items-center justify-center shrink-0 mt-1"
                         :class="{
                             'border-red-500 text-red-500': type === 'danger',
                             'border-green-500 text-green-500': type === 'success',
                             'border-yellow-500 text-yellow-500': type === 'warning',
                             'border-blue-500 text-blue-500': type === 'info'
                         }">
                        <span class="material-symbols-outlined text-3xl"
                              x-text="type === 'danger' ? 'bolt' : (type === 'success' ? 'check' : (type === 'warning' ? 'warning' : 'info'))">
                        </span>
                    </div>
                    
                    {{-- Text --}}
                    <div>
                        <h3 class="text-lg font-bold mb-1"
                            :class="{
                                'text-red-700 dark:text-red-400': type === 'danger',
                                'text-green-700 dark:text-green-400': type === 'success',
                                'text-yellow-700 dark:text-yellow-400': type === 'warning',
                                'text-blue-700 dark:text-blue-400': type === 'info'
                            }" x-text="title"></h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed" x-text="message"></p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 dark:bg-slate-900/50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100 dark:border-slate-700">
                    <button type="button" @click="closeModal()" 
                            class="rounded-lg bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors" x-text="cancelText">
                    </button>
                    <button type="button" @click="executeConfirm()" 
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors"
                            :class="{
                                'bg-red-600 hover:bg-red-700': type === 'danger',
                                'bg-green-600 hover:bg-green-700': type === 'success',
                                'bg-yellow-500 hover:bg-yellow-600': type === 'warning',
                                'bg-blue-600 hover:bg-blue-700': type === 'info'
                            }" x-text="confirmText">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>