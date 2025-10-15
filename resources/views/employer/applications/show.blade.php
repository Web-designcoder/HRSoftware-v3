<x-layout>
    <x-breadcrumbs class="mb-4" :links="[
        'Jobs' => route('jobs.index'),
        $job->title => route('jobs.show', $job),
        'Candidate details' => '#'
    ]" />

    @php
        // Build safe display values
        $salutation = $application->salutation ?? $application->user->salutation ?? null;
        $fullName = trim(($application->first_name ?? '') . ' ' . ($application->last_name ?? ''));
        if ($fullName === '') {
            $fullName = $application->user->name ?? 'Candidate';
        }
        $initial = strtoupper(substr($fullName, 0, 1));

        // Try common avatar fields; fall back to null
        $avatarPath = $application->user->profile_picture
            ?? $application->user->avatar
            ?? $application->user->profile_photo_path
            ?? null;

        // Recruiter (assigned consultant) contact
        $recruiterName  = $job->consultant?->name ?? 'PHR Team';
        $recruiterEmail = $job->consultant?->email ?? null;
        $mailto = $recruiterEmail
            ? 'mailto:' . $recruiterEmail .
              '?subject=' . rawurlencode("Candidate Interest: {$fullName} for {$job->title}")
            : '#';
    @endphp

    <div class="mx-auto grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- LEFT COLUMN -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Candidate Photo -->
            <div class="bg-white shadow rounded-lg p-6 text-center">
                @if ($avatarPath)
                    <img src="{{ asset('storage/' . $avatarPath) }}"
                         alt="{{ $fullName }}"
                         class="w-28 h-28 md:w-32 md:h-32 rounded-full object-cover mx-auto mb-3 border border-gray-200" />
                @else
                    <div class="w-28 h-28 md:w-32 md:h-32 rounded-full mx-auto mb-3 flex items-center justify-center bg-gray-100 text-gray-500 border border-gray-200 text-2xl font-bold">
                        {{ $initial }}
                    </div>
                @endif
                <h2 class="text-lg font-semibold text-gray-800">Candidate details</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $salutation ? $salutation . ' ' : '' }}{{ $fullName }}
                </p>
            </div>

            <!-- Interested in this candidate -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-2">Interested in this candidate?</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Contact your recruiter to discuss next steps, interviews, or shortlisting.
                </p>
                <a href="{{ $mailto }}"
                   class="inline-block w-full text-center px-4 py-2 bg-[#04215c] text-white text-sm font-semibold rounded-md hover:bg-[#06318a] transition">
                    Contact recruiter{{ $recruiterName ? ' — ' . $recruiterName : '' }}
                </a>
            </div>

            <!-- Candidate introduction video -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800">Candidate introduction video</h3>
                </div>
                <div class="p-4">
                    @if (!empty($application->video_intro))
                        <video controls class="w-full rounded-md border border-gray-200">
                            <source src="{{ asset('storage/' . $application->video_intro) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <p class="text-sm text-gray-600">No video provided.</p>
                    @endif
                </div>
            </div>

            <!-- Candidate documents -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-3">Candidate documents</h3>
                @if (!empty($application->cv_path))
                    <a href="{{ asset('storage/' . $application->cv_path) }}"
                       target="_blank"
                       class="inline-block px-4 py-2 bg-[#04215c] text-white text-sm font-semibold rounded-md hover:bg-[#06318a] transition">
                        View CV / Resume
                    </a>
                @else
                    <p class="text-sm text-gray-600">No CV uploaded.</p>
                @endif
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="lg:col-span-3 bg-white shadow rounded-lg p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Candidate details</h1>
                    <p class="text-sm text-gray-500">
                        Applied: {{ optional($application->created_at)->format('d M Y') ?? 'N/A' }}
                        <span class="text-gray-300">·</span>
                        Status:
                        <span class="inline-block align-middle px-2 py-0.5 rounded-full text-xs font-semibold
                                     {{ ($application->status ?? 'pending') === 'shortlist' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($application->status ?? 'pending') }}
                        </span>
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('jobs.show', $job) }}"
                       class="inline-block px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-100 transition">
                       ← Back to Job
                    </a>
                </div>
            </div>

            <!-- Candidate Profile (no email/phone) -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3">Profile</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div>
                        <span class="block text-gray-500">Salutation</span>
                        <span class="font-medium">{{ $salutation ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500">Full name</span>
                        <span class="font-medium">{{ $fullName }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500">Location</span>
                        <span class="font-medium">
                            {{ $application->city ?? '—' }}
                            @if(!empty($application->postcode))
                                <span class="text-gray-400">·</span> {{ $application->postcode }}
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="block text-gray-500">Applied for</span>
                        <span class="font-medium">{{ $job->title }}</span>
                    </div>
                </div>
            </div>

            <!-- Key Competency Questions -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-800 mb-4">Key competency questions</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-3 rounded-md border border-gray-200 bg-white">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">Attention to detail</div>
                        <div class="text-sm text-gray-800">{{ $application->attention_to_detail ?? '—' }}</div>
                    </div>

                    <div class="p-3 rounded-md border border-gray-200 bg-white">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">Customer management</div>
                        <div class="text-sm text-gray-800">{{ $application->customer_management ?? '—' }}</div>
                    </div>

                    <div class="p-3 rounded-md border border-gray-200 bg-white">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">Market understanding</div>
                        <div class="text-sm text-gray-800">{{ $application->market_understanding ?? '—' }}</div>
                    </div>

                    <div class="p-3 rounded-md border border-gray-200 bg-white">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">Sales & business development</div>
                        <div class="text-sm text-gray-800">{{ $application->sales_and_business_development ?? '—' }}</div>
                    </div>

                    <div class="p-3 rounded-md border border-gray-200 bg-white">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">Ambition</div>
                        <div class="text-sm text-gray-800">{{ $application->ambition ?? '—' }}</div>
                    </div>

                    <div class="p-3 rounded-md border border-gray-200 bg-white">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">Leadership skills</div>
                        <div class="text-sm text-gray-800">{{ $application->leadership_skills ?? '—' }}</div>
                    </div>

                    <div class="p-3 rounded-md border border-gray-200 bg-white md:col-span-2">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">Risk assessment</div>
                        <div class="text-sm text-gray-800">{{ $application->risk_assessment ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
