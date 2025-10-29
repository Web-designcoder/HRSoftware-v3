<x-card title="Campaign Information">
    <form @submit.prevent="saveDetails" class="space-y-4 text-sm">
        <div>
            <label class="block mb-1 font-medium">Employer</label>
            <select x-model="job.employer_id" class="w-full border rounded-md p-2 text-sm">
                <option value="">Select Employer</option>
                @foreach($employers as $employer)
                    <option value="{{ $employer->id }}">{{ $employer->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1 font-medium">Job Title</label>
            <input type="text" x-model="job.title" class="w-full border rounded-md p-2 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block mb-1 font-medium">Location</label>
                <input type="text" x-model="job.location" class="w-full border rounded-md p-2 text-sm">
            </div>
            <div>
                <label class="block mb-1 font-medium">City/Suburb</label>
                <input type="text" x-model="job.city" class="w-full border rounded-md p-2 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block mb-1 font-medium">Country</label>
                <input type="text" x-model="job.country" class="w-full border rounded-md p-2 text-sm">
            </div>
            <div>
                <label class="block mb-1 font-medium">Managed By</label>
                <input type="text" x-model="job.managed_by" class="w-full border rounded-md p-2 text-sm" placeholder="Consultant name">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block mb-1 font-medium">Date Posted</label>
                <input type="date" x-model="job.date_posted" class="w-full border rounded-md p-2 text-sm">
            </div>
            <div>
                <label class="block mb-1 font-medium">Salary</label>
                <input type="number" x-model="job.salary" class="w-full border rounded-md p-2 text-sm" placeholder="e.g. 75000">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block mb-1 font-medium">Experience</label>
                <input type="text" x-model="job.experience" class="w-full border rounded-md p-2 text-sm">
            </div>
            <div>
                <label class="block mb-1 font-medium">Category</label>
                <input type="text" x-model="job.category" class="w-full border rounded-md p-2 text-sm">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-[#04215c] text-white rounded-md hover:bg-[#06318a] text-sm">
                Save Changes
            </button>
        </div>

        <p x-text="flash.details" class="text-xs mt-1" :class="flash.details_ok ? 'text-green-600' : 'text-red-600'"></p>
    </form>
</x-card>
