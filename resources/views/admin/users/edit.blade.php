<x-layout>
    <div class="container mx-auto py-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Edit User</h1>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">← Back</a>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-md bg-green-50 text-green-800 px-4 py-3">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-md bg-red-50 text-red-800 px-4 py-3">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-md bg-red-50 text-red-800 px-4 py-3">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white rounded-lg shadow p-6 space-y-5">
            @csrf
            @method('PUT')

            @include('admin.users._form', ['user' => $user, 'roles' => $roles, 'mode' => 'edit'])

            <div class="flex items-center justify-between">
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                      onsubmit="return confirm('Delete this user? This cannot be undone.');" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Delete user</button>
                </form>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button class="px-4 py-2 rounded-md bg-[#04215c] text-white hover:bg-[#06318a]">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</x-layout>
