<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>403 — Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=Inter:wght@400;500&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { primary: '#006b3f', 'primary-light': '#e8f5ee' },
                fontFamily: { sans: ['Inter','sans-serif'], display: ['Plus Jakarta Sans','sans-serif'] }
            }}
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="text-center max-w-md">
        <div class="w-24 h-24 bg-red-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-red-500 text-5xl">lock</span>
        </div>
        <h1 class="text-7xl font-display font-black text-red-500 mb-2">403</h1>
        <h2 class="text-xl font-display font-bold text-gray-800 mb-3">Akses Ditolak</h2>
        <p class="text-gray-500 text-sm mb-8 leading-relaxed">
            Anda tidak memiliki izin untuk mengakses halaman ini.
            Silakan login dengan akun yang sesuai.
        </p>
        <div class="flex gap-3 justify-center">
            <a href="javascript:history.back()"
                class="border border-gray-300 text-gray-600 px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-100 transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-base">arrow_back</span> Kembali
            </a>
            <a href="{{ url('/dashboard') }}"
                class="bg-primary text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-green-800 transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-base">home</span> Dashboard
            </a>
        </div>
    </div>
</body>
</html>