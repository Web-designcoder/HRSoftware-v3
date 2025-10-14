<x-layout>
    <div class="container mx-auto py-6">
        <h1 class="text-2xl font-bold mb-4">Employer Dashboard</h1>
        <p>Welcome back, {{ auth()->user()->name }}!</p>

        <div id="dashboard" class="text-white">
            <!-- Column 1: Welcome Cards -->
            <div class="column column-2-3">
                <x-card class="text-slate-700 text-center flex justify-center flex-col items-center text-3xl">
                    <h2>{{ auth()->user()->employer->company_name ?? auth()->user()->name }}</h2>
                    @if(auth()->user()->employer)
                        <p class="text-sm text-slate-500">{{ auth()->user()->employer->industry ?? 'Employer' }}</p>
                    @endif
                </x-card>

                <x-card class="!bg-[#5ddfe6] flex justify-between flex-col p-8">
                    <h2 class="text-4xl uppercase">Welcome to your employer portal</h2>
                    <p class="text-3xl">Browse campaigns and connect with top candidates.</p>
                    <p class="italic text-xl text-right">- From the PHR Team</p>
                </x-card>
            </div>

            <!-- Column 2: Campaigns Section (2 columns wide) -->
            <div class="column" style="width: 49%;">
                <x-card class="!bg-gradient-to-br from-[#3b76c4] to-[#0cc0df] p-6 flex flex-col !h-[620px]">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">Available Campaigns</h2>
                        <a href="{{ route('jobs.index') }}" class="bg-white text-[#04215c] px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition text-sm flex items-center gap-2">
                            View All
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>

                    @php
                        $employerId = optional(auth()->user()->employer)->id;

                        $campaigns = \App\Models\Job::query()
                            ->with(['consultant', 'employer'])
                            ->withCount('jobApplications')
                            ->where('employer_id', $employerId)
                            ->latest()
                            ->take(4)
                            ->get();
                    @endphp

                    @if($campaigns->count() > 0)
                        <div class="flex flex-wrap gap-4 justify-center flex-1">
                            @foreach($campaigns as $campaign)
                                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition flex flex-col w-[48%]">
                                    <!-- Logo -->
                                    <div class="flex justify-center mb-3">
                                        @if($campaign->company_logo)
                                            <img src="{{ asset('storage/' . $campaign->company_logo) }}" alt="{{ $campaign->company }}" class="w-16 h-16 object-contain rounded">
                                        @else
                                            <div class="w-16 h-16 bg-white/20 rounded flex items-center justify-center text-2xl font-bold">
                                                {{ substr($campaign->company ?? 'C', 0, 1) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Job Title -->
                                    <h3 class="text-base font-bold mb-2 text-center">{{ $campaign->title }}</h3>

                                    {{-- <!-- Status -->
                                    <div class="mb-2 text-center">
                                        <span class="bg-white/20 px-3 py-1 rounded-full text-xs">
                                            {{ ucfirst($campaign->status ?? 'active') }}
                                        </span>
                                    </div> --}}

                                    <!-- Candidates Count -->
                                    <p class="text-xs mb-1 text-center opacity-90">
                                        <strong>{{ $campaign->job_applications_count }}</strong> Candidates
                                    </p>

                                    <!-- Managed By -->
                                    <p class="text-xs mb-3 text-center opacity-75">
                                        Managed by: {{ $campaign->consultant?->name ?? 'PHR Team' }}
                                    </p>

                                    <!-- Date Started -->
                                    <p class="text-xs text-center opacity-90 mb-1">
                                        Date Started: {{ optional($campaign->date_posted)->format('d M Y') ?? 'N/A' }}
                                    </p>

                                    <!-- View Button -->
                                    <a href="{{ route('jobs.show', $campaign) }}" class="mt-auto bg-white text-[#04215c] px-4 py-2 rounded-lg text-xs font-semibold hover:bg-blue-50 transition text-center">
                                        View Campaign
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 flex-1 flex items-center justify-center">
                            <div>
                                <p class="text-xl opacity-80">No campaigns available</p>
                                <p class="text-sm opacity-60 mt-2">Check back soon!</p>
                            </div>
                        </div>
                    @endif
                </x-card>
            </div>

            <!-- Column 3: Quick Stats & Consultant -->
            <div class="column column-2-3">
                <x-card class="!bg-gradient-to-br from-[#1025a1] to-[#3b76c4] p-6 flex flex-col justify-center">
                    <h2 class="text-2xl font-bold mb-4">Quick Stats</h2>
                    @if(auth()->user()->employer)
                        @php
                            $campaignCount = \App\Models\Job::where('employer_id', $employerId)->count();
                            $candidateCount = \App\Models\JobApplication::whereHas('job', function ($q) use ($employerId) {
                                $q->where('employer_id', $employerId);
                            })->distinct('user_id')->count('user_id');
                        @endphp
                        <div class="flex gap-4 justify-center">
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center hover:bg-white/20 transition flex-1">
                                <div class="text-4xl font-bold mb-1">{{ $campaignCount }}</div>
                                <div class="text-xs opacity-90">Campaigns</div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center hover:bg-white/20 transition flex-1">
                                <div class="text-4xl font-bold mb-1">{{ $candidateCount }}</div>
                                <div class="text-xs opacity-90">Candidates</div>
                            </div>
                        </div>
                    @endif
                </x-card>
                
                <x-card class="!p-0 overflow-hidden relative" style="background-image: url('/images/office.webp'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-black/40 flex flex-col justify-center items-center p-6 text-center">
                        <h2 class="text-2xl font-bold mb-4">Get in touch with a consultant!</h2>
                        <a href="#" class="bg-[#5ddfe6] text-slate-800 px-6 py-3 rounded-lg text-base font-semibold hover:bg-[#4dcdd4] transition">
                            Contact Us
                        </a>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-layout>
