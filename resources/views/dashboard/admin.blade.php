<x-layout>
    <x-breadcrumbs :links="['My Applications' => '#']" class="mb-4"/>

    @forelse ($applications as $application)
        <x-card class="mb-4">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h2 class="text-lg font-medium">{{ $application->job->title }}</h2>
                    <div class="text-slate-500 text-sm mt-1">
                        {{ $application->job->employer->company_name }}
                    </div>
                    <div class="text-slate-500 text-xs mt-1">
                        Applied {{ $application->created_at->diffForHumans() }}
                    </div>
                    
                    <!-- Status Badge -->
                    <div class="mt-2">
                        @if($application->status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Pending</span>
                        @elseif($application->status === 'reviewing')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Reviewing</span>
                        @elseif($application->status === 'accepted')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Accepted</span>
                        @elseif($application->status === 'rejected')
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Rejected</span>
                        @elseif($application->status === 'withdrawn')
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Withdrawn</span>
                        @endif
                    </div>
                </div>

                <div class="flex space-x-2">
                    <x-link-button :href="route('job.application.show', [$application->job, $application])">
                        View
                    </x-link-button>

                    @if($application->status === 'pending' || $application->status === 'reviewing')
                        <form action="{{ route('my-job-applications.destroy', $application) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-button class="bg-red-500 hover:bg-red-600">Withdraw</x-button>
                        </form>
                    @endif
                </div>
            </div>
        </x-card>
    @empty
        <div class="rounded-md border border-dashed border-slate-300 p-8">
            <div class="text-center font-medium">
                No applications yet
            </div>
            <div class="text-center">
                Browse jobs and apply <a href="{{route('jobs.index')}}" class="text-indigo-500 hover:underline">Here!</a>
            </div>
        </div>
    @endforelse
</x-layout>