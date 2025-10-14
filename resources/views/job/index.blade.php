<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Jobs' => route('jobs.index')]" />

    <div class="mx-auto">
        <!-- Filters -->
        <x-card class="mb-6 text-sm">
            <form id="filtering-form" action="{{ route('jobs.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block font-semibold mb-1">Search</label>
                        <x-text-input name="search" value="{{ request('search') }}" placeholder="Search for any text" />
                    </div>

                    <!-- Salary -->
                    <div>
                        <label class="block font-semibold mb-1">Salary</label>
                        <div class="flex space-x-2">
                            <x-text-input name="min_salary" value="{{ request('min_salary') }}" placeholder="From" />
                            <x-text-input name="max_salary" value="{{ request('max_salary') }}" placeholder="To" />
                        </div>
                    </div>

                    <!-- Experience -->
                    <div>
                        <label class="block font-semibold mb-1">Experience</label>
                        <x-radio-group name="experience"
                            :options="array_combine(array_map('ucfirst', \App\Models\Job::$experience), \App\Models\Job::$experience)" />
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block font-semibold mb-1">Category</label>
                        <x-radio-group name="category" :options="\App\Models\Job::$category" />
                    </div>
                </div>

                <div>
                    <x-button class="w-full md:w-auto">Filter</x-button>
                </div>
            </form>
        </x-card>

        <!-- Job Grid -->
        @if ($jobs->isEmpty())
            <p class="text-center text-gray-600">No jobs found.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($jobs as $job)
                    <div class="bg-white shadow rounded-lg p-4 flex flex-col items-center text-center transition hover:shadow-lg">
                        <!-- Company Logo -->
                        <div class="w-24 h-24 mb-3">
                            @if ($job->company_logo)
                                <img src="{{ asset('storage/' . $job->company_logo) }}" alt="Logo"
                                     class="w-full h-full object-contain rounded-md border border-gray-200">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100 rounded-md border border-gray-200 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 11c0-3.866-3.582-7-8-7m0 0a8 8 0 018 8 8 8 0 01-8 8m0 0a8 8 0 008-8" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Title -->
                        <h3 class="font-semibold text-lg text-gray-800 mb-2 line-clamp-2">
                            {{ $job->title }}
                        </h3>

                        <!-- Candidates Count -->
                        <p class="text-xs mb-1 text-gray-600">
                            <strong>{{ $job->jobApplications()->count() }}</strong> Candidates
                        </p>

                        <!-- Managed By -->
                        <p class="text-xs mb-1 text-gray-500">
                            Managed by: {{ $job->consultant?->name ?? 'PHR Team' }}
                        </p>

                        <!-- Date Started -->
                        <p class="text-xs text-gray-500 mb-4">
                            Date Started: {{ optional($job->date_posted)->format('d M Y') ?? 'N/A' }}
                        </p>

                        <!-- Action Button -->
                        <div class="mt-auto w-full">
                            @if(auth()->user()->isCandidate())
                                @if($job->hasUserApplied(auth()->user()))
                                    <span class="inline-block w-full px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-md">
                                        Applied
                                    </span>
                                @else
                                    <a href="{{ route('job.application.create', $job) }}"
                                       class="inline-block w-full px-4 py-2 bg-[#04215c] text-white text-sm font-medium rounded-md transition hover:bg-[#06318a]">
                                       Apply Now
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('jobs.show', $job) }}"
                                   class="inline-block w-full px-4 py-2 bg-[#04215c] text-white text-sm font-medium rounded-md transition hover:bg-[#06318a]">
                                   View Campaign
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $jobs->links() }}
            </div>
        @endif
    </div>
</x-layout>
