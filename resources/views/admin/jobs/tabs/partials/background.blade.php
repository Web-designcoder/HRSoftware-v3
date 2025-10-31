<x-card title="Background & Assignment Overview">
    <div 
        x-data="campaignDetails({
            job: @js($job),
            logo: '{{ $job->company_logo_url ?? '' }}',
            background: '{{ $job->description ?? '' }}',
            assignment: '{{ $job->assignment_overview ?? '' }}',
            csrf: '{{ csrf_token() }}',
            detailsUrl: '{{ route('admin.jobs.details.update', $job) }}',
            overviewsUrl: '{{ route('admin.jobs.overviews.update', $job) }}',
            logoUrl: '{{ route('admin.jobs.logo.upload', $job) }}'
        })"
        class="space-y-4"
    >
        <!-- Tabs -->
        <div class="flex border-b border-gray-200 mb-3">
            <button 
                @click="activeTab = 'background'" 
                :class="activeTab === 'background' ? 'border-b-2 border-[#04215c] text-[#04215c]' : 'text-gray-500'"
                class="px-4 py-2 text-sm font-medium focus:outline-none"
            >
                Background
            </button>
            <button 
                @click="activeTab = 'assignment'" 
                :class="activeTab === 'assignment' ? 'border-b-2 border-[#04215c] text-[#04215c]' : 'text-gray-500'"
                class="px-4 py-2 text-sm font-medium focus:outline-none"
            >
                Assignment Overview
            </button>
        </div>

        <form @submit.prevent="saveOverviews" class="space-y-3">
            <div x-show="activeTab === 'background'" x-cloak>
                <label class="block mb-1 text-sm font-medium">Background</label>
                <textarea x-model="background" rows="6" class="w-full border rounded-md p-2 text-sm"></textarea>
            </div>

            <div x-show="activeTab === 'assignment'" x-cloak>
                <label class="block mb-1 text-sm font-medium">Assignment Overview</label>
                <textarea x-model="assignment" rows="6" class="w-full border rounded-md p-2 text-sm"></textarea>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-4 py-2 bg-[#04215c] text-white text-sm rounded-md hover:bg-[#06318a]">
                    Save
                </button>
            </div>

            <p x-text="flash.overviews" class="text-xs mt-1" :class="flash.overviews_ok ? 'text-green-600' : 'text-red-600'"></p>
        </form>
    </div>
</x-card>
