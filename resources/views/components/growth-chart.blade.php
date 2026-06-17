@props([
    'labels' => [],
    'weight' => [],
    'height' => [],
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-brand-sand shadow-soft p-5']) }}>
    @if (count($labels) === 0)
        <div class="text-center py-10 text-slate-400 text-sm">
            <p class="text-3xl mb-2">📊</p>
            এখনো কোনো পরিমাপ যোগ করা হয়নি। প্রথম পরিমাপ যোগ করলে গ্রাফ দেখা যাবে।
        </div>
    @else
        <div
            x-data="growthChart({
                labels: @js($labels),
                weight: @js($weight),
                height: @js($height),
            })"
            x-init="render()"
            wire:ignore
        >
            <canvas x-ref="canvas" height="120"></canvas>
        </div>
    @endif
</div>
