<!-- resources/views/admin/jobs/tabs/partials/employer-contacts.blade.php -->
<x-card title="Employer Contacts"
    x-data="jobContacts({
        fetchUrl: '{{ route('admin.jobs.contacts.index', $job) }}',
        attachUrl: '{{ route('admin.jobs.contacts.attach', $job) }}',
        detachBaseUrl: '{{ url('admin/jobs/'.$job->id.'/contacts') }}',
        primaryUrl: '{{ route('admin.jobs.contacts.primary', $job) }}',
        allUsersUrl: '{{ route('admin.users.clients.json') }}',
        csrf: '{{ csrf_token() }}',
        initialPrimary: {{ $job->primary_contact_id ?? 'null' }}
    })">

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Primary Contact</label>
        <select x-model="primaryContact" @change="updatePrimary"
            class="w-full rounded-md border-gray-300 text-sm">
            <option value="">-- Select --</option>
            <template x-for="c in contacts" :key="c.id">
                <option :value="c.id" x-text="c.name"></option>
            </template>
        </select>

        <hr class="my-4">

        <div class="flex justify-between items-center">
            <h3 class="font-semibold text-sm text-gray-700">Contacts</h3>
            <button @click="openModal=true" class="text-sm text-[#04215c] hover:underline">+ Add Contact</button>
        </div>

        <template x-if="contacts.length===0">
            <p class="text-gray-500 text-sm">No contacts added yet.</p>
        </template>

        <ul class="space-y-2 text-sm" x-show="contacts.length>0">
            <template x-for="c in contacts" :key="c.id">
                <li class="flex justify-between items-center bg-gray-50 px-2 py-1 rounded">
                    <span x-text="`${c.name} — ${c.email}`"></span>
                    <button @click="removeContact(c.id)" class="text-red-500 hover:text-red-700">🗑️</button>
                </li>
            </template>
        </ul>
    </div>

    <!-- Modal -->
    <div x-show="openModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div @click.away="openModal=false"
             class="bg-white rounded-lg shadow-xl w-full max-w-3xl p-6 relative">
            <h3 class="text-lg font-semibold text-[#04215c] mb-4">Add Employer Contacts</h3>

            <!-- Search + Select All -->
            <div class="flex items-center justify-between mb-3">
                <input type="text" x-model="search"
                    placeholder="Search users..."
                    class="border rounded-md px-3 py-2 w-2/3 focus:ring-2 focus:ring-[#04215c]">
                <label class="flex items-center space-x-2 text-sm">
                    <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="accent-[#04215c]">
                    <span>Select All</span>
                </label>
            </div>

            <!-- User list -->
            <div class="max-h-[400px] overflow-y-auto border rounded-md divide-y">
                <template x-for="user in filteredUsers" :key="user.id">
                    <label class="flex items-center justify-between px-4 py-2 hover:bg-gray-50 cursor-pointer">
                        <div>
                            <span class="font-medium" x-text="user.name"></span>
                            <span class="text-gray-500 text-sm ml-2" x-text="user.email"></span>
                        </div>
                        <input type="checkbox" class="accent-[#04215c]"
                            :value="user.id"
                            x-model="pendingIds">
                    </label>
                </template>
                <div x-show="filteredUsers.length === 0"
                     class="text-center text-gray-500 py-4">
                    No matching users found.
                </div>
            </div>

            <div class="flex justify-end mt-4 space-x-3">
                <button @click="openModal=false"
                        class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button @click="attachChecked()"
                        :disabled="loading || pendingIds.length===0"
                        class="px-4 py-2 bg-[#04215c] text-white rounded-md hover:bg-[#06318a] disabled:opacity-50">
                    <span x-show="!loading">Add Selected (<span x-text="pendingIds.length"></span>)</span>
                    <span x-show="loading">Adding...</span>
                </button>
            </div>
        </div>
    </div>
</x-card>
