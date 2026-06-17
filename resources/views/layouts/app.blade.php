<!DOCTYPE html>
<html lang="bn">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Khude Khabar') }}</title>

        <!-- Fonts: Hind Siliguri for Bengali, Baloo 2 for headings -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=hind-siliguri:400,500,600,700|baloo-2:500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-700 bg-brand-cream">
        <div class="min-h-screen lg:flex" x-data="{ sidebarOpen: false }">
            <!-- Sidebar -->
            <x-sidebar />

            <!-- Mobile overlay -->
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
                class="fixed inset-0 z-20 bg-slate-900/40 lg:hidden"
                style="display: none;"
            ></div>

            <!-- Main column -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Mobile top bar -->
                <header class="lg:hidden flex items-center justify-between bg-white border-b border-brand-sand px-4 py-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-display text-lg font-bold text-brand-green">
                        <span class="text-2xl">🍚</span> {{ config('app.name') }}
                    </a>
                    <button @click="sidebarOpen = true" class="p-2 text-slate-600" aria-label="মেনু">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </header>

                @isset($header)
                    <div class="bg-white border-b border-brand-sand">
                        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                <main class="flex-1">
                    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <x-alert />
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
