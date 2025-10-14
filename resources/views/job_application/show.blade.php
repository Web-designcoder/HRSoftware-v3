<x-layout>
    <x-breadcrumbs class="mb-2" 
        :links="[
            'Jobs' => route('jobs.index'), 
            $job->title => route('jobs.show', $job), 
            'Application' => '#'
        ]" />

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="md:col-span-1 space-y-6">
            <x-card>
                <h3 class="font-semibold mb-4">Candidate Details</h3>
                <p><strong>First Name:</strong> {{ $application->first_name }}</p>
                <p><strong>Last Name:</strong> {{ $application->last_name }}</p>
                <p><strong>City:</strong> {{ $application->city }}</p>
                <p><strong>Postcode:</strong> {{ $application->postcode }}</p>
            </x-card>

            <x-card>
                <h3 class="font-semibold mb-4">Application Status</h3>
                @if($application->status === 'pending')
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">Pending Review</span>
                @elseif($application->status === 'reviewing')
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">Under Review</span>
                @elseif($application->status === 'accepted')
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Accepted</span>
                @elseif($application->status === 'rejected')
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">Rejected</span>
                @elseif($application->status === 'withdrawn')
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">Withdrawn</span>
                @endif
            </x-card>

            <x-card>
                <h3 class="font-semibold mb-4">Introduction Video</h3>
                @if ($application->video_intro)
                    <video controls class="w-full rounded-md">
                        <source src="{{ Storage::url($application->video_intro) }}" type="video/mp4">
                    </video>
                @else
                    <p class="text-slate-500 text-sm">No video uploaded.</p>
                @endif
            </x-card>

            <x-card>
                <h3 class="font-semibold mb-4">CV</h3>
                @if ($application->cv_path)
                    <a href="{{ Storage::url($application->cv_path) }}" target="_blank" class="text-indigo-600 hover:underline text-sm">Download CV</a>
                @else
                    <p class="text-slate-500 text-sm">No CV uploaded.</p>
                @endif
            </x-card>
        </div>

        <div class="md:col-span-3">
            <x-card>
                <h3 class="font-semibold mb-4">Key Competency Responses</h3>
                @foreach ([
                    'attention_to_detail' => 'Attention to Detail',
                    'customer_management' => 'Customer Management',
                    'market_understanding' => 'Market Understanding',
                    'sales_and_business_development' => 'Sales and Business Development',
                    'ambition' => 'Ambition',
                    'leadership_skills' => 'Leadership Skills',
                    'risk_assessment' => 'Risk Assessment'
                ] as $name => $label)
                    <div class="mb-5">
                        <x-label>{{ $label }}</x-label>
                        <p class="text-sm text-slate-700 whitespace-pre-line border rounded-md p-3 bg-slate-50">
                            {{ $application->$name ?: '—' }}
                        </p>
                    </div>
                @endforeach

                {{-- Only show withdraw button if application is still active --}}
                @if (in_array($application->status, ['pending', 'reviewing']))
                    <form action="{{ route('job.application.destroy', [$job, $application]) }}" method="POST" class="mt-6">
                        @csrf
                        @method('DELETE')
                        <x-button class="bg-red-600 hover:bg-red-700 w-full">Withdraw Application</x-button>
                    </form>
                @elseif($application->status === 'withdrawn')
                    <p class="text-center text-slate-500 mt-4 italic">This application has been withdrawn.</p>
                @elseif($application->status === 'rejected')
                    <p class="text-center text-slate-500 mt-4 italic">Unfortunately, this application was not successful.</p>
                @elseif($application->status === 'accepted')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4 text-center">
                        <p class="text-green-800 font-medium">🎉 Congratulations! Your application has been accepted.</p>
                        <p class="text-green-600 text-sm mt-1">The employer will contact you soon.</p>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-layout>