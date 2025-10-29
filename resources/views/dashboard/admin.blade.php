<x-layout>
    <div class="container mx-auto py-6">
        <h1 class="text-2xl font-bold mb-4">
            {{ auth()->user()->isAdmin() ? 'Admin Dashboard' : 'Consultant Dashboard' }}
        </h1>
        <p>Welcome back, {{ auth()->user()->name }}!</p>

        <div id="dashboard" class="text-white flex flex-wrap gap-4">
            {{-- Column 1: Recent Applications --}}
            <div class="column" style="width: 49%;">
                <x-card class="!bg-white p-6 flex flex-col !h-[620px]">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold text-slate-900">Recent Applications</h2>
                    </div>

                    @if($applications->count() > 0)
                        <div class="flex-1 overflow-y-auto pr-1">
                            <ul class="space-y-3">
                                @foreach($applications as $application)
                                    <li class="border rounded-lg p-4 hover:bg-gray-50 transition">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex items-center gap-3">
                                                @php
                                                    $logo = $application->job?->company_logo;
                                                    $company = $application->job?->company ?? $application->job?->employer?->company_name;
                                                    $title = $application->job?->title;
                                                @endphp

                                                @if($logo)
                                                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ $company }}" class="w-10 h-10 object-contain rounded">
                                                @else
                                                    <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center text-sm font-semibold text-slate-700">
                                                        {{ substr($company ?? 'C', 0, 1) }}
                                                    </div>
                                                @endif

                                                <div>
                                                    <div class="font-semibold text-slate-900">
                                                        {{ $title ?? 'Untitled role' }}
                                                    </div>
                                                    <div class="text-xs text-slate-600">
                                                        @if($company)
                                                            {{ $company }}
                                                        @endif
                                                        @if($application->user?->name)
                                                            • {{ $application->user->name }}
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-slate-500 mt-1">
                                                        Applied {{ optional($application->created_at)->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                @php $status = $application->status; @endphp
                                                @if($status === 'accepted')
                                                    <span class="inline-block text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">Accepted</span>
                                                @elseif($status === 'rejected')
                                                    <span class="inline-block text-xs px-2 py-1 rounded-full bg-red-100 text-red-700">Rejected</span>
                                                @else
                                                    <span class="inline-block text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                                                @endif

                                                <div class="mt-2">
                                                    <a href="{{ route('admin.job.application.show', [$application->job?->id, $application->id]) }}"
                                                       class="text-xs underline text-slate-700 hover:text-slate-900">View job</a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-12 flex-1 flex items-center justify-center">
                            <div>
                                <p class="text-xl opacity-80 text-slate-900">No applications yet</p>
                                <p class="text-sm opacity-60 mt-2 text-slate-700">
                                    When candidates apply to your campaigns, they'll appear here.
                                </p>
                            </div>
                        </div>
                    @endif
                </x-card>
            </div>

            {{-- Column 2: Campaigns --}}
            <div class="column" style="width: 49%;">
                <x-card class="!bg-gradient-to-br from-[#3b76c4] to-[#0cc0df] p-6 flex flex-col !h-[620px]">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">Your Campaigns</h2>
                        <a href="{{ route('jobs.index') }}" class="bg-white text-[#04215c] px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition text-sm flex items-center gap-2">
                            View All
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>

                    @if($campaigns->count() > 0)
                        <div class="flex flex-wrap gap-4 justify-center flex-1">
                            @foreach($campaigns as $campaign)
                                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition flex flex-col w-[48%]">
                                    {{-- Logo --}}
                                    <div class="flex justify-center mb-3">
                                        @if($campaign->company_logo)
                                            <img src="{{ asset('storage/' . $campaign->company_logo) }}" alt="{{ $campaign->company ?? $campaign->employer?->company_name }}" class="w-16 h-16 object-contain rounded">
                                        @else
                                            <div class="w-16 h-16 bg-white/20 rounded flex items-center justify-center text-2xl font-bold">
                                                {{ substr($campaign->company ?? $campaign->employer?->company_name ?? 'C', 0, 1) }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Job Title --}}
                                    <h3 class="text-base font-bold mb-2 text-center">{{ $campaign->title }}</h3>

                                    {{-- Candidates Count --}}
                                    <p class="text-xs mb-1 text-center opacity-90">
                                        <strong>{{ $campaign->job_applications_count }}</strong> Candidates
                                    </p>

                                    {{-- Managed By --}}
                                    <p class="text-xs mb-3 text-center opacity-75">
                                        Managed by: {{ $campaign->consultant?->name ?? 'PHR Team' }}
                                    </p>

                                    {{-- Date Started --}}
                                    <p class="text-xs text-center opacity-90 mb-1">
                                        Date Started: {{ optional($campaign->date_posted)->format('d M Y') ?? 'N/A' }}
                                    </p>

                                    {{-- View Button --}}
                                    <a href="{{ route('admin.jobs.edit', $campaign) }}"
                                       class="mt-auto bg-white text-[#04215c] px-4 py-2 rounded-lg text-xs font-semibold hover:bg-blue-50 transition text-center">
                                        View Campaign
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 flex-1 flex items-center justify-center">
                            <div>
                                <p class="text-xl opacity-90">No campaigns to show</p>
                                <p class="text-sm opacity-80 mt-2">Create or get assigned to a campaign to see it here.</p>
                            </div>
                        </div>
                    @endif
                </x-card>
            </div>
        </div>
    </div>
</x-layout>