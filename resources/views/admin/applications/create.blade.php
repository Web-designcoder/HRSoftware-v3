<x-layout>
    <x-breadcrumbs class="mb-2"
        :links="[
            'Applications' => route('admin.applications.index'),
            'Create' => '#'
        ]" />

    <form action="{{ route('admin.applications.store', $job) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- LEFT -->
            <div class="md:col-span-1 space-y-6">
                <!-- Job Summary -->
                <x-card>
                    <h3 class="font-semibold mb-4">Job Summary</h3>
                    <div class="text-sm text-slate-700 space-y-1">
                        <p><span class="font-medium">Title:</span> {{ $job->title }}</p>
                        <p><span class="font-medium">Employer:</span> {{ $job->employer?->company_name ?? '—' }}</p>
                        <p><span class="font-medium">Location:</span> {{ $job->city ?? $job->location ?? '—' }}</p>
                        <p><span class="font-medium">Posted:</span> {{ optional($job->date_posted)->format('d M Y') ?? '—' }}</p>
                    </div>
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
                            @error('cv')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center mb-2">
                                <x-label class="mb-0">Introduction Video</x-label>
                                <div class="relative group ml-2">ℹ️
                                    <div class="absolute left-6 top-0 hidden group-hover:block bg-slate-800 text-white text-xs rounded-md px-3 py-2 w-72 z-10">
                                        Optional 1–2 min video (MP4/MOV/AVI/MKV).
                                    </div>
                                </div>
                            </div>
                            <x-text-input type="file" name="video_intro" accept="video/*" />
                            @error('video_intro')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- RIGHT -->
            <div class="md:col-span-3">
                <x-card>
                    <div class="flex items-center mb-4">
                        <h3 class="font-semibold text-lg">Key Competency Questions</h3>
                        <div class="relative group ml-2">ℹ️
                            <div class="absolute left-6 top-0 hidden group-hover:block bg-slate-800 text-white text-xs rounded-md px-3 py-2 w-80 z-10">
                                Use STAR format: Situation, Task, Action, Result.
                            </div>
                        </div>
                    </div>

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
                                @error($field)
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <x-button class="w-full mt-6">Create Application</x-button>
                </x-card>
            </div>
        </div>
    </form>
</x-layout>
