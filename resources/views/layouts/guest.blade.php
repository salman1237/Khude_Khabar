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
    <body class="font-sans text-slate-700 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-b from-brand-cream to-brand-sand/60 px-4">
            <a href="/" class="flex flex-col items-center gap-2 mb-2">
                <span class="text-5xl">🍚</span>
                <span class="font-display text-2xl font-bold text-brand-green">{{ config('app.name', 'Khude Khabar') }}</span>
                <span class="text-sm text-slate-500">শিশুর পুষ্টিকর খাবারের সঙ্গী</span>
            </a>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white shadow-soft overflow-hidden rounded-2xl border border-brand-sand">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
