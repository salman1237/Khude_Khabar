<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('dashboard') }}" class="text-sm text-slate-400 hover:text-brand-green">← ড্যাশবোর্ড</a>
                <h1 class="font-display text-2xl font-bold text-slate-800 mt-1">
                    {{ $child->name }}-এর সাপ্তাহিক খাবার তালিকা
                </h1>
                <p class="text-sm text-slate-500">
                    সপ্তাহ শুরু: {{ $mealPlan->week_start_date->format('d M Y') }} · {{ $child->age_display }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('grocery-list.show', $mealPlan) }}" class="btn-ghost">🛒 বাজারের তালিকা</a>
                <form method="POST" action="{{ route('meal-plans.regenerate', $child) }}">
                    @csrf
                    <button type="submit" class="btn-primary">🔄 নতুন তালিকা</button>
                </form>
            </div>
        </div>
    </x-slot>

    <!-- Summary -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="card p-4">
            <p class="text-xs text-slate-400">মোট খরচ (সপ্তাহ)</p>
            <p class="font-display text-xl font-bold text-brand-green mt-1">৳ {{ number_format($mealPlan->total_cost) }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-400">দৈনিক গড়</p>
            <p class="font-display text-xl font-bold text-slate-800 mt-1">৳ {{ number_format($mealPlan->total_cost / 7) }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-400">মোট খাবার</p>
            <p class="font-display text-xl font-bold text-slate-800 mt-1">{{ $mealPlan->mealPlanDays->count() }} টি</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-400">দিন</p>
            <p class="font-display text-xl font-bold text-slate-800 mt-1">৭ দিন</p>
        </div>
    </div>

    <!-- Days -->
    <div class="space-y-6">
        @foreach ($mealsByDay as $dayName => $meals)
            <div class="card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display text-lg font-bold text-slate-800">
                        📅 {{ \App\Models\MealPlan::DAYS_BENGALI[$dayName] ?? $dayName }}
                    </h2>
                    <span class="text-sm text-brand-green font-medium">
                        ৳ {{ number_format($meals->sum('cost_bdt')) }}
                    </span>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach (['breakfast', 'lunch', 'dinner'] as $type)
                        @php $meal = $meals->firstWhere('meal_type', $type); @endphp
                        @if ($meal)
                            <x-meal-card :meal="$meal" />
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
