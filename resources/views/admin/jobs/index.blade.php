<x-layout>
    <x-breadcrumbs :links="['Admin Jobs' => '#']" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#04215c]">Manage Jobs</h1>
        <a href="{{ route('admin.jobs.create') }}"
           class="inline-block px-4 py-2 bg-[#04215c] text-white rounded-md hover:bg-[#06318a] transition">
            + New Job
        </a>
    </div>

    <div class="bg-white shadow rounded-lg">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2">Title</th>
                    <th class="px-4 py-2">Employer</th>
                    <th class="px-4 py-2">Consultant</th>
                    <th class="px-4 py-2">Posted</th>
                    <th class="px-4 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr class="border-b">
                        <td class="px-4 py-2 font-semibold">{{ $job->title }}</td>
                        <td class="px-4 py-2">{{ $job->employer?->name }}</td>
                        <td class="px-4 py-2">{{ $job->consultant?->first_name }} {{ $job->consultant?->last_name }}</td>
                        <td class="px-4 py-2">{{ $job->date_posted?->format('d M Y') }}</td>
                        <td class="px-4 py-2 text-right space-x-2">
                            <a href="{{ route('admin.jobs.edit', $job) }}"
                               class="px-3 py-1 bg-[#04215c] text-white rounded hover:bg-[#06318a]">Edit</a>
                            <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Delete this job?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">No jobs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $jobs->links() }}
    </div>
</x-layout>
