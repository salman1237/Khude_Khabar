<!DOCTYPE html>
<html lang="bn">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Khude Khabar') }} — শিশুর পুষ্টিকর খাবারের সঙ্গী</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=hind-siliguri:400,500,600,700|baloo-2:500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-700 bg-brand-cream">
        <!-- Nav -->
        <header class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 font-display text-xl font-bold text-brand-green">
                <span class="text-2xl">🍚</span> {{ config('app.name', 'Khude Khabar') }}
            </a>
            <nav class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary">ড্যাশবোর্ড</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-brand-green px-3 py-2">লগ ইন</a>
                    <a href="{{ route('register') }}" class="btn-primary">শুরু করুন</a>
                @endauth
            </nav>
        </header>

        <!-- Hero -->
        <main>
            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20 grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-brand-green/10 text-brand-green text-sm font-medium mb-4">
                        🇧🇩 বাংলাদেশের শিশুদের জন্য
                    </span>
                    <h1 class="font-display text-4xl sm:text-5xl font-bold text-slate-800 leading-tight">
                        আপনার শিশুর জন্য <span class="text-brand-green">পুষ্টিকর</span> সাপ্তাহিক খাবার তালিকা
                    </h1>
                    <p class="text-lg text-slate-500 mt-5">
                        বয়স, বাজেট, অ্যালার্জি ও স্বাস্থ্যের কথা বিবেচনা করে স্বয়ংক্রিয়ভাবে তৈরি হয়
                        সাশ্রয়ী বাংলা খাবারের তালিকা, রেসিপি ও বাজারের লিস্ট।
                    </p>
                    <div class="flex flex-wrap gap-3 mt-7">
                        <a href="{{ route('register') }}" class="btn-primary text-base">🌱 বিনামূল্যে শুরু করুন</a>
                        <a href="{{ route('login') }}" class="btn-ghost text-base">লগ ইন</a>
                    </div>
                </div>
                <div class="relative">
                    <div class="card p-8 text-center">
                        <div class="text-7xl">🍲</div>
                        <p class="font-display text-lg font-bold text-slate-800 mt-4">সবজি খিচুড়ি</p>
                        <p class="text-sm text-slate-400">সকালের নাস্তা · ২৮০ ক্যালরি · ৳৩৫</p>
                        <div class="grid grid-cols-3 gap-2 mt-5 text-xs">
                            <div class="rounded-xl bg-brand-cream p-3"><p class="font-bold text-slate-700">২৮০</p>ক্যালরি</div>
                            <div class="rounded-xl bg-brand-cream p-3"><p class="font-bold text-slate-700">৯</p>প্রোটিন</div>
                            <div class="rounded-xl bg-brand-cream p-3"><p class="font-bold text-slate-700">৩</p>আয়রন</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <h2 class="font-display text-2xl font-bold text-slate-800 text-center mb-10">যেভাবে কাজ করে</h2>
                <div class="grid sm:grid-cols-3 gap-6">
                    @foreach ([
                        ['🍼', 'শিশুর তথ্য দিন', 'বয়স, ওজন, বাজেট ও অ্যালার্জির তথ্য যোগ করুন।'],
                        ['🍽️', 'তালিকা পান', 'সপ্তাহের ২১টি খাবারের পরিকল্পনা ও রেসিপি স্বয়ংক্রিয়ভাবে তৈরি হয়।'],
                        ['📈', 'অগ্রগতি দেখুন', 'গ্রোথ ট্র্যাকারে ওজন ও উচ্চতার অগ্রগতি পর্যবেক্ষণ করুন।'],
                    ] as $feature)
                        <div class="card p-6 text-center">
                            <div class="text-4xl mb-3">{{ $feature[0] }}</div>
                            <h3 class="font-display font-bold text-slate-800">{{ $feature[1] }}</h3>
                            <p class="text-sm text-slate-500 mt-2">{{ $feature[2] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </main>

        <footer class="text-center text-sm text-slate-400 py-8">
            © {{ date('Y') }} {{ config('app.name', 'Khude Khabar') }} — শিশুর সুস্থ ভবিষ্যতের জন্য।
        </footer>
    </body>
</html>
