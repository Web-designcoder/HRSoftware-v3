<x-layout>
<div class="container mx-auto py-6"
     x-data="{ view: localStorage.getItem('clientView') || 'card', showFilters: true }"
     x-init="$watch('view', v => localStorage.setItem('clientView', v))">

    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Clients (Employers)</h1>
        <a href="{{ route('admin.users.create') }}"
           class="px-4 py-2 rounded-md bg-[#04215c] text-white hover:bg-[#06318a] transition">
            + Add Client
        </a>
    </div>

    <!-- Filter toggle -->
    <div class="flex justify-between items-center mb-3">
        <button @click="showFilters = !showFilters"
                class="text-sm text-[#04215c] font-semibold flex items-center gap-1 hover:underline">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            Toggle Filters
        </button>
    </div>

    <!-- Filters -->
    <form method="GET" x-show="showFilters" x-transition
          class="bg-white rounded-lg shadow p-4 mb-6 space-y-4">

        <!-- ─── Row 1: Text Inputs ───────────────────────── -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input name="keyword" value="{{ request('keyword') }}"
                   placeholder="Search company, contact person, email or keyword..."
                   class="border bg-white border-gray-300 rounded-md px-3 py-2 text-sm w-full" />

            <input name="office_number" value="{{ request('office_number') }}"
                   placeholder="Office Number"
                   class="border bg-white border-gray-300 rounded-md px-3 py-2 text-sm w-full" />

            <input name="office_email" value="{{ request('office_email') }}"
                   placeholder="Office Email Address"
                   class="border bg-white border-gray-300 rounded-md px-3 py-2 text-sm w-full" />
        </div>

        <!-- ─── Row 2: Dropdowns + Checkbox ───────────────── -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-center">
            <select name="job_title" class="border rounded-md px-3 py-2 text-sm w-full">
                <option value="">All Job Titles</option>
                @foreach($jobTitles as $title)
                    <option value="{{ $title }}" {{ request('job_title') == $title ? 'selected' : '' }}>{{ $title }}</option>
                @endforeach
            </select>

            <select name="industry" class="border rounded-md px-3 py-2 text-sm w-full">
                <option value="">All Industries</option>
                @foreach($industries as $industry)
                    <option value="{{ $industry }}" {{ request('industry') == $industry ? 'selected' : '' }}>{{ $industry }}</option>
                @endforeach
            </select>

            <select name="country" class="border rounded-md px-3 py-2 text-sm w-full">
                <option value="">All Countries</option>
                @foreach($countries as $country)
                    <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                @endforeach
            </select>

            <select name="city" class="border rounded-md px-3 py-2 text-sm w-full">
                <option value="">All Cities/Suburbs</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>

            <label class="flex items-center gap-2 text-sm whitespace-nowrap">
                <input type="checkbox" name="has_attachment" value="1"
                       {{ request('has_attachment') ? 'checked' : '' }}
                       class="rounded border-gray-300">
                <span>Has Attachment</span>
            </label>
        </div>

        <!-- ─── Row 3: Buttons ───────────────────────────── -->
        <div class="flex gap-2 pt-2">
            <x-button>Apply Filters</x-button>
            <a href="{{ route('admin.users.clients') }}"
               class="px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">Reset</a>
        </div>
    </form>

    <!-- View toggle -->
    <div class="flex justify-end mb-4 gap-2">
        <button @click="view = 'card'"
                :class="view === 'card' ? 'bg-[#04215c] text-white' : 'bg-white text-gray-700 border'"
                class="px-3 py-2 rounded-md text-sm font-medium hover:bg-[#06318a] hover:text-white transition">
            Card View
        </button>
        <button @click="view = 'list'"
                :class="view === 'list' ? 'bg-[#04215c] text-white' : 'bg-white text-gray-700 border'"
                class="px-3 py-2 rounded-md text-sm font-medium hover:bg-[#06318a] hover:text-white transition">
            List View
        </button>
    </div>

    <!-- CARD VIEW -->
    <div x-show="view === 'card'" x-transition>
        @if($clients->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($clients as $u)
                    <x-card class="bg-white p-5 shadow-md hover:shadow-lg transition rounded-lg">
                        <h2 class="font-bold text-lg text-[#04215c] mb-1">{{ $u->company_name ?? '—' }}</h2>
                        <p class="text-sm text-gray-500 mb-3">{{ $u->name }} • {{ $u->email }}</p>

                        <ul class="text-sm text-gray-700 mb-4 space-y-1">
                            <li><strong>Job Title:</strong> {{ $u->job_title ?? '—' }}</li>
                            <li><strong>Industry:</strong> {{ $u->industry ?? '—' }}</li>
                            <li><strong>City:</strong> {{ $u->city ?? '—' }}</li>
                            <li><strong>Country:</strong> {{ $u->country ?? '—' }}</li>
                            <li><strong>Office:</strong> {{ $u->office_number ?? '—' }}</li>
                        </ul>

                        <div class="flex justify-end gap-3">
                            @if($u->attachment)
                                <a href="{{ asset('storage/' . $u->attachment) }}" target="_blank"
                                   class="text-sm text-blue-600 hover:underline">📎 Attachment</a>
                            @endif
                            <a href="{{ route('admin.users.edit', $u) }}"
                               class="text-sm bg-[#04215c] text-white px-3 py-1.5 rounded-md hover:bg-[#06318a] transition">
                               Edit
                            </a>
                        </div>
                    </x-card>
                @endforeach
            </div>
            <div class="mt-6">{{ $clients->links() }}</div>
        @else
            <p class="text-gray-600 text-center py-6">No clients found.</p>
        @endif
    </div>

    <!-- LIST VIEW -->
    <div x-show="view === 'list'" x-transition>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="p-3">Company</th>
                        <th class="p-3">Job Title</th>
                        <th class="p-3">Industry</th>
                        <th class="p-3">City</th>
                        <th class="p-3">Country</th>
                        <th class="p-3">Office No.</th>
                        <th class="p-3">Office Email</th>
                        <th class="p-3">Attachment</th>
                        <th class="p-3 w-32">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $u)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-3">{{ $u->company_name ?? '—' }}</td>
                            <td class="p-3">{{ $u->job_title ?? '—' }}</td>
                            <td class="p-3">{{ $u->industry ?? '—' }}</td>
                            <td class="p-3">{{ $u->city ?? '—' }}</td>
                            <td class="p-3">{{ $u->country ?? '—' }}</td>
                            <td class="p-3">{{ $u->office_number ?? '—' }}</td>
                            <td class="p-3">{{ $u->email ?? '—' }}</td>
                            <td class="p-3">
                                @if($u->attachment)
                                    <a href="{{ asset('storage/' . $u->attachment) }}" target="_blank"
                                       class="text-blue-600 hover:underline">📎</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="p-3">
                                <a href="{{ route('admin.users.edit', $u) }}" class="text-blue-600 hover:underline">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="p-6 text-center text-gray-500">No clients found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $clients->links() }}</div>
    </div>
</div>
</x-layout>
