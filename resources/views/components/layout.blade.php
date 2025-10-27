@props(['showNav' => true, 'bodyClass' => '', 'bodyStyle' => ''])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Job Board</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="text-slate-700 {{ $bodyClass }}" 
      style="{{ $bodyStyle ?: 'background: linear-gradient(to right, #5ddfe6, #014cae);' }}">

    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isConsultant()))
        {{-- ───────────────────────────────────────────────
            ADMIN / CONSULTANT LAYOUT
        ─────────────────────────────────────────────── --}}
        <div class="bg-gray-100 text-slate-700 min-h-screen">

            {{-- Top Bar --}}
            <x-navbar/>
            {{-- <header class="fixed top-0 left-0 right-0 bg-white h-14 shadow z-20 flex justify-between items-center px-6">
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
                        <button type="submit" class="text-sm font-medium hover:text-red-800">
                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                stroke-width="1.5" stroke="currentColor" 
                                class="w-5 h-5 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3H6.75A2.25 2.25 0 004.5 5.25v13.5A2.25 2.25 0 006.75 21h6.75a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                        </button>
                    </form>
                </div>
            </header> --}}

            {{-- Sidebar --}}
            <aside id="sidebar" class="fixed top-15 left-0 w-56 bg-white shadow-md h-[calc(100vh-3.5rem)] py-6 px-4 z-10 transition-all duration-200">
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

            {{-- Main Content --}}
            <main class="ml-0 md:ml-56 pt-16 px-6 pb-10 min-h-screen transition-all duration-200">
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
        </div>

        {{-- Mobile toggle --}}
        <button id="toggleSidebar" class="md:hidden fixed top-3 left-4 z-30 bg-white p-1 rounded shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </button>

        <script>
            const toggleBtn = document.getElementById('toggleSidebar');
            const sidebar = document.getElementById('sidebar');
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('-translate-x-full');
                    sidebar.classList.toggle('hidden');
                });
            }
        </script>

    @else
        {{-- ───────────────────────────────────────────────
            ALL OTHER USERS (Employers, Candidates, Guests)
        ─────────────────────────────────────────────── --}}
        @if($showNav)
            <x-navbar/>
        @endif

        <div id="wrapper" class="mx-auto my-10 max-w-[1400px] min-h-[76vh] {{ $bodyClass }}">
            @if(session('success'))
                <div role="alert" class="my-8 rounded-md border-l-4 border-green-300 bg-green-100 p-4 text-green-700 opacity-75">
                    <p class="font-bold">Success!</p>
                    <p>{{session('success')}}</p>
                </div>
            @endif
            @if (session('error'))
                <div role="alert" class="my-8 rounded-md border-l-4 border-red-300 bg-red-100 p-4 text-red-700 opacity-75">
                    <p class="font-bold">Error!</p>
                    <p>{{session('error')}}</p>
                </div>
            @endif

            {{ $slot }}
        </div>

        @auth
        <footer class="bg-white text-center p-[17px]">
            <p>
                Copyright © {{ date('Y') }} Project HR. 
                All rights reserved || Software by 
                <a href="https://www.webwizards.com.au/" target="_blank">WW</a>
            </p>
        </footer>
        @endauth
    @endif

</body>
</html>
