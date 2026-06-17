<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('dashboard') }}" class="text-sm text-slate-400 hover:text-brand-green">← ড্যাশবোর্ড</a>
            <h1 class="font-display text-2xl font-bold text-slate-800 mt-1">📈 {{ $child->name }}-এর গ্রোথ ট্র্যাকার</h1>
            <p class="text-sm text-slate-500">ওজন ও উচ্চতার অগ্রগতি পর্যবেক্ষণ করুন।</p>
        </div>
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Chart + history -->
        <div class="lg:col-span-2 space-y-6">
            <x-growth-chart :labels="$chartLabels" :weight="$weightData" :height="$heightData" />

            <div class="card p-5">
                <h2 class="font-display font-bold text-slate-800 mb-3">📋 পরিমাপের ইতিহাস</h2>
                @if ($records->isEmpty())
                    <p class="text-sm text-slate-400 py-4 text-center">এখনো কোনো রেকর্ড নেই।</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-400 border-b border-brand-sand">
                                    <th class="py-2 font-medium">তারিখ</th>
                                    <th class="py-2 font-medium">ওজন (কেজি)</th>
                                    <th class="py-2 font-medium">উচ্চতা (সেমি)</th>
                                    <th class="py-2 font-medium">BMI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-brand-sand">
                                @foreach ($records->sortByDesc('recorded_date') as $record)
                                    <tr>
                                        <td class="py-2.5 text-slate-700">{{ $record->recorded_date->format('d M Y') }}</td>
                                        <td class="py-2.5 text-slate-600">{{ rtrim(rtrim((string) $record->weight_kg, '0'), '.') }}</td>
                                        <td class="py-2.5 text-slate-600">{{ rtrim(rtrim((string) $record->height_cm, '0'), '.') }}</td>
                                        <td class="py-2.5 text-slate-600">{{ $record->bmi ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Add record -->
        <div class="lg:col-span-1">
            <div class="card p-5 sticky top-6">
                <h2 class="font-display font-bold text-slate-800 mb-4">➕ নতুন পরিমাপ</h2>
                <form method="POST" action="{{ route('growth-tracker.store', $child) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="recorded_date" class="field-label">তারিখ <span class="text-rose-500">*</span></label>
                        <input id="recorded_date" name="recorded_date" type="date" max="{{ now()->toDateString() }}"
                               value="{{ old('recorded_date', now()->toDateString()) }}" class="field-input" required>
                        @error('recorded_date') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="weight_kg" class="field-label">ওজন (কেজি) <span class="text-rose-500">*</span></label>
                        <input id="weight_kg" name="weight_kg" type="number" step="0.01" min="0"
                               value="{{ old('weight_kg', $child->weight_kg) }}" class="field-input" required>
                        @error('weight_kg') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="height_cm" class="field-label">উচ্চতা (সেমি) <span class="text-rose-500">*</span></label>
                        <input id="height_cm" name="height_cm" type="number" step="0.01" min="0"
                               value="{{ old('height_cm', $child->height_cm) }}" class="field-input" required>
                        @error('height_cm') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="btn-primary w-full">💾 সংরক্ষণ করুন</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
