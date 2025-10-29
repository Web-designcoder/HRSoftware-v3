<x-layout>
    <div class="max-w-7xl mx-auto mt-8">
        {{-- ───────────────────────────────
             HEADER BAR
        ─────────────────────────────── --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-[#04215c]">
                Edit Campaign — {{ $job->title }}
            </h1>

            <div class="flex gap-3">
                <a href="{{ route('jobs.show', $job) }}?preview=1"
                    class="px-4 py-2 bg-[#04215c] text-white rounded-md hover:bg-[#06318a] transition">
                        View as Contact
                </a>

                <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                        Delete Campaign
                    </button>
                </form>
            </div>
        </div>

        {{-- ───────────────────────────────
             TAB NAVIGATION
        ─────────────────────────────── --}}
        <div x-data="{ tab: 'details' }">
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex flex-wrap gap-2">
                    <button @click="tab='details'"
                        :class="tab==='details' ? 'border-[#04215c] text-[#04215c]' : 'border-transparent text-gray-500 hover:text-[#04215c]'"
                        class="whitespace-nowrap px-4 py-2 text-sm font-medium border-b-2 transition">
                        Campaign Details
                    </button>

                    <button @click="tab='candidates'"
                        :class="tab==='candidates' ? 'border-[#04215c] text-[#04215c]' : 'border-transparent text-gray-500 hover:text-[#04215c]'"
                        class="whitespace-nowrap px-4 py-2 text-sm font-medium border-b-2 transition">
                        Campaign Candidates
                    </button>

                    <button @click="tab='scheduler'"
                        :class="tab==='scheduler' ? 'border-[#04215c] text-[#04215c]' : 'border-transparent text-gray-500 hover:text-[#04215c]'"
                        class="whitespace-nowrap px-4 py-2 text-sm font-medium border-b-2 transition">
                        Interview Scheduler
                    </button>

                    <button @click="tab='settings'"
                        :class="tab==='settings' ? 'border-[#04215c] text-[#04215c]' : 'border-transparent text-gray-500 hover:text-[#04215c]'"
                        class="whitespace-nowrap px-4 py-2 text-sm font-medium border-b-2 transition">
                        Campaign Settings
                    </button>

                    <button @click="tab='modules'"
                        :class="tab==='modules' ? 'border-[#04215c] text-[#04215c]' : 'border-transparent text-gray-500 hover:text-[#04215c]'"
                        class="whitespace-nowrap px-4 py-2 text-sm font-medium border-b-2 transition">
                        Module Management
                    </button>
                </nav>
            </div>

            {{-- ───────────────────────────────
                 TAB CONTENT
            ─────────────────────────────── --}}
            <div x-show="tab === 'details'" x-cloak>
                @include('admin.jobs.tabs.campaign-details')
            </div>

            <div x-show="tab === 'candidates'" x-cloak>
                @include('admin.jobs.tabs.campaign-candidates')
            </div>

            <div x-show="tab === 'scheduler'" x-cloak>
                @include('admin.jobs.tabs.interview-scheduler')
            </div>

            <div x-show="tab === 'settings'" x-cloak>
                @include('admin.jobs.tabs.campaign-settings')
            </div>

            <div x-show="tab === 'modules'" x-cloak>
                @include('admin.jobs.tabs.module-management')
            </div>
        </div>
    </div>

    {{-- Alpine.js for tab switching --}}
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <script>
    window.toast = function (msg, type = 'success') {
        const div = document.createElement('div');
        div.textContent = msg;
        div.className = `fixed bottom-5 right-5 px-4 py-2 rounded-md text-white shadow-lg z-50 ${
            type === 'error' ? 'bg-red-600' : 'bg-green-600'
        }`;
        document.body.appendChild(div);
        setTimeout(() => div.remove(), 2500);
    };
    </script>

</x-layout>
