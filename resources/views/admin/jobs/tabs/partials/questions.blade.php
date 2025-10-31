<x-card>
    <div
        x-data="jobQuestions({
            fetchUrl: '{{ route('admin.jobs.questions.index', $job) }}',
            createUrl: '{{ route('admin.jobs.questions.store', $job) }}',
            toggleBase: '{{ route('admin.jobs.questions.toggle', [$job, 0]) }}',
            deleteBase: '{{ route('admin.jobs.questions.destroy', [$job, 0]) }}',
            reorderUrl: '{{ route('admin.jobs.questions.reorder', $job) }}',
            seedUrl: '{{ route('admin.jobs.questions.seed', $job) }}',
            csrf: '{{ csrf_token() }}'
        })"
        x-init="init()"
    >
        <!-- Header -->
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-lg font-semibold text-[#04215c]">Key Competency Sections</h2>
            <button
                @click="openKCQModal = true"
                class="flex items-center gap-1 px-3 py-1 bg-[#04215c] text-white text-sm rounded-md hover:bg-[#06318a] transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L7.5 20.5H4v-3.5L16.732 3.732z"/>
                </svg>
                Edit
            </button>
        </div>

        <!-- Summary List -->
        <ul class="text-sm text-gray-700 space-y-1">
            <template x-for="q in questions.slice(0, 3)" :key="q.id">
                <li>
                    <strong x-text="q.heading"></strong>
                    — <span x-text="q.body"></span>
                </li>
            </template>
            <template x-if="questions.length === 0">
                <li class="text-gray-400 italic">No competency questions yet.</li>
            </template>
        </ul>

        <!-- Modal (matches your Contacts modal positioning/behavior) -->
        <div
            x-show="openKCQModal"
            x-transition.opacity.duration.200ms
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        >
            <div
                @click.away="openKCQModal=false"
                class="bg-white rounded-lg shadow-xl w-full max-w-3xl p-6 relative"
            >
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-lg font-semibold text-[#04215c]">Manage Key Competency Questions</h3>
                    <button @click="openKCQModal=false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                </div>

                <!-- Empty state -->
                <template x-if="questions.length === 0">
                    <div class="text-sm text-gray-500 mb-3">
                        No questions added yet.
                        <button
                            @click="seed"
                            class="ml-2 px-2 py-1 bg-blue-600 text-white text-xs rounded-md"
                        >
                            Load Default Questions
                        </button>
                    </div>
                </template>

                <!-- Questions List -->
                <template x-if="questions.length > 0">
                    <ul class="space-y-3 max-h-[400px] overflow-y-auto border rounded-md p-3">
                        <template x-for="q in questions" :key="q.id">
                            <li class="p-3 rounded-md border hover:bg-gray-50 transition">
                                <div class="flex justify-between items-center mb-1">
                                    <strong x-text="q.heading" class="text-[#04215c]"></strong>
                                    <div class="flex gap-3">
                                        <button
                                            @click="toggle(q.id)"
                                            class="text-xs"
                                            :class="q.is_enabled ? 'text-green-600' : 'text-gray-400'"
                                        >
                                            <span x-text="q.is_enabled ? 'Enabled' : 'Disabled'"></span>
                                        </button>
                                        <button @click="remove(q.id)" class="text-red-500 hover:text-red-700">🗑</button>
                                    </div>
                                </div>
                                <p x-text="q.body" class="text-sm text-gray-700"></p>
                            </li>
                        </template>
                    </ul>
                </template>

                <!-- Add Question -->
                <form class="mt-5 space-y-2" @submit.prevent="add">
                    <input
                        type="text"
                        x-model="heading"
                        placeholder="Enter heading..."
                        class="border rounded-md px-2 py-1 w-full focus:ring-2 focus:ring-[#04215c]"
                    >
                    <textarea
                        x-model="body"
                        placeholder="Enter question text..."
                        class="border rounded-md px-2 py-1 w-full h-20 focus:ring-2 focus:ring-[#04215c]"
                    ></textarea>
                    <button
                        type="submit"
                        class="px-3 py-1 bg-[#04215c] text-white rounded-md text-sm hover:bg-[#06318a]"
                    >
                        Add Question
                    </button>
                </form>

                <!-- Modal Footer -->
                <div class="flex justify-end mt-6 space-x-3">
                    <button
                        @click="openKCQModal=false"
                        class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-card>
