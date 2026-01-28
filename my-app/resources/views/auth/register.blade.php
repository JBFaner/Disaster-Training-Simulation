<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LGU Admin Registration</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-slate-100 flex items-center justify-center">
        <div class="w-full max-w-md bg-white shadow-md rounded-xl p-8">
            <h1 class="text-2xl font-semibold text-slate-800 mb-1">LGU Admin Registration</h1>
            <p class="text-sm text-slate-500 mb-6">
                Create an LGU-admin account for managing the disaster preparedness system.
            </p>

            @if ($errors->any())
                <div class="mb-4 rounded-md bg-rose-50 border border-rose-200 px-3 py-2 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1" for="name">
                        Full name
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1" for="email">
                        Email
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1" for="password">
                        Password
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1" for="password_confirmation">
                        Confirm password
                    </label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full inline-flex justify-center items-center rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2.5 transition-colors"
                >
                    Register LGU-admin
                </button>
            </form>

            <p class="mt-4 text-xs text-slate-500 text-center">
                Already have an admin account?
                <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">
                    Go to login
                </a>
            </p>
        </div>
    </body>
</html>




