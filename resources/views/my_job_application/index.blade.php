<x-layout>
    <x-breadcrumbs :links="['My Applications' => '#']" class="mb-4"/>

    <div class="mx-auto">
        @if ($applications->isNotEmpty())
            <div class="flex flex-wrap gap-6">
                @foreach ($applications as $application)
                    <div class="bg-white shadow rounded-lg p-4 flex flex-col items-center text-center transition hover:shadow-lg w-full sm:w-[calc(50%-0.75rem)] lg:w-[calc(25%-1.125rem)]">
                        <!-- Company Logo -->
                        <div class="w-24 h-24 mb-3">
                            @if ($application->job->company_logo)
                                <img src="{{ asset('storage/' . $application->job->company_logo) }}" alt="Logo"
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

                        <!-- Job Title -->
                        <a href="{{ route('job.application.show', [$application->job, $application]) }}" 
                           class="font-semibold text-lg text-gray-800 mb-2 line-clamp-2 hover:text-indigo-600 transition">
                            {{ $application->job->title }}
                        </a>

                        <!-- Company Name -->
                        <p class="text-sm text-gray-500 mb-2">
                            {{ $application->job->employer->company_name }}
                        </p>

                        <!-- Applied Date -->
                        <p class="text-xs text-gray-400 mb-3">
                            Applied {{ $application->created_at->diffForHumans() }}
                        </p>

                        <!-- Status Badge -->
                        <div class="mb-4">
                            @if($application->status === 'pending')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>
                            @elseif($application->status === 'reviewing')
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Reviewing</span>
                            @elseif($application->status === 'accepted')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Accepted</span>
                            @elseif($application->status === 'rejected')
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rejected</span>
                            @elseif($application->status === 'withdrawn')
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Withdrawn</span>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="mt-auto w-full space-y-2">
                            <a href="{{ route('job.application.show', [$application->job, $application]) }}"
                               class="inline-block w-full px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                               View Application
                            </a>

                            @if($application->status === 'pending' || $application->status === 'reviewing')
                                <form action="{{ route('my-job-applications.destroy', $application) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-600 transition">
                                        Withdraw
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-md border border-dashed border-slate-300 p-8">
                <div class="text-center font-medium">
                    No applications yet
                </div>
                <div class="text-center">
                    Browse jobs and apply <a href="{{route('jobs.index')}}" class="text-indigo-500 hover:underline">Here!</a>
                </div>
            </div>
        @endif
    </div>
</x-layout>