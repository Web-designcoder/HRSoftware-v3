<x-layout>
    <div class="container mx-auto py-6"
         x-data="{ view: localStorage.getItem('candidateView') || 'card' }"
         x-init="$watch('view', v => localStorage.setItem('candidateView', v))">

        <!-- Header + Add Button -->
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Candidates</h1>
            <a href="{{ route('admin.users.create') }}"
               class="px-4 py-2 rounded-md bg-[#04215c] text-white hover:bg-[#06318a] transition">
                + Add Candidate
            </a>
        </div>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6 bg-white p-4 rounded-lg shadow">
            <input name="keyword" value="{{ request('keyword') }}" placeholder="Search name, email or keyword"
                class="border bg-white border-gray-300 rounded-md px-3 py-2 text-sm col-span-full md:col-span-2 lg:col-span-2" />
                
            <select name="job_title" class="border rounded-md px-3 py-2 text-sm">
                <option value="">All Job Titles</option>
                @foreach($jobTitles as $title)
                    <option value="{{ $title }}" {{ request('job_title') == $title ? 'selected' : '' }}>{{ $title }}</option>
                @endforeach
            </select>

            <select name="industry" class="border rounded-md px-3 py-2 text-sm">
                <option value="">All Industries</option>
                @foreach($industries as $industry)
                    <option value="{{ $industry }}" {{ request('industry') == $industry ? 'selected' : '' }}>{{ $industry }}</option>
                @endforeach
            </select>

            <select name="city" class="border rounded-md px-3 py-2 text-sm">
                <option value="">All Cities</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>

            <select name="country" class="border rounded-md px-3 py-2 text-sm">
                <option value="">All Countries</option>
                @foreach($countries as $country)
                    <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                @endforeach
            </select>

            <div class="col-span-full flex gap-2">
                <x-button>Apply Filters</x-button>
                <a href="{{ route('admin.users.candidates') }}" class="px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">Reset</a>
            </div>
        </form>

        <!-- View Toggle Buttons -->
        <div class="flex justify-end mb-4 gap-2">
            <button @click="view = 'card'"
                    :class="view === 'card' ? 'bg-[#04215c] text-white' : 'bg-white text-gray-700 border'"
                    class="px-3 py-2 rounded-md text-sm font-medium hover:bg-[#06318a] hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Card View
            </button>
            <button @click="view = 'list'"
                    :class="view === 'list' ? 'bg-[#04215c] text-white' : 'bg-white text-gray-700 border'"
                    class="px-3 py-2 rounded-md text-sm font-medium hover:bg-[#06318a] hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h7M4 12h7M4 18h7M17 6h3M17 12h3M17 18h3" />
                </svg>
                List View
            </button>
        </div>

        <!-- CARD VIEW -->
        <div x-show="view === 'card'" x-transition>
            @if($candidates->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($candidates as $u)
                        <x-card class="bg-white p-5 shadow-md hover:shadow-lg transition rounded-lg">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h2 class="font-bold text-lg">{{ $u->name }}</h2>
                                    <p class="text-sm text-gray-500">{{ $u->email }}</p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700 capitalize">Candidate</span>
                            </div>

                            <p class="text-xs text-gray-400 mb-4">Updated: {{ optional($u->updated_at)->format('d M Y') }}</p>

                            <ul class="text-sm text-gray-700 mb-4 space-y-1">
                                <li><strong>Job:</strong> {{ $u->desired_job_title ?? '—' }}</li>
                                <li><strong>City:</strong> {{ $u->city ?? '—' }}</li>
                                <li><strong>Country:</strong> {{ $u->country ?? '—' }}</li>
                            </ul>

                            <div class="flex justify-end gap-3">
                                <a href="{{ route('admin.users.edit', $u) }}"
                                   class="text-sm bg-[#04215c] text-white px-3 py-1.5 rounded-md hover:bg-[#06318a] transition">
                                   Edit
                                </a>
                            </div>
                        </x-card>
                    @endforeach
                </div>
                <div class="mt-6">{{ $candidates->links() }}</div>
            @else
                <p class="text-gray-600 text-center py-6">No candidates found.</p>
            @endif
        </div>

        <!-- LIST VIEW -->
        <div x-show="view === 'list'" x-transition>
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3">Name</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Job Title</th>
                            <th class="p-3">City</th>
                            <th class="p-3">Country</th>
                            <th class="p-3">Updated</th>
                            <th class="p-3 w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($candidates as $u)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-3">{{ $u->name }}</td>
                                <td class="p-3">{{ $u->email }}</td>
                                <td class="p-3">{{ $u->desired_job_title ?? '—' }}</td>
                                <td class="p-3">{{ $u->city ?? '—' }}</td>
                                <td class="p-3">{{ $u->country ?? '—' }}</td>
                                <td class="p-3">{{ optional($u->updated_at)->format('d M Y') }}</td>
                                <td class="p-3">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="text-blue-600 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-6 text-center text-gray-500">No candidates found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $candidates->links() }}</div>
        </div>
    </div>
</x-layout>
