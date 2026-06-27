<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login — SPK UKT AHP</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#006b3f',
                        'primary-dark': '#004d2c',
                        'primary-light': '#e8f5ee',
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
        body { font-family: 'Inter', sans-serif; }

        .bg-pattern {
            background-color: #006b3f;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,0.05) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(0,77,44,0.5) 0%, transparent 70%);
        }

        .card-login {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.25), 0 0 0 1px rgba(255,255,255,0.1);
        }

        .input-field {
            width: 100%;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 44px 12px 16px;
            font-size: 0.9rem;
            transition: all 0.2s;
            outline: none;
            background: #fafafa;
            color: #191c1d;
        }
        .input-field:focus {
            border-color: #006b3f;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0,107,63,0.1);
        }
        .input-field::placeholder { color: #9ca3af; }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #006b3f 0%, #004d2c 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(0,107,63,0.4);
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0,107,63,0.5);
        }
        .btn-login:active { transform: translateY(0); }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="bg-pattern min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    {{-- Floating decorative shapes --}}
    <div class="floating-shape w-64 h-64 -top-16 -left-16" style="animation-delay: 0s;"></div>
    <div class="floating-shape w-48 h-48 top-1/4 -right-12" style="animation-delay: 2s;"></div>
    <div class="floating-shape w-32 h-32 bottom-16 left-1/4" style="animation-delay: 4s;"></div>
    <div class="floating-shape w-20 h-20 bottom-8 right-1/3" style="animation-delay: 1s;"></div>

    <div class="w-full max-w-md relative z-10">

        {{-- Card Login --}}
        <div class="card-login p-8">

            {{-- Logo & Header --}}
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <span class="material-symbols-outlined text-white text-3xl">school</span>
                </div>
                <h1 class="text-2xl font-display font-bold text-gray-900">SPK UKT AHP</h1>
                <p class="text-gray-500 text-sm mt-1">Sistem Pendukung Keputusan</p>
                <p class="text-gray-400 text-xs mt-0.5">Penentuan Golongan Uang Kuliah Tunggal</p>
            </div>

            {{-- Divider --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-xs text-gray-400 font-medium">Masuk ke sistem</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            {{-- Error Session --}}
            @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 flex items-center gap-2 text-sm">
                <span class="material-symbols-outlined text-red-500 text-base">error</span>
                {{ session('error') }}
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Email / NIM --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Email / NIM
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Masukkan email atau NIM Anda"
                            class="input-field pr-11">
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">
                            person
                        </span>
                    </div>
                    @error('email')
                    <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">error</span>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="passwordInput"
                            required
                            autocomplete="current-password"
                            placeholder="Masukkan password Anda"
                            class="input-field pr-11">
                        <button type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <span class="material-symbols-outlined text-xl" id="eyeIcon">visibility</span>
                        </button>
                    </div>
                    @error('password')
                    <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">error</span>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 accent-primary rounded">
                        <span class="text-sm text-gray-600">Ingat saya</span>
                    </label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-sm text-primary hover:text-primary-dark font-medium hover:underline">
                        Lupa password?
                    </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-login mt-2">
                    <span class="flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-base">login</span>
                        Masuk
                    </span>
                </button>
            </form>

            {{-- Info Mahasiswa --}}
            <div class="mt-6 bg-primary-light rounded-xl px-4 py-3">
                <p class="text-xs font-semibold text-primary mb-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">info</span>
                    Info untuk Mahasiswa
                </p>
                <p class="text-xs text-green-800 leading-relaxed">
                    Login menggunakan <strong>NIM</strong> sebagai username dan password.
                    Hubungi admin jika belum memiliki akses.
                </p>
            </div>

        </div>

        {{-- Footer --}}
        <p class="text-center text-white/50 text-xs mt-6">
            © {{ date('Y') }} SPK UKT AHP — Metode Analytical Hierarchy Process
        </p>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }
    </script>

</body>
</html>