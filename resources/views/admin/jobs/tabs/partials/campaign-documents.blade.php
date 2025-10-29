<x-card title="Campaign Documents">
    <div x-data="jobDocuments({
        fetchUrl: '{{ route('admin.jobs.documents.index', $job) }}',
        uploadUrl: '{{ route('admin.jobs.documents.store', $job) }}',
        deleteBase: '{{ route('admin.jobs.documents.destroy', [$job, 0]) }}',
        reorderUrl: '{{ route('admin.jobs.documents.reorder', $job) }}',
        csrf: '{{ csrf_token() }}'
    })">
        <template x-if="docs.length===0">
            <p class="text-sm text-gray-500">No documents added yet.</p>
        </template>

        <template x-if="docs.length>0">
            <ul class="space-y-1">
                <template x-for="doc in docs" :key="doc.id">
                    <li class="flex justify-between items-center border p-2 rounded-md">
                        <a :href="doc.url" target="_blank" class="text-[#04215c] text-sm font-medium" x-text="doc.name"></a>
                        <button @click="remove(doc.id)" class="text-red-500 hover:text-red-700">🗑</button>
                    </li>
                </template>
            </ul>
        </template>

        <form class="mt-3 flex gap-2" @submit.prevent="add">
            <input type="text" x-model="name" placeholder="Document Name" class="border rounded-md px-2 py-1 flex-1">
            <input type="file" class="hidden" x-ref="file" @change="selectFile">
            <button type="button" @click="$refs.file.click()" class="px-3 py-1 bg-gray-200 rounded-md text-sm">Select</button>
            <button type="submit" class="px-3 py-1 bg-[#04215c] text-white rounded-md text-sm">Add</button>
        </form>
    </div>
</x-card>
