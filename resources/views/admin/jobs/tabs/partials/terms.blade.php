<x-card title="Terms & Conditions">
    <div 
        x-data="{
            activeTab: 'candidate',
            ...termsBox({
                getUrl: '{{ route('admin.jobs.terms.get', $job) }}',
                saveUrl: '{{ route('admin.jobs.terms.update', $job) }}',
                csrf: '{{ csrf_token() }}',
            })
        }" 
        x-init="init()" 
        class="space-y-4"
    >
        <!-- Tabs -->
        <div class="flex border-b border-gray-200 mb-3">
            <button 
                @click="activeTab = 'candidate'" 
                :class="activeTab === 'candidate' ? 'border-b-2 border-[#04215c] text-[#04215c]' : 'text-gray-500'"
                class="px-4 py-2 text-sm font-medium focus:outline-none"
            >
                Candidate Terms
            </button>
            <button 
                @click="activeTab = 'employer'" 
                :class="activeTab === 'employer' ? 'border-b-2 border-[#04215c] text-[#04215c]' : 'text-gray-500'"
                class="px-4 py-2 text-sm font-medium focus:outline-none"
            >
                Employer Terms
            </button>
        </div>

        <!-- Candidate Terms -->
        <div x-show="activeTab === 'candidate'" x-cloak>
            <label class="block mb-1 text-sm font-medium">Candidate Terms</label>
            <textarea x-model="candidate" rows="8" class="w-full border rounded-md p-2 text-sm"></textarea>
        </div>

        <!-- Employer Terms -->
        <div x-show="activeTab === 'employer'" x-cloak>
            <label class="block mb-1 text-sm font-medium">Employer Terms</label>
            <textarea x-model="employer" rows="8" class="w-full border rounded-md p-2 text-sm"></textarea>
        </div>

        <!-- Save -->
        <div class="flex justify-end pt-2">
            <button 
                @click="save" 
                class="px-4 py-2 bg-[#04215c] text-white text-sm rounded-md hover:bg-[#06318a]"
            >
                Save Terms
            </button>
        </div>

        <p x-text="msg" class="text-xs" :class="ok ? 'text-green-600' : 'text-red-600'"></p>
    </div>
</x-card>
