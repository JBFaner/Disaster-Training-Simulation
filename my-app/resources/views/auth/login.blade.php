<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LGU Admin Login</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        @vite(['resources/css/app.css'])
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
            
            * {
                font-family: 'Inter', sans-serif;
            }
            
            .login-hero-bg {
                position: relative;
                background-image: url('{{ asset('images/hero-training.jpg') }}');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                min-height: 100vh;
            }
            
            .login-hero-bg::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, rgba(16, 24, 40, 0.95) 0%, rgba(30, 41, 59, 0.92) 50%, rgba(15, 118, 110, 0.88) 100%);
                z-index: 1;
            }
            
            .login-form-wrapper {
                position: relative;
                z-index: 2;
            }
        </style>
    </head>
    <body class="login-hero-bg">
        <div class="login-form-wrapper min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white/95 backdrop-blur-xl shadow-2xl rounded-2xl p-8 border border-white/20">
                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('logo.svg') }}" alt="LGU Logo" class="h-16 w-auto">
                </div>
                
                <h1 class="text-3xl font-bold text-slate-800 mb-2 text-center">Admin Login</h1>
                <p class="text-sm text-slate-600 mb-8 text-center">
                    Manage disaster preparedness training & simulations
                </p>

                @if ($errors->any())
                    <div class="mb-6 rounded-lg bg-red-50 border-l-4 border-red-500 px-4 py-3 text-sm text-red-700 flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2" for="email">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                placeholder="admin@example.com"
                                class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2" for="password">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                placeholder="Enter your password"
                                class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-slate-600 hover:text-slate-800">
                            <input
                                type="checkbox"
                                name="remember"
                                class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 focus:ring-offset-0"
                            >
                            <span class="font-medium">Remember me</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white text-sm font-semibold px-4 py-3.5 transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02]"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Sign In to Dashboard</span>
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-200">
                    <p class="text-sm text-slate-600 text-center">
                        No admin account yet?
                        <a href="{{ route('register') }}" class="text-teal-600 hover:text-teal-700 font-semibold ml-1">
                            Register as LGU Admin
                        </a>
                    </p>
                    <p class="text-xs text-slate-500 text-center mt-3">
                        <a href="/" class="hover:text-slate-700">← Back to Home</a>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>




