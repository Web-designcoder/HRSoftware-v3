<x-layout>
    <div class="container mx-auto py-6" x-data="{ view: 'card' }">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Job Applications</h1>

            <!-- View toggle buttons -->
            <div class="flex gap-2">
                <button @click="view = 'card'"
                        :class="view === 'card' ? 'bg-[#04215c] text-white' : 'bg-white text-gray-700 border'"
                        class="px-3 py-2 rounded-md text-sm font-medium hover:bg-[#06318a] hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    Card View
                </button>
                <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-[#04215c] text-white' : 'bg-white text-gray-700 border'"
                        class="px-3 py-2 rounded-md text-sm font-medium hover:bg-[#06318a] hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h7M4 12h7M4 18h7M17 6h3M17 12h3M17 18h3" />
                    </svg>
                    List View
                </button>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="flex gap-4 mb-6">
            <input type="text" name="search" placeholder="Search name or job title"
                   value="{{ request('search') }}"
                   class="bg-white border rounded-md px-3 py-2 flex-1">
            <select name="status" class="bg-white border rounded-md px-3 py-2">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="accepted" {{ request('status')=='accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button type="submit"
                    class="bg-[#04215c] text-white px-4 py-2 rounded-md hover:bg-[#06318a] transition">
                Filter
            </button>
        </form>

        <!-- ─── CARD VIEW (default) ───────────────────────────── -->
        <div x-show="view === 'card'" x-transition>
            @if($applications->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($applications as $app)
                        <x-card class="bg-white p-5 shadow-md hover:shadow-lg transition rounded-lg">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h2 class="font-bold text-lg">{{ $app->user?->name ?? 'Unknown Candidate' }}</h2>
                                    <p class="text-sm text-gray-500">{{ $app->job?->title ?? 'No Job Title' }}</p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full 
                                    @if($app->status=='accepted') bg-green-100 text-green-700
                                    @elseif($app->status=='rejected') bg-red-100 text-red-700
                                    @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-600 mb-2">
                                <strong>Employer:</strong> {{ $app->job?->employer?->company_name ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-600 mb-2">
                                <strong>Consultant:</strong> {{ $app->job?->consultant?->name ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-400 mb-4">Applied on: {{ optional($app->created_at)->format('d M Y') }}</p>

                            <div class="flex justify-end">
                                <a href="{{ route('admin.job.application.show', [$app->job_id, $app->id]) }}"
                                   class="text-sm bg-[#04215c] text-white px-3 py-1.5 rounded-md hover:bg-[#06318a] transition">
                                    View Details
                                </a>
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $applications->links() }}
                </div>
            @else
                <p class="text-gray-600 text-center py-6">No job applications found.</p>
            @endif
        </div>

        <!-- ─── LIST VIEW ───────────────────────────── -->
        <div x-show="view === 'list'" x-transition>
            <x-card class="!bg-white p-4">
                @if($applications->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-200 rounded-md">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="p-3 text-left">Candidate</th>
                                    <th class="p-3 text-left">Job Title</th>
                                    <th class="p-3 text-left">Employer</th>
                                    <th class="p-3 text-left">Consultant</th>
                                    <th class="p-3 text-left">Status</th>
                                    <th class="p-3 text-left">Applied On</th>
                                    <th class="p-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $app)
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="p-3">{{ $app->user?->name ?? 'Unknown' }}</td>
                                        <td class="p-3">{{ $app->job?->title ?? 'N/A' }}</td>
                                        <td class="p-3">{{ $app->job?->employer?->company_name ?? 'N/A' }}</td>
                                        <td class="p-3">{{ $app->job?->consultant?->name ?? '—' }}</td>
                                        <td class="p-3 capitalize">{{ $app->status }}</td>
                                        <td class="p-3">{{ optional($app->created_at)->format('d M Y') }}</td>
                                        <td class="p-3">
                                            <a href="{{ route('admin.job.application.show', [$app->job_id, $app->id]) }}"
                                               class="text-blue-600 hover:underline">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $applications->links() }}
                    </div>
                @else
                    <p class="text-gray-600 text-center py-6">No job applications found.</p>
                @endif
            </x-card>
        </div>
    </div>
</x-layout>
