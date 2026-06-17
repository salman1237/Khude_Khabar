<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('meal-plans.show', $mealPlan) }}" class="text-sm text-slate-400 hover:text-brand-green">← সাপ্তাহিক তালিকা</a>
            <div class="flex items-center gap-3 mt-1">
                <span class="text-3xl">{{ $meal->meal_emoji }}</span>
                <div>
                    <h1 class="font-display text-2xl font-bold text-slate-800">{{ $meal->meal_name_bengali }}</h1>
                    <p class="text-sm text-slate-500">
                        {{ $meal->day_name_bengali }} · {{ $meal->meal_typebengali }} · {{ $child->name }}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Ingredients -->
        <div class="lg:col-span-1 space-y-6">
            <div class="card p-5">
                <h2 class="font-display font-bold text-slate-800 mb-3">🧺 উপকরণ</h2>
                <ul class="divide-y divide-brand-sand">
                    @forelse ($meal->ingredients as $ingredient)
                        <li class="flex items-center justify-between py-2.5 text-sm">
                            <span class="text-slate-700">{{ $ingredient->ingredient_name }}</span>
                            <span class="text-slate-500">{{ $ingredient->quantity }} {{ $ingredient->unit }}</span>
                        </li>
                    @empty
                        <li class="py-2.5 text-sm text-slate-400">উপকরণ তালিকাভুক্ত নেই।</li>
                    @endforelse
                </ul>
                @if (! is_null($meal->cost_bdt))
                    <div class="mt-3 pt-3 border-t border-brand-sand flex items-center justify-between">
                        <span class="text-sm text-slate-500">আনুমানিক খরচ</span>
                        <span class="font-display font-bold text-brand-green">৳ {{ rtrim(rtrim((string) $meal->cost_bdt, '0'), '.') }}</span>
                    </div>
                @endif
            </div>

            <!-- Nutrition -->
            @if (! empty($meal->nutrition))
                <div class="card p-5">
                    <h2 class="font-display font-bold text-slate-800 mb-3">💪 পুষ্টিগুণ</h2>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-xl bg-brand-cream p-3">
                            <p class="font-display text-lg font-bold text-slate-800">{{ $meal->nutrition['calories'] ?? '—' }}</p>
                            <p class="text-xs text-slate-400">ক্যালরি</p>
                        </div>
                        <div class="rounded-xl bg-brand-cream p-3">
                            <p class="font-display text-lg font-bold text-slate-800">{{ $meal->nutrition['protein'] ?? '—' }}</p>
                            <p class="text-xs text-slate-400">প্রোটিন (গ্রাম)</p>
                        </div>
                        <div class="rounded-xl bg-brand-cream p-3">
                            <p class="font-display text-lg font-bold text-slate-800">{{ $meal->nutrition['iron'] ?? '—' }}</p>
                            <p class="text-xs text-slate-400">আয়রন (মিগ্রা)</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Instructions -->
        <div class="lg:col-span-2">
            <div class="card p-6">
                <h2 class="font-display font-bold text-slate-800 mb-4">👩‍🍳 রান্নার পদ্ধতি</h2>
                @if (! empty($meal->instructions))
                    <ol class="space-y-4">
                        @foreach ($meal->instructions as $i => $step)
                            <li class="flex gap-4">
                                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-brand-green text-white flex items-center justify-center font-display font-bold">{{ $i + 1 }}</span>
                                <p class="text-slate-700 pt-1">{{ $step }}</p>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <p class="text-slate-400 text-sm">এই খাবারের জন্য বিস্তারিত পদ্ধতি যোগ করা হয়নি।</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
