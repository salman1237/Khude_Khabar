@props([
    'meal',
])

<a
    href="{{ route('recipes.show', $meal) }}"
    {{ $attributes->merge(['class' => 'block bg-white rounded-xl border border-brand-sand p-4 hover:shadow-soft hover:border-brand-green/40 transition group']) }}
>
    <div class="flex items-center gap-2 text-xs font-medium text-slate-400 uppercase tracking-wide">
        <span class="text-base">{{ $meal->meal_emoji }}</span>
        {{ $meal->meal_typebengali }}
    </div>

    <h4 class="mt-1 font-display font-semibold text-slate-800 group-hover:text-brand-green">{{ $meal->meal_name_bengali }}</h4>

    <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
        @if (! empty($meal->nutrition['calories']))
            <span>🔥 {{ $meal->nutrition['calories'] }} ক্যালরি</span>
        @endif
        @if (! is_null($meal->cost_bdt))
            <span class="font-medium text-brand-green">৳ {{ rtrim(rtrim((string) $meal->cost_bdt, '0'), '.') }}</span>
        @endif
    </div>
</a>
