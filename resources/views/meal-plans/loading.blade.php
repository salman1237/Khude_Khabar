<x-app-layout>
    <div
        class="max-w-lg mx-auto text-center py-10"
        x-data="mealPlanLoader({
            statusUrl: '{{ route('meal-plans.status', $mealPlan) }}',
            channel: 'meal-plans.{{ $mealPlan->id }}',
        })"
        x-init="init()"
    >
        <div class="card p-10">
            <!-- Cooking animation -->
            <div class="text-7xl animate-bounce">🍳</div>

            <h1 class="font-display text-2xl font-bold text-slate-800 mt-6">
                {{ $child->name }}-এর খাবার তালিকা তৈরি হচ্ছে…
            </h1>
            <p class="text-slate-500 mt-2" x-text="message">
                বয়স, বাজেট ও পুষ্টির চাহিদা অনুযায়ী সাপ্তাহিক মেনু সাজানো হচ্ছে।
            </p>

            <!-- Progress bar -->
            <div class="mt-6 h-2 rounded-full bg-brand-sand overflow-hidden">
                <div class="h-full bg-brand-green rounded-full transition-all duration-700" :style="`width: ${progress}%`"></div>
            </div>

            <div class="mt-6 flex items-center justify-center gap-2 text-sm text-slate-400">
                <span class="inline-block w-2 h-2 rounded-full bg-brand-green animate-pulse"></span>
                একটু অপেক্ষা করুন…
            </div>

            <template x-if="failed">
                <div class="mt-6">
                    <p class="text-rose-600 text-sm">দুঃখিত, তালিকা তৈরিতে সমস্যা হয়েছে।</p>
                    <a href="{{ route('dashboard') }}" class="btn-ghost mt-3">ড্যাশবোর্ডে ফিরে যান</a>
                </div>
            </template>
        </div>
    </div>

    @push('scripts')
    @endpush

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mealPlanLoader', ({ statusUrl, channel }) => ({
                progress: 12,
                message: 'বয়স, বাজেট ও পুষ্টির চাহিদা অনুযায়ী সাপ্তাহিক মেনু সাজানো হচ্ছে।',
                failed: false,
                timer: null,
                ticker: null,

                init() {
                    // Gentle fake progress so the bar feels alive.
                    this.ticker = setInterval(() => {
                        if (this.progress < 90) this.progress += Math.random() * 6;
                    }, 800);

                    // Real-time path via Echo/Pusher (if configured).
                    if (window.Echo) {
                        window.Echo.channel(channel).listen('.ready', (e) => {
                            this.go(e.redirect_url);
                        });
                    }

                    // Polling fallback (works without Pusher credentials).
                    this.poll();
                    this.timer = setInterval(() => this.poll(), 3000);
                },

                async poll() {
                    try {
                        const res = await window.axios.get(statusUrl);
                        if (res.data.status === 'completed' && res.data.redirect_url) {
                            this.go(res.data.redirect_url);
                        } else if (res.data.status === 'failed') {
                            this.failed = true;
                            this.message = 'তালিকা তৈরি ব্যর্থ হয়েছে।';
                            this.cleanup();
                        }
                    } catch (err) {
                        // Ignore transient errors; keep polling.
                    }
                },

                go(url) {
                    this.progress = 100;
                    this.message = 'প্রস্তুত! নিয়ে যাওয়া হচ্ছে…';
                    this.cleanup();
                    setTimeout(() => window.location.assign(url), 500);
                },

                cleanup() {
                    clearInterval(this.timer);
                    clearInterval(this.ticker);
                },
            }));
        });
    </script>
</x-app-layout>
