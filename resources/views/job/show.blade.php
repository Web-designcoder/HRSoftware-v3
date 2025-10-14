<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Jobs' => route('jobs.index'), $job->title => '#']" />

    <div class="mx-auto grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- LEFT COLUMN -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Company Logo -->
            <div class="bg-white shadow rounded-lg p-6 text-center">
                @if ($job->company_logo)
                    <img src="{{ asset('storage/' . $job->company_logo) }}" 
                         alt="Company Logo"
                         class="w-32 h-32 object-contain mx-auto mb-3 rounded-md border border-gray-200">
                @else
                    <div class="w-32 h-32 flex items-center justify-center mx-auto bg-gray-100 rounded-md border border-gray-200 text-gray-400 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 11c0-3.866-3.582-7-8-7m0 0a8 8 0 018 8 8 8 0 01-8 8m0 0a8 8 0 008-8" />
                        </svg>
                    </div>
                @endif
                <h2 class="text-lg font-semibold text-gray-800">{{ $job->title }}</h2>
            </div>

            <!-- Map Placeholder -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800">Candidate Map</h3>
                    <p class="text-xs text-gray-500">Map of candidates in {{ $job->city ?? 'this area' }}</p>
                </div>
                <div class="h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                    <span>🗺️ Map Placeholder</span>
                </div>
            </div>

            <!-- Campaign Documents -->
            <div class="bg-white shadow rounded-lg p-6 text-sm text-gray-700">
                <h3 class="text-base font-semibold text-gray-800 mb-3">Campaign Documents</h3>
                @if ($job->campaign_documents)
                    <a href="{{ asset('storage/' . $job->campaign_documents) }}" 
                       target="_blank"
                       class="inline-block px-4 py-2 bg-[#04215c] text-white rounded-md hover:bg-[#06318a] transition">
                       📄 View Document
                    </a>
                @else
                    <p class="text-gray-500">No documents available</p>
                @endif
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="lg:col-span-3 bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $job->title }}</h1>

            <!-- Tabs -->
            <div x-data="{ tab: 'all' }">
                <div class="flex border-b border-gray-200 mb-4">
                    <button @click="tab = 'all'"
                            :class="tab === 'all' ? 'text-[#04215c] border-[#04215c]' : 'text-gray-500 border-transparent'"
                            class="px-4 py-2 border-b-2 font-semibold text-sm focus:outline-none">
                        All
                    </button>
                </div>

                <!-- All Candidates Tab -->
                <div x-show="tab === 'all'" class="space-y-4">
                    @php
                        $candidates = $job->jobApplications()->with('user')->get();
                    @endphp

                    @if($candidates->count() > 0)
                        @foreach($candidates as $application)
                            <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg shadow-sm hover:bg-gray-100 transition">
                                <!-- Candidate Info -->
                                <div class="flex items-center space-x-4">
                                    <img src="{{ $application->user->profile_picture 
                                                ? asset('storage/' . $application->user->profile_picture) 
                                                : asset('images/default-avatar.png') }}" 
                                         alt="Candidate" 
                                         class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover border border-gray-200">
                                    <div>
                                        <h3 class="font-semibold text-gray-800">{{ $application->user->name }}</h3>
                                        <p class="text-sm text-gray-500">Status: Shortlist</p>
                                    </div>
                                </div>

                                <!-- View Button -->
                                <a href="#"
                                   class="inline-block px-4 py-2 bg-[#04215c] text-white text-sm rounded-md hover:bg-[#06318a] transition">
                                    View Candidate
                                </a>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-center py-8">No candidates have applied yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>
