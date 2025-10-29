<x-card title="Key Competency Questions">
    <div x-data="jobQuestions({
        fetchUrl: '{{ route('admin.jobs.questions.index', $job) }}',
        createUrl: '{{ route('admin.jobs.questions.store', $job) }}',
        toggleBase: '{{ route('admin.jobs.questions.toggle', [$job, 0]) }}',
        deleteBase: '{{ route('admin.jobs.questions.destroy', [$job, 0]) }}',
        reorderUrl: '{{ route('admin.jobs.questions.reorder', $job) }}',
        seedUrl: '{{ route('admin.jobs.questions.seed', $job) }}',
        csrf: '{{ csrf_token() }}'
    })">
        <template x-if="questions.length===0">
            <div class="text-sm text-gray-500">
                No questions added yet.
                <button 
                    @click="seed" 
                    class="ml-2 px-2 py-1 bg-blue-600 text-white text-xs rounded-md">
                    Load Default Questions
                </button>
            </div>
        </template>

        <template x-if="questions.length>0">
            <ul class="space-y-2">
                <template x-for="q in questions" :key="q.id">
                    <li class="flex justify-between items-center border p-2 rounded-md text-sm">
                        <span x-text="q.question"></span>
                        <div class="flex gap-2">
                            <button @click="toggle(q.id)" class="text-xs" :class="q.is_enabled ? 'text-green-600' : 'text-gray-400'">
                                <span x-text="q.is_enabled ? 'Enabled' : 'Disabled'"></span>
                            </button>
                            <button @click="remove(q.id)" class="text-red-500">🗑</button>
                        </div>
                    </li>
                </template>
            </ul>
        </template>

        <form class="mt-3 flex gap-2" @submit.prevent="add">
            <input type="text" x-model="question" placeholder="Enter new question..." class="border rounded-md px-2 py-1 flex-1">
            <button type="submit" class="px-3 py-1 bg-[#04215c] text-white rounded-md text-sm">Add</button>
        </form>
    </div>
</x-card>
