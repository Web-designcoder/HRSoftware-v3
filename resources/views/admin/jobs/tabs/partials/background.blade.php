<x-card title="Background & Assignment Overview">
    <form @submit.prevent="saveOverviews" class="space-y-3">
        <div>
            <label class="block mb-1 text-sm font-medium">Background</label>
            <textarea x-model="background" rows="4" class="w-full border rounded-md p-2 text-sm"></textarea>
        </div>
        <div>
            <label class="block mb-1 text-sm font-medium">Assignment Overview</label>
            <textarea x-model="assignment" rows="4" class="w-full border rounded-md p-2 text-sm"></textarea>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-[#04215c] text-white text-sm rounded-md hover:bg-[#06318a]">Save</button>
        </div>
        <p x-text="flash.overviews" class="text-xs mt-1" :class="flash.overviews_ok ? 'text-green-600' : 'text-red-600'"></p>
    </form>
</x-card>
