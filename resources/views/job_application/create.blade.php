{{-- File: resources/views/job_application/create.blade.php --}}
<x-layout>
    <x-breadcrumbs class="mb-2" 
        :links="[
            'Jobs' => route('jobs.index'), 
            $job->title => route('jobs.show', $job), 
            'Apply' => '#'
        ]" />

    <form action="{{ route('job.application.store', $job) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            <!-- LEFT -->
            <div class="md:col-span-1 space-y-6">
                <!-- Candidate Details -->
                <x-card>
                    <h3 class="font-semibold mb-4">Candidate Details</h3>
                    <div class="space-y-3">
                        <x-label>First Name</x-label>
                        <x-text-input name="first_name" :value="$user->first_name" readonly />

                        <x-label>Last Name</x-label>
                        <x-text-input name="last_name" :value="$user->last_name" readonly />

                        <x-label>City</x-label>
                        <x-text-input name="city" :value="$user->city" readonly />

                        <x-label>Postcode</x-label>
                        <x-text-input name="postcode" :value="$user->postcode" readonly />
                    </div>
                </x-card>

                <!-- Candidate Introduction Video -->
                <x-card>
                    <div class="flex items-center mb-4">
                        <h3 class="font-semibold text-base">Candidate Introduction Video</h3>
                        <!-- Hover tooltip icon -->
                        <div class="relative group ml-2">
                            ℹ️
                            <div class="absolute left-6 top-0 hidden group-hover:block bg-slate-800 text-white text-xs rounded-md px-3 py-2 w-72 z-10">
                                Record and upload a short 1–2 minute video introduction of yourself for the employer to view. Use this opportunity as a showcase to impress the employer early.
                            </div>
                        </div>
                    </div>
                    <x-text-input type="file" name="video_intro" accept="video/*" />
                    @error('video_intro')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @if (old('video_intro'))
                        <p class="text-xs text-slate-500 mt-1">Please re-upload your video file (not retained after validation).</p>
                    @endif
                </x-card>

                <!-- Upload CV -->
                <x-card>
                    <x-label for="cv">Upload CV</x-label>
                    <x-text-input type="file" name="cv" />
                    @error('cv')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @if (old('cv'))
                        <p class="text-xs text-slate-500 mt-1">Please re-upload your CV (file fields cannot persist after validation).</p>
                    @endif
                </x-card>
            </div>

            <!-- RIGHT -->
            <div class="md:col-span-3">
                <x-card>
                    <div class="flex items-center mb-4">
                        <h3 class="font-semibold text-lg">Key Competency Questions</h3>
                        <!-- Hover tooltip icon -->
                        <div class="relative group ml-2">
                            ℹ️
                            <div class="absolute left-6 top-0 hidden group-hover:block bg-slate-800 text-white text-xs rounded-md px-3 py-2 w-80 z-10">
                                Key Competency Questions test your job-related skills. Answer the questions below truthfully and clearly, explaining your actions in the situation. Use the STAR format: Situation, Task, Action, Result.
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">

                        <div>
                            <x-label for="attention_to_detail">Attention to Detail</x-label>
                            <p class="text-xs text-slate-500 mb-1">Describe at least one example when you failed in this area and explain what you learned from the experience.</p>
                            <textarea name="attention_to_detail" rows="3" class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('attention_to_detail') }}</textarea>
                            @error('attention_to_detail')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="customer_management">Customer Management</x-label>
                            <p class="text-xs text-slate-500 mb-1">Describe an occasion when customer needs and best interests conflicted. Explain how you resolved the issue and the outcome.</p>
                            <textarea name="customer_management" rows="3" class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('customer_management') }}</textarea>
                            @error('customer_management')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="market_understanding">Market Understanding</x-label>
                            <p class="text-xs text-slate-500 mb-1">Would you describe your skill set as more technical or commercial? Provide examples to support your answer.</p>
                            <textarea name="market_understanding" rows="3" class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('market_understanding') }}</textarea>
                            @error('market_understanding')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="sales_and_business_development">Sales and Business Development</x-label>
                            <p class="text-xs text-slate-500 mb-1">Why do people buy from you and continue to buy? What traits make your selling style effective?</p>
                            <textarea name="sales_and_business_development" rows="3" class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('sales_and_business_development') }}</textarea>
                            @error('sales_and_business_development')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="ambition">Ambition</x-label>
                            <p class="text-xs text-slate-500 mb-1">Explain, in a few sentences, why you excel in this area.</p>
                            <textarea name="ambition" rows="3" class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('ambition') }}</textarea>
                            @error('ambition')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="leadership_skills">Leadership Skills</x-label>
                            <p class="text-xs text-slate-500 mb-1">How important do you believe leadership is to the success of your work?</p>
                            <textarea name="leadership_skills" rows="3" class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('leadership_skills') }}</textarea>
                            @error('leadership_skills')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="risk_assessment">Risk Assessment</x-label>
                            <p class="text-xs text-slate-500 mb-1">Describe at least one example when you failed in this area and what you learned from it.</p>
                            <textarea name="risk_assessment" rows="3" class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('risk_assessment') }}</textarea>
                            @error('risk_assessment')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <x-button class="w-full mt-6">Submit Application</x-button>
                </x-card>
            </div>
        </div>
    </form>
</x-layout>
