<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('meal-plans.show', $mealPlan) }}" class="text-sm text-slate-400 hover:text-brand-green">← সাপ্তাহিক তালিকা</a>
                <h1 class="font-display text-2xl font-bold text-slate-800 mt-1">🛒 বাজারের তালিকা</h1>
                <p class="text-sm text-slate-500">
                    {{ $child->name }} · সপ্তাহ শুরু {{ $mealPlan->week_start_date->format('d M Y') }}
                </p>
            </div>
            <div class="card px-5 py-3 text-right">
                <p class="text-xs text-slate-400">আনুমানিক মোট খরচ</p>
                <p class="font-display text-2xl font-bold text-brand-green">৳ {{ number_format($totalCost) }}</p>
            </div>
        </div>
    </x-slot>

    @if ($groceriesByCategory->isEmpty())
        <div class="card p-10 text-center text-slate-400">কোনো উপকরণ পাওয়া যায়নি।</div>
    @else
        <div class="grid gap-5 sm:grid-cols-2">
            @foreach ($groceriesByCategory as $category => $items)
                <div class="card p-5"
                     x-data="{ checked: 0, total: {{ $items->count() }} }">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-display font-bold text-slate-800">{{ $category }}</h2>
                        <span class="text-xs text-slate-400" x-text="`${checked}/${total}`"></span>
                    </div>
                    <ul class="divide-y divide-brand-sand">
                        @foreach ($items as $item)
                            <li class="flex items-center gap-3 py-2.5">
                                <input type="checkbox"
                                       class="rounded border-brand-sand text-brand-green focus:ring-brand-green/40"
                                       @change="checked += $event.target.checked ? 1 : -1">
                                <span class="flex-1 text-sm text-slate-700">
                                    {{ $item['ingredient_name'] }}
                                    <span class="text-slate-400">({{ $item['quantities'] }} {{ $item['unit'] }})</span>
                                </span>
                                <span class="text-sm text-slate-500">৳ {{ rtrim(rtrim((string) $item['total_price'], '0'), '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            * দাম আনুমানিক এবং বাজারভেদে পরিবর্তিত হতে পারে।
        </p>
    @endif
</x-app-layout>
