<x-layout>
    <x-breadcrumbs class="mb-2"
        :links="[
            'Applications' => route('admin.applications.index'),
            'Create New Application' => '#'
        ]" />

    <form action="{{ route('admin.applications.storeStandalone') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- LEFT -->
            <div class="md:col-span-1 space-y-6">
                <!-- Job Selection -->
                <x-card>
                    <h3 class="font-semibold mb-4">Select Job Campaign</h3>
                    <select name="job_id"
                            class="w-full border-slate-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Select a Job --</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job->id }}" {{ old('job_id') == $job->id ? 'selected' : '' }}>
                                {{ $job->title }} — {{ $job->employer?->company_name ?? 'Unknown Employer' }}
                            </option>
                        @endforeach
                    </select>
                    @error('job_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </x-card>

                <!-- Candidate Selection -->
                <x-card>
                    <h3 class="font-semibold mb-4">Select Candidate</h3>
                    <select name="user_id"
                            class="w-full border-slate-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Select a Candidate --</option>
                        @foreach($candidates as $c)
                            <option value="{{ $c->id }}" {{ old('user_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </x-card>

                <!-- Uploads -->
                <x-card>
                    <h3 class="font-semibold mb-2">Attachments</h3>
                    <div class="space-y-4">
                        <div>
                            <x-label for="cv">CV / Resume</x-label>
                            <x-text-input type="file" name="cv" />
                        </div>
                        <div>
                            <x-label for="video_intro">Introduction Video (optional)</x-label>
                            <x-text-input type="file" name="video_intro" accept="video/*" />
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- RIGHT -->
            <div class="md:col-span-3">
                <x-card>
                    <h3 class="font-semibold text-lg mb-4">Key Competency Questions</h3>
                    <div class="space-y-5">
                        @foreach([
                            'attention_to_detail' => 'Describe at least one example when you failed and what you learned.',
                            'customer_management' => 'An occasion when customer needs conflicted with business goals.',
                            'market_understanding' => 'Would you describe your skill set as more technical or commercial?',
                            'sales_and_business_development' => 'Why do people buy from you and continue to buy?',
                            'ambition' => 'Explain, in a few sentences, why you excel in this area.',
                            'leadership_skills' => 'How important is leadership to the success of your work?',
                            'risk_assessment' => 'An example when you failed in this area and what you learned.'
                        ] as $field => $prompt)
                            <div>
                                <x-label for="{{ $field }}">{{ ucwords(str_replace('_', ' ', $field)) }}</x-label>
                                <p class="text-xs text-slate-500 mb-1">{{ $prompt }}</p>
                                <textarea name="{{ $field }}" rows="3"
                                          class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old($field) }}</textarea>
                            </div>
                        @endforeach
                    </div>
                    <x-button class="w-full mt-6">Create Application</x-button>
                </x-card>
            </div>
        </div>
    </form>
</x-layout>
