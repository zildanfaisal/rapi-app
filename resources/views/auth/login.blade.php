<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RAPI PVC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .animated-gradient {
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #4facfe);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            border-color: #667eea;
        }
    </style>
</head>
<body class="animated-gradient min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-6xl grid md:grid-cols-2 gap-0 rounded-3xl overflow-hidden shadow-2xl">

        <!-- Left Side - Branding -->
        <div class="hidden md:flex flex-col justify-center items-center bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 p-12 text-white relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-0 left-0 w-72 h-72 bg-white opacity-5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full translate-x-1/2 translate-y-1/2"></div>

            <div class="relative z-10 text-center">
                <!-- Logo -->
                <div class="mb-8 flex justify-center">
                    <img src="{{ asset('images/logo-rapi.png') }}" alt="RAPI PVC Logo" class="w-48 h-48 object-contain drop-shadow-2xl">
                </div>

                <h1 class="text-4xl font-bold mb-4">RAPI PVC</h1>
                <p class="text-lg text-indigo-100 mb-8">Sistem Manajemen Keuangan</p>

                <div class="space-y-4 text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span>Manajemen Keuangan Terintegrasi</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span>Laporan Real-time</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span>Aman & Terpercaya</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="glass-effect p-8 md:p-12 flex flex-col justify-center">
            <!-- Mobile Logo -->
            <div class="md:hidden flex justify-center mb-8">
                <img src="{{ asset('path-to-your-logo.png') }}" alt="RAPI PVC Logo" class="w-24 h-24 object-contain">
            </div>

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang</h2>
                <p class="text-gray-600">Silakan login untuk melanjutkan</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-200"
                            placeholder="nama@email.com"
                        />
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-200"
                            placeholder="••••••••"
                        />
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        >
                        <span class="ml-2 text-sm text-gray-600">Ingat Saya</span>
                    </label>

                    {{-- @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                            Lupa Password?
                        </a>
                    @endif --}}
                </div>

                <!-- Login Button -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                >
                    Masuk
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-gray-500">
                © {{ date('Y') }} RAPI PVC. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
