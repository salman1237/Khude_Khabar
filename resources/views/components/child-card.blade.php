@props([
    'child',
])

@php
    $latestPlan = $child->mealPlans->first();
    $genderEmoji = $child->gender === 'boy' ? '👦' : '👧';
    $budgetLabel = ['low' => 'কম বাজেট', 'medium' => 'মাঝারি বাজেট', 'high' => 'বেশি বাজেট'][$child->budget_preference] ?? '';
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-brand-sand shadow-soft p-5 flex flex-col']) }}>
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-brand-cream flex items-center justify-center text-2xl">{{ $genderEmoji }}</div>
            <div>
                <h3 class="font-display text-lg font-bold text-slate-800 leading-tight">{{ $child->name }}</h3>
                <p class="text-sm text-slate-500">{{ $child->age_display }}</p>
            </div>
        </div>

        <a href="{{ route('children.edit', $child) }}" class="text-slate-400 hover:text-brand-green p-1" title="তথ্য সম্পাদনা">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </a>
    </div>

    <div class="flex flex-wrap gap-2 mt-4 text-xs">
        @if ($child->weight_kg)
            <span class="px-2.5 py-1 rounded-full bg-brand-cream text-slate-600">⚖️ {{ rtrim(rtrim((string) $child->weight_kg, '0'), '.') }} কেজি</span>
        @endif
        @if ($child->height_cm)
            <span class="px-2.5 py-1 rounded-full bg-brand-cream text-slate-600">📏 {{ rtrim(rtrim((string) $child->height_cm, '0'), '.') }} সেমি</span>
        @endif
        <span class="px-2.5 py-1 rounded-full bg-brand-cream text-slate-600">💰 {{ $budgetLabel }}</span>
    </div>

    @if ($child->allergies->isNotEmpty())
        <p class="mt-3 text-xs text-rose-600">⚠️ অ্যালার্জি: {{ $child->allergies->pluck('allergy_name')->implode(', ') }}</p>
    @endif

    <!-- Meal plan status -->
    <div class="mt-4 pt-4 border-t border-brand-sand">
        @if ($latestPlan && $latestPlan->status === 'completed')
            <a href="{{ route('meal-plans.show', $latestPlan) }}" class="flex items-center justify-between text-sm font-medium text-brand-green hover:underline">
                <span>🍽️ এই সপ্তাহের খাবার তালিকা</span>
                <span>→</span>
            </a>
        @elseif ($latestPlan && in_array($latestPlan->status, ['pending', 'processing']))
            <a href="{{ route('meal-plans.loading', $latestPlan) }}" class="flex items-center gap-2 text-sm font-medium text-amber-600">
                <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                খাবার তালিকা তৈরি হচ্ছে…
            </a>
        @else
            <form method="POST" action="{{ route('meal-plans.regenerate', $child) }}">
                @csrf
                <button type="submit" class="text-sm font-medium text-brand-green hover:underline">🍽️ খাবার তালিকা তৈরি করুন</button>
            </form>
        @endif
    </div>

    <!-- Actions -->
    <div class="mt-4 grid grid-cols-2 gap-2">
        <a href="{{ route('growth-tracker.show', $child) }}" class="text-center text-sm font-medium px-3 py-2 rounded-xl bg-brand-cream text-slate-700 hover:bg-brand-sand transition">
            📈 গ্রোথ
        </a>
        @if ($latestPlan && $latestPlan->status === 'completed')
            <a href="{{ route('grocery-list.show', $latestPlan) }}" class="text-center text-sm font-medium px-3 py-2 rounded-xl bg-brand-cream text-slate-700 hover:bg-brand-sand transition">
                🛒 বাজার
            </a>
        @else
            <form method="POST" action="{{ route('meal-plans.regenerate', $child) }}" class="contents">
                @csrf
                <button type="submit" class="text-center text-sm font-medium px-3 py-2 rounded-xl bg-brand-cream text-slate-700 hover:bg-brand-sand transition">🔄 নতুন তালিকা</button>
            </form>
        @endif
    </div>
</div>
