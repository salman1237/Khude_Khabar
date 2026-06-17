<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('dashboard') }}" class="text-sm text-slate-400 hover:text-brand-green">← ড্যাশবোর্ড</a>
            <h1 class="font-display text-2xl font-bold text-slate-800 mt-1">নতুন শিশু যোগ করুন</h1>
            <p class="text-sm text-slate-500">তথ্য দিলেই স্বয়ংক্রিয়ভাবে সাপ্তাহিক খাবার তালিকা তৈরি হবে।</p>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        @include('children.form', [
            'action' => route('children.store'),
            'method' => 'POST',
            'submitLabel' => '🍽️ যোগ করুন ও তালিকা তৈরি করুন',
        ])
    </div>
</x-app-layout>
