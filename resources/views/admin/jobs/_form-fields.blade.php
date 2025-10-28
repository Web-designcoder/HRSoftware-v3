<!-- Shared form fields for admin/jobs create & edit -->
@php
    // Ensure $job always exists (avoid undefined variable in create view)
    $job = $job ?? new \App\Models\Job;
@endphp

<!-- Title -->
<div>
    <label class="block text-sm font-medium text-gray-700">Job Title</label>
    <input type="text" name="title" value="{{ old('title', $job->title ?? '') }}" required
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#04215c] focus:border-[#04215c]" />
</div>

<!-- Location -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Location</label>
        <input type="text" name="location" value="{{ old('location', $job->location ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#04215c] focus:border-[#04215c]" />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">City</label>
        <input type="text" name="city" value="{{ old('city', $job->city ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#04215c] focus:border-[#04215c]" />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Country</label>
        <input type="text" name="country" value="{{ old('country', $job->country ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#04215c] focus:border-[#04215c]" />
    </div>
</div>

<!-- Salary -->
<div>
    <label class="block text-sm font-medium text-gray-700">Salary</label>
    <input type="number" name="salary" value="{{ old('salary', $job->salary ?? '') }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#04215c] focus:border-[#04215c]" />
</div>

<!-- Experience -->
<div>
    <label class="block text-sm font-medium text-gray-700">Experience Level</label>
    <select name="experience"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#04215c] focus:border-[#04215c]">
        <option value="">Select Experience</option>
        @foreach (\App\Models\Job::$experience as $level)
            <option value="{{ $level }}" {{ old('experience', $job->experience ?? '') == $level ? 'selected' : '' }}>
                {{ ucfirst($level) }}
            </option>
        @endforeach
    </select>
</div>

<!-- Category -->
<div>
    <label class="block text-sm font-medium text-gray-700">Category</label>
    <select name="category"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#04215c] focus:border-[#04215c]">
        <option value="">Select Category</option>
        @foreach (\App\Models\Job::$category as $cat)
            <option value="{{ $cat }}" {{ old('category', $job->category ?? '') == $cat ? 'selected' : '' }}>
                {{ $cat }}
            </option>
        @endforeach
    </select>
</div>

<!-- Employer -->
<div>
    <label class="block text-sm font-medium text-gray-700">Assign Employer (Company)</label>
    <select name="employer_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#04215c] focus:border-[#04215c]">
        <option value="">Select Employer</option>
        @foreach($employers as $employer)
            <option value="{{ $employer->id }}"
                @selected(old('employer_id', $job->employer_id ?? null) == $employer->id)>
                {{ $employer->name }} — {{ $employer->city }}, {{ $employer->country }}
            </option>
        @endforeach
    </select>
    @error('employer_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<!-- Consultant -->
<div>
    <label class="block text-sm font-medium text-gray-700">Assign Consultant</label>
    <select name="consultant_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#04215c] focus:border-[#04215c]">
        <option value="">Select Consultant</option>
        @foreach($consultants as $consultant)
            <option value="{{ $consultant->id }}"
                @selected(old('consultant_id', $job->consultant_id ?? null) == $consultant->id)>
                {{ $consultant->first_name }} {{ $consultant->last_name }} ({{ $consultant->email }})
            </option>
        @endforeach
    </select>
</div>

<!-- Candidates -->
<div>
    <label class="block text-sm font-medium text-gray-700">Assign Candidates (who can see this job)</label>
    @php
        $selected = old('candidate_ids', isset($job) ? $job->assignedCandidates->pluck('id')->toArray() : []);
    @endphp
    <select name="candidate_ids[]" multiple
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#04215c] focus:border-[#04215c] h-40">
        @foreach($candidates as $candidate)
            <option value="{{ $candidate->id }}" @selected(in_array($candidate->id, $selected))>
                {{ $candidate->first_name }} {{ $candidate->last_name }} ({{ $candidate->email }})
            </option>
        @endforeach
    </select>
    <small class="text-gray-500 text-sm">Hold Ctrl (or Cmd) to select multiple candidates.</small>
</div>

<!-- Date Posted -->
<div>
    <label class="block text-sm font-medium text-gray-700">Date Posted</label>
    <input type="date" name="date_posted" value="{{ old('date_posted', optional($job->date_posted)->format('Y-m-d')) }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#04215c] focus:border-[#04215c]" />
</div>

<!-- Managed By -->
<div>
    <label class="block text-sm font-medium text-gray-700">Managed By</label>
    <input type="text" name="managed_by" value="{{ old('managed_by', $job->managed_by ?? '') }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#04215c] focus:border-[#04215c]" />
</div>

<!-- Assignment Overview -->
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Assignment Overview</label>
    <textarea name="assignment_overview" id="assignment_overview" rows="10"
        class="w-full rounded-md border-gray-300 shadow-sm focus:ring-[#04215c] focus:border-[#04215c]">{{ old('assignment_overview', $job->assignment_overview ?? '') }}</textarea>
</div>

<!-- Company Logo -->
<div>
    <label class="block text-sm font-medium text-gray-700">Company Logo</label>
    @if (!empty($job->company_logo))
        <div class="mb-2">
            <img src="{{ asset('storage/' . $job->company_logo) }}" alt="Logo" class="h-16 rounded">
        </div>
    @endif
    <input type="file" name="company_logo"
        class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer focus:outline-none" />
</div>

<!-- Campaign Documents -->
<div>
    <label class="block text-sm font-medium text-gray-700">Campaign Documents</label>
    @if (!empty($job->campaign_documents))
        <p class="mb-2">
            <a href="{{ asset('storage/' . $job->campaign_documents) }}" target="_blank" class="text-[#04215c] underline">
                View Current Document
            </a>
        </p>
    @endif
    <input type="file" name="campaign_documents"
        class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer focus:outline-none" />
</div>
