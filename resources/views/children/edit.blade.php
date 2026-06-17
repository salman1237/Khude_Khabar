<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('dashboard') }}" class="text-sm text-slate-400 hover:text-brand-green">← ড্যাশবোর্ড</a>
                <h1 class="font-display text-2xl font-bold text-slate-800 mt-1">{{ $child->name }}-এর তথ্য</h1>
                <p class="text-sm text-slate-500">তথ্য পরিবর্তন করলে নতুন খাবার তালিকা তৈরি হতে পারে।</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        @include('children.form', [
            'child' => $child,
            'action' => route('children.update', $child),
            'method' => 'PUT',
            'submitLabel' => '💾 পরিবর্তন সংরক্ষণ করুন',
        ])

        <!-- Danger zone -->
        <div class="card p-6 border-rose-200">
            <h2 class="font-display font-bold text-rose-700">⚠️ শিশু মুছে ফেলুন</h2>
            <p class="text-sm text-slate-500 mt-1">এই শিশুর সব খাবার তালিকা ও গ্রোথ রেকর্ড মুছে যাবে। এটি ফেরানো যাবে না।</p>
            <form method="POST" action="{{ route('children.destroy', $child) }}" class="mt-4"
                  onsubmit="return confirm('{{ $child->name }} মুছে ফেলতে চান?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 text-white font-medium hover:bg-rose-700 transition">
                    🗑️ মুছে ফেলুন
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
