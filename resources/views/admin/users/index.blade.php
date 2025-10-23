<x-layout>
    <div class="container mx-auto py-6"
         x-data="{ view: localStorage.getItem('userView') || 'list' }"
         x-init="$watch('view', v => localStorage.setItem('userView', v))">

        <!-- Header + Add Button -->
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Users</h1>
            <a href="{{ route('admin.users.create') }}"
               class="px-4 py-2 rounded-md bg-[#04215c] text-white hover:bg-[#06318a] transition">
                + Add User
            </a>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 rounded-md bg-green-50 text-green-800 px-4 py-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-md bg-red-50 text-red-800 px-4 py-3">{{ session('error') }}</div>
        @endif

        <!-- Tabs + Search -->
        <div class="flex flex-wrap items-center gap-3 mb-4">
            @php
                $tabs = [
                    ['label' => 'All',        'role' => null,         'count' => $counts['all'] ?? null],
                    ['label' => 'Admins',     'role' => 'admin',      'count' => $counts['admin'] ?? null],
                    ['label' => 'Consultants','role' => 'consultant', 'count' => $counts['consultant'] ?? null],
                    ['label' => 'Employers',  'role' => 'employer',   'count' => $counts['employer'] ?? null],
                    ['label' => 'Candidates', 'role' => 'candidate',  'count' => $counts['candidate'] ?? null],
                ];
            @endphp

            <div class="flex flex-wrap gap-2">
                @foreach($tabs as $tab)
                    @php
                        $active = ($role === ($tab['role'] ?? null));
                        $url = $tab['role']
                            ? route('admin.users.index', array_merge(request()->only('q'), ['role' => $tab['role']]))
                            : route('admin.users.index', request()->only('q'));
                    @endphp
                    <a href="{{ $url }}"
                       class="px-3 py-1 rounded-full text-sm border {{ $active ? 'bg-[#04215c] text-white border-[#04215c]' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                        {{ $tab['label'] }}
                        @if(!is_null($tab['count'])) <span class="ml-1 text-xs opacity-80">({{ $tab['count'] }})</span> @endif
                    </a>
                @endforeach
            </div>

            <form method="GET" class="ml-auto flex items-center gap-2">
                <input type="hidden" name="role" value="{{ $role }}">
                <input name="q" value="{{ $q }}" placeholder="Search name or email"
                       class="border bg-white border-gray-300 rounded-md px-3 py-2 text-sm w-64" />
                <button class="px-3 py-2 bg-white rounded-md border border-gray-300 text-sm hover:bg-gray-50">Search</button>
                @if($q)
                    <a href="{{ route('admin.users.index', ['role' => $role]) }}" class="text-sm text-gray-600 hover:underline">Clear</a>
                @endif
            </form>
        </div>

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

        <!-- ─── CARD VIEW ───────────────────────────── -->
        <div x-show="view === 'card'" x-transition>
            @if($users->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($users as $u)
                        <x-card class="bg-white p-5 shadow-md hover:shadow-lg transition rounded-lg">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h2 class="font-bold text-lg">{{ $u->name }}</h2>
                                    <p class="text-sm text-gray-500">{{ $u->email }}</p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full capitalize
                                    @if($u->role=='admin') bg-blue-100 text-blue-700
                                    @elseif($u->role=='consultant') bg-green-100 text-green-700
                                    @elseif($u->role=='employer') bg-purple-100 text-purple-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ $u->role }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mb-4">Created on: {{ optional($u->created_at)->format('d M Y') }}</p>

                            <div class="flex justify-end gap-3">
                                <a href="{{ route('admin.users.edit', $u) }}"
                                   class="text-sm bg-[#04215c] text-white px-3 py-1.5 rounded-md hover:bg-[#06318a] transition">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline"
                                      onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                    @csrf @method('DELETE')
                                    <button class="text-sm bg-red-100 text-red-700 px-3 py-1.5 rounded-md hover:bg-red-200 transition" type="submit">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </x-card>
                    @endforeach
                </div>
                <div class="mt-6">{{ $users->links() }}</div>
            @else
                <p class="text-gray-600 text-center py-6">No users found.</p>
            @endif
        </div>

        <!-- ─── LIST VIEW (default) ───────────────────────────── -->
        <div x-show="view === 'list'" x-transition>
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-100 text-left text-sm text-gray-700">
                            <th class="p-3">Name</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Role</th>
                            <th class="p-3">Created</th>
                            <th class="p-3 w-40">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($users as $u)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-3">{{ $u->name }}</td>
                                <td class="p-3">{{ $u->email }}</td>
                                <td class="p-3 capitalize">{{ $u->role }}</td>
                                <td class="p-3">{{ optional($u->created_at)->format('d M Y') }}</td>
                                <td class="p-3">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="text-blue-600 hover:underline">Edit</a>
                                    <span class="mx-1 text-gray-300">|</span>
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline"
                                          onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="p-6 text-center text-gray-500" colspan="5">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
</x-layout>
