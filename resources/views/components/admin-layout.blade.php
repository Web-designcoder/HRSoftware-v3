@props(['title' => 'Admin Panel'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - Project HR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-slate-700">

    {{-- Top Bar --}}
    <header class="fixed top-0 left-0 right-0 bg-white h-14 shadow z-20 flex justify-between items-center px-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('dashboard') }}">
                <img src="/images/projecthr-logo.webp" alt="Logo" class="h-8">
            </a>
            <span class="text-sm font-semibold text-slate-600">Admin Dashboard</span>
        </div>

        <div class="flex items-center space-x-4">
            <a href="{{ route('account.edit') }}" class="flex items-center space-x-2">
                @if(auth()->user()->profile_picture)
                    <img src="{{ asset('storage/'.auth()->user()->profile_picture) }}" class="h-8 w-8 rounded-full object-cover">
                @else
                    <span class="text-sm">Account</span>
                @endif
            </a>

            <form action="{{ route('auth.destroy') }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Logout</button>
            </form>
        </div>
    </header>

    {{-- Sidebar --}}
    <aside class="fixed top-14 left-0 w-56 bg-white shadow-md h-[calc(100vh-3.5rem)] py-6 px-4 z-10">
        <nav class="space-y-4 text-sm font-medium">
            <a href="{{ route('dashboard') }}" class="block py-2 px-3 rounded hover:bg-gray-100">Dashboard</a>

            <div>
                <p class="uppercase text-xs text-gray-400 px-3 mb-1">Campaigns</p>
                <a href="{{ route('jobs.index') }}" class="block py-2 px-3 rounded hover:bg-gray-100">All Campaigns</a>
                <a href="{{ route('jobs.create') }}" class="block py-2 px-3 rounded hover:bg-gray-100">Create New</a>
            </div>

            <div>
                <p class="uppercase text-xs text-gray-400 px-3 mb-1">Applications</p>
                <a href="{{ route('admin.applications.index') }}" class="block py-2 px-3 rounded hover:bg-gray-100">All Applications</a>
                <a href="{{ route('admin.applications.createStandalone') }}" class="block py-2 px-3 rounded hover:bg-gray-100">New Application</a>
            </div>

            <div>
                <p class="uppercase text-xs text-gray-400 px-3 mb-1">Users</p>
                <a href="{{ route('admin.users.index') }}" class="block py-2 px-3 rounded hover:bg-gray-100">All Users</a>
                <a href="{{ route('admin.users.create') }}" class="block py-2 px-3 rounded hover:bg-gray-100">Create New</a>
                <a href="{{ route('admin.users.clients') }}" class="block py-2 px-3 rounded hover:bg-gray-100">Clients</a>
                <a href="{{ route('admin.users.candidates') }}" class="block py-2 px-3 rounded hover:bg-gray-100">Candidates</a>
            </div>
        </nav>
    </aside>

    {{-- Main content area --}}
    <main class="ml-56 pt-16 px-8 pb-10 min-h-screen">
        @if(session('success'))
            <div class="mb-6 rounded-md border-l-4 border-green-300 bg-green-100 p-4 text-green-700">
                <p class="font-bold">Success!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 rounded-md border-l-4 border-red-300 bg-red-100 p-4 text-red-700">
                <p class="font-bold">Error!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{ $slot }}
    </main>

</body>
</html>
