<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ManagJeh - Manajemen Keuangan') }}</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <!-- Fonts (Inter) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Theme Initialization (Prevent FOUC) -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen flex flex-col selection:bg-blue-600 selection:text-white relative">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-2xs">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Main Page Content with Glide Animation -->
        <main class="flex-1 py-6 animate-glide">
            {{ $slot }}
        </main>

        <!-- Floating Action Button (FAB) for Quick Transaction -->
        @auth
            <aside class="fixed bottom-6 right-6 z-40" aria-label="Aksi Cepat Transaksi">
                <a href="{{ route('transactions.create') }}" title="Catat Transaksi Cepat" class="group flex items-center gap-2.5 px-4 py-3.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm rounded-full shadow-xl shadow-blue-600/30 hover:shadow-2xl hover:shadow-blue-600/40 hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
                    <svg class="w-5 h-5 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline tracking-tight font-semibold">Transaksi</span>
                </a>
            </aside>
        @endauth

        <!-- Minimalist Bottom-Right Toast Notifications -->
        <div aria-live="polite" class="fixed bottom-6 left-6 sm:left-auto sm:right-24 z-50 max-w-sm space-y-2 pointer-events-none">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="pointer-events-auto flex items-center justify-between gap-3 px-4 py-3 bg-white dark:bg-slate-900 border border-emerald-500/40 text-slate-800 dark:text-slate-200 rounded-2xl shadow-xl shadow-slate-900/10 text-xs font-semibold animate-glide">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 ml-2">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" class="pointer-events-auto flex items-center justify-between gap-3 px-4 py-3 bg-white dark:bg-slate-900 border border-red-500/40 text-slate-800 dark:text-slate-200 rounded-2xl shadow-xl shadow-slate-900/10 text-xs font-semibold animate-glide">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 ml-2">&times;</button>
                </div>
            @endif
        </div>

        <footer class="py-5 border-t border-slate-200 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
            &copy; {{ date('Y') }} ManagJeh &bull; Solusi Manajemen Keuangan Terstruktur
        </footer>
    </body>
</html>
