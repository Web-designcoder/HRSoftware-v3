<x-layout :forcePublicLayout="request()->has('preview')">
    @php
        $user = auth()->user();
    @endphp

    <div class="flex justify-between items-center mb-4">
        <x-breadcrumbs :links="['Jobs' => route('jobs.index'), $job->title => '#']" />

        @auth
            @if($user->isAdmin() || $user->isConsultant())
                <div>
                    <a href="{{ url('/admin/jobs/' . $job->id . '/applications/create') }}"
                    class="inline-block px-4 py-2 bg-[#04215c] text-white rounded-md hover:bg-[#06318a] transition">
                        + Create Application
                    </a>

                    <a href="{{ route('admin.jobs.edit', $job) }}"
                    class="inline-block px-4 py-2 bg-[#04215c] text-white rounded-md hover:bg-[#06318a] transition">
                        ✏️ Edit Campaign
                    </a>
                </div>
            @endif
        @endauth

    </div>

    {{-- ====================== CANDIDATE VIEW ====================== --}}
    @if($user && $user->isCandidate())
        <div class="mx-auto grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- LEFT COLUMN (Sidebar Info) -->
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
                    @if($job->category)
                        <p class="text-sm text-gray-500 mt-1">{{ $job->category }}</p>
                    @endif
                </div>

                <!-- Job Details -->
                <div class="bg-white shadow rounded-lg p-6 text-sm text-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 mb-3">Job Details</h3>
                    <ul class="space-y-1">
                        <li><strong>Location:</strong> {{ $job->location ?? 'N/A' }}</li>
                        <li><strong>City:</strong> {{ $job->city ?? 'N/A' }}</li>
                        <li><strong>Country:</strong> {{ $job->country ?? 'N/A' }}</li>
                        <li><strong>Experience:</strong> {{ ucfirst($job->experience ?? 'Not specified') }}</li>
                        <li><strong>Salary:</strong> {{ $job->salary ? '$' . number_format($job->salary) : 'N/A' }}</li>
                        <li><strong>Managed By:</strong> {{ $job->managed_by ?? 'N/A' }}</li>
                        <li><strong>Date Posted:</strong> {{ $job->date_posted?->format('d M Y') ?? 'N/A' }}</li>
                    </ul>
                </div>

                <!-- Campaign Documents -->
                <div class="bg-white shadow rounded-lg p-6 text-sm text-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 mb-3">Campaign Documents</h3>
                    @if ($job->campaign_documents)
                        <a href="{{ asset('storage/' . $job->campaign_documents) }}" 
                           target="_blank"
                           class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                           📄 View Document
                        </a>
                    @else
                        <p class="text-gray-500">No documents available</p>
                    @endif
                </div>
            </div>

            <!-- RIGHT COLUMN (Main Content) -->
            <div class="lg:col-span-3 bg-white shadow rounded-lg p-8">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $job->title }}</h1>

                <!-- Assignment Overview -->
                <div class="prose prose-indigo max-w-none mb-8">
                    {!! $job->assignment_overview !!}
                </div>

                <!-- Apply + Back buttons -->
                <div class="flex flex-col sm:flex-row sm:justify-between gap-4">
                    <a href="{{ route('jobs.index') }}"
                       class="inline-block px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-100 transition">
                       ← Back to Jobs
                    </a>

                    @php
                        $existingApplication = \App\Models\JobApplication::query()
                            ->where('job_id', $job->id)
                            ->where('user_id', auth()->id())
                            ->latest('id')
                            ->first();
                    @endphp

                    @if($existingApplication)
                        <x-link-button :href="route('job.application.show', [$job, $existingApplication])">
                            View Your Application
                        </x-link-button>

                        <form action="{{ route('job.application.destroy', [$job, $existingApplication]) }}" method="POST" class="inline-block ml-2">
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" class="bg-red-500 hover:bg-red-600">
                                Withdraw Application
                            </x-button>
                        </form>
                    @else
                        @can('apply', $job)
                            <x-link-button :href="route('job.application.create', $job)">
                                Apply for this Job
                            </x-link-button>
                        @endcan
                    @endif
                </div>
            </div>
        </div>

        {{-- <!-- More Jobs by Same Employer -->
        @if ($job->employer && $job->employer->jobs->count() > 1)
            <div class="max-w-7xl mx-auto mt-10 bg-white shadow rounded-lg p-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">
                    More from {{ $job->employer->company_name }}
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($job->employer->jobs->where('id', '!=', $job->id)->take(4) as $otherJob)
                        <a href="{{ route('jobs.show', $otherJob) }}"
                           class="block border border-gray-200 rounded-md p-4 hover:shadow-md transition">
                            <h3 class="font-medium text-gray-800 mb-1">{{ $otherJob->title }}</h3>
                            <p class="text-sm text-gray-500 mb-1">
                                {{ $otherJob->city ?? 'Unknown Location' }}
                                @if($otherJob->salary)
                                    <span class="text-gray-400">·</span>
                                    ${{ number_format($otherJob->salary) }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-400">Posted {{ $otherJob->created_at->diffForHumans() }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif --}}

    {{-- ====================== EMPLOYER / ADMIN VIEW ====================== --}}
    @elseif($user && ($user->isEmployer() || $user->isAdmin()))
        <div class="mx-auto grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="space-y-6 lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6 text-center">
                    @if ($job->company_logo)
                        <img src="{{ asset('storage/' . $job->company_logo) }}" 
                             alt="Company Logo"
                             class="w-32 h-32 object-contain mx-auto mb-3 rounded-md border border-gray-200">
                    @endif
                    <h2 class="text-lg font-semibold text-gray-800">{{ $job->title }}</h2>
                </div>

                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-800">Candidate Map</h3>
                        <p class="text-xs text-gray-500">Map of candidates in {{ $job->city ?? 'this area' }}</p>
                    </div>
                    <div class="h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                        <span>🗺️ Map Placeholder</span>
                    </div>
                </div>

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

            <div class="lg:col-span-3 bg-white shadow rounded-lg p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $job->title }}</h1>

                <div x-data="{ tab: 'all' }">
                    <div class="flex border-b border-gray-200 mb-4">
                        <button @click="tab = 'all'"
                                :class="tab === 'all' ? 'text-[#04215c] border-[#04215c]' : 'text-gray-500 border-transparent'"
                                class="px-4 py-2 border-b-2 font-semibold text-sm focus:outline-none">
                            All Candidates
                        </button>
                    </div>

                    <div x-show="tab === 'all'" class="space-y-4">
                        @php
                            $candidates = $job->jobApplications()->with('user')->get();
                        @endphp

                        @if($candidates->count() > 0)
                            @foreach($candidates as $application)
                                <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg shadow-sm hover:bg-gray-100 transition">
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ $application->user->profile_picture 
                                                    ? asset('storage/' . $application->user->profile_picture) 
                                                    : asset('images/default-avatar.png') }}" 
                                             alt="Candidate" 
                                             class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover border border-gray-200">
                                        <div>
                                            <h3 class="font-semibold text-gray-800">{{ $application->user->name }}</h3>
                                            <p class="text-sm text-gray-500">Status: {{ ucfirst($application->status ?? 'Shortlist') }}</p>
                                        </div>
                                    </div>

                                    @if($user && ($user->isAdmin()))
                                    <a href="{{ route('admin.job.application.show', [$job, $application]) }}"
                                    @elseif ($user && ($user->isEmployer()))
                                    <a href="{{ route('employer.job.application.show', [$job, $application]) }}"
                                    @endif
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

    @endif
</x-layout>
