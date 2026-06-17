@php
    $child = $child ?? null;
    $oldAllergies = old('allergies', $child?->allergies->pluck('allergy_name')->all() ?? []);
    $oldConditions = old('health_conditions', $child?->healthConditions->pluck('condition_name')->all() ?? []);
    // Ensure at least one empty row for usability.
    $oldAllergies = count($oldAllergies) ? $oldAllergies : [''];
    $oldConditions = count($oldConditions) ? $oldConditions : [''];
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if (($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div class="card p-6 space-y-5">
        <h2 class="font-display font-bold text-slate-800">📋 মৌলিক তথ্য</h2>

        <div>
            <label for="name" class="field-label">শিশুর নাম <span class="text-rose-500">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name', $child?->name) }}" class="field-input" required>
            @error('name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label for="age_months" class="field-label">বয়স (মাসে) <span class="text-rose-500">*</span></label>
                <input id="age_months" name="age_months" type="number" min="0" max="216" value="{{ old('age_months', $child?->age_months) }}" class="field-input" required>
                @error('age_months') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label">লিঙ্গ <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach (['boy' => '👦 ছেলে', 'girl' => '👧 মেয়ে'] as $val => $label)
                        <label class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border border-brand-sand cursor-pointer has-[:checked]:border-brand-green has-[:checked]:bg-brand-cream transition">
                            <input type="radio" name="gender" value="{{ $val }}" class="text-brand-green focus:ring-brand-green/40"
                                @checked(old('gender', $child?->gender) === $val) required>
                            <span class="text-sm">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('gender') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label for="weight_kg" class="field-label">ওজন (কেজি)</label>
                <input id="weight_kg" name="weight_kg" type="number" step="0.01" min="0" value="{{ old('weight_kg', $child?->weight_kg) }}" class="field-input" placeholder="যেমন: ৮.৫">
                @error('weight_kg') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="height_cm" class="field-label">উচ্চতা (সেমি)</label>
                <input id="height_cm" name="height_cm" type="number" step="0.01" min="0" value="{{ old('height_cm', $child?->height_cm) }}" class="field-input" placeholder="যেমন: ৭০">
                @error('height_cm') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="field-label">বাজেট পছন্দ <span class="text-rose-500">*</span></label>
            <div class="grid grid-cols-3 gap-3">
                @foreach (['low' => '💵 কম', 'medium' => '💰 মাঝারি', 'high' => '💎 বেশি'] as $val => $label)
                    <label class="flex items-center justify-center gap-1 px-3 py-2.5 rounded-xl border border-brand-sand cursor-pointer has-[:checked]:border-brand-green has-[:checked]:bg-brand-cream transition text-sm">
                        <input type="radio" name="budget_preference" value="{{ $val }}" class="sr-only"
                            @checked(old('budget_preference', $child?->budget_preference ?? 'medium') === $val)>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            @error('budget_preference') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <!-- Allergies -->
    <div class="card p-6" x-data="{ items: @js(array_values($oldAllergies)) }">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-bold text-slate-800">🥜 অ্যালার্জি</h2>
            <button type="button" @click="items.push('')" class="text-sm text-brand-green font-medium">+ যোগ করুন</button>
        </div>
        <p class="text-xs text-slate-400 mt-1">যেসব খাবারে শিশুর অ্যালার্জি আছে (থাকলে)।</p>
        <div class="space-y-2 mt-3">
            <template x-for="(item, index) in items" :key="index">
                <div class="flex gap-2">
                    <input type="text" :name="`allergies[${index}]`" x-model="items[index]" class="field-input" placeholder="যেমন: চিনাবাদাম">
                    <button type="button" @click="items.splice(index, 1)" class="px-3 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100">✕</button>
                </div>
            </template>
        </div>
    </div>

    <!-- Health conditions -->
    <div class="card p-6" x-data="{ items: @js(array_values($oldConditions)) }">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-bold text-slate-800">🩺 স্বাস্থ্য সমস্যা</h2>
            <button type="button" @click="items.push('')" class="text-sm text-brand-green font-medium">+ যোগ করুন</button>
        </div>
        <p class="text-xs text-slate-400 mt-1">যেমন: রক্তস্বল্পতা, কম ওজন (থাকলে)।</p>
        <div class="space-y-2 mt-3">
            <template x-for="(item, index) in items" :key="index">
                <div class="flex gap-2">
                    <input type="text" :name="`health_conditions[${index}]`" x-model="items[index]" class="field-input" placeholder="যেমন: রক্তস্বল্পতা">
                    <button type="button" @click="items.splice(index, 1)" class="px-3 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100">✕</button>
                </div>
            </template>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('dashboard') }}" class="btn-ghost">বাতিল</a>
        <button type="submit" class="btn-primary">{{ $submitLabel ?? 'সংরক্ষণ করুন' }}</button>
    </div>
</form>
