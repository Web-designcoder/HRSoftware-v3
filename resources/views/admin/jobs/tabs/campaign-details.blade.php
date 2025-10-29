<x-card title="Campaign Details">
    <div
        x-data='campaignDetails({
            detailsUrl: "{{ route('admin.jobs.details.update', $job) }}",
            overviewsUrl: "{{ route('admin.jobs.overviews.update', $job) }}",
            logoUrl: "{{ route('admin.jobs.logo.upload', $job) }}",
            employerVideoUploadUrl: "{{ route('admin.jobs.video.employer.upload', $job) }}",
            employerVideoDeleteUrl: "{{ route('admin.jobs.video.employer.delete', $job) }}",
            {{-- candidateVideoUploadUrl: "{{ route('admin.jobs.video.candidate.upload', $job) }}",
            candidateVideoDeleteUrl: "{{ route('admin.jobs.video.candidate.delete', $job) }}", --}}
            csrf: "{{ csrf_token() }}",
            job: {!! json_encode($job->only(['title','location','city','country','managed_by','date_posted','salary','experience','category','employer_id'])) !!},
            logo: "{{ $job->company_logo_url }}",
            background: {!! json_encode($job->description ?? '') !!},
            assignment: {!! json_encode($job->assignment_overview ?? '') !!},
            employerVideo: "{{ $job->employer_intro_video_url }}",
            candidateVideo: "{{ $job->candidate_assessment_video_url }}"
        })'
        class="grid grid-cols-1 lg:grid-cols-3 gap-6"
    >

        {{-- LEFT COLUMN --}}
        <div class="space-y-6">
            @include('admin.jobs.tabs.partials.manage-logo', ['job' => $job])
            @include('admin.jobs.tabs.partials.campaign-status', ['job' => $job])
            @include('admin.jobs.tabs.partials.candidate-video', ['job' => $job])
            @include('admin.jobs.tabs.partials.employer-video', ['job' => $job])
            @include('admin.jobs.tabs.partials.campaign-documents', ['job' => $job])
            @include('admin.jobs.tabs.partials.required-docs', ['job' => $job])
            @include('admin.jobs.tabs.partials.questions', ['job' => $job])
        </div>

        {{-- MIDDLE COLUMN --}}
        <div class="space-y-6">
            @include('admin.jobs.tabs.partials.campaign-form', ['job' => $job, 'employers' => $employers])
            @include('admin.jobs.tabs.partials.mcquaig')
            @include('admin.jobs.tabs.partials.terms', ['job' => $job])
            @include('admin.jobs.tabs.partials.background', ['job' => $job])
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="space-y-6">
            @include('admin.jobs.tabs.partials.employer-contacts', ['job' => $job])
            @include('admin.jobs.tabs.partials.fees')
        </div>
    </div>
</x-card>
