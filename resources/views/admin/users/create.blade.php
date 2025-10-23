<x-layout>
    <div class="container mx-auto py-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Add User</h1>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 bg-white">← Back</a>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-md bg-red-50 text-red-800 px-4 py-3">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded-lg shadow p-6 space-y-5">
            @csrf

            @include('admin.users._form', ['user' => $user, 'roles' => $roles, 'mode' => 'create'])

            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Cancel</a>
                <button class="px-4 py-2 rounded-md bg-[#04215c] text-white hover:bg-[#06318a]">Create User</button>
            </div>
        </form>
    </div>
</x-layout>
