@props([
    'type' => null,
])

@php
    // Resolve from session flash when no explicit type/message is passed.
    $resolvedType = $type;
    $message = $slot->isNotEmpty() ? $slot : null;

    if (! $resolvedType) {
        foreach (['success', 'error', 'warning', 'status'] as $flash) {
            if (session()->has($flash)) {
                $resolvedType = $flash === 'status' ? 'success' : $flash;
                $message = session($flash);
                break;
            }
        }
    }

    $styles = [
        'success' => ['bg' => 'bg-emerald-50 border-emerald-200 text-emerald-800', 'icon' => '✅'],
        'error'   => ['bg' => 'bg-rose-50 border-rose-200 text-rose-800', 'icon' => '⚠️'],
        'warning' => ['bg' => 'bg-amber-50 border-amber-200 text-amber-800', 'icon' => '🔔'],
        'info'    => ['bg' => 'bg-sky-50 border-sky-200 text-sky-800', 'icon' => 'ℹ️'],
    ];
    $style = $styles[$resolvedType] ?? $styles['info'];
@endphp

@if ($message)
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        {{ $attributes->merge(['class' => "flex items-start gap-3 border rounded-xl px-4 py-3 mb-5 text-sm {$style['bg']}"]) }}
    >
        <span class="text-lg leading-none">{{ $style['icon'] }}</span>
        <div class="flex-1">{{ $message }}</div>
        <button type="button" @click="show = false" class="opacity-60 hover:opacity-100" aria-label="বন্ধ">✕</button>
    </div>
@endif
