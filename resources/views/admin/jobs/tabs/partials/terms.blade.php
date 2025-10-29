<x-card title="Terms & Conditions">
    <div x-data="termsBox({
        getUrl: '{{ route('admin.jobs.terms.get', $job) }}',
        saveUrl: '{{ route('admin.jobs.terms.update', $job) }}',
        csrf: '{{ csrf_token() }}',
    })" class="space-y-4">
        <div>
            <label class="block mb-1 text-sm font-medium">Candidate Terms</label>
            <textarea x-model="candidate" rows="5" class="w-full border rounded-md p-2 text-sm"></textarea>
        </div>
        <div>
            <label class="block mb-1 text-sm font-medium">Employer Terms</label>
            <textarea x-model="employer" rows="5" class="w-full border rounded-md p-2 text-sm"></textarea>
        </div>

        <div class="flex justify-end">
            <button @click="save" class="px-4 py-2 bg-[#04215c] text-white text-sm rounded-md hover:bg-[#06318a]">Save Terms</button>
        </div>

        <p x-text="msg" class="text-xs" :class="ok ? 'text-green-600' : 'text-red-600'"></p>
    </div>
</x-card>
