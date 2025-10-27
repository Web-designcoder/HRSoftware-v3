<header class="bg-white p-5 shadow-md sticky top-0 z-10 
    {{ auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isConsultant()) ? 'h-14 flex items-center px-6' : '' }}">
    <nav class="flex justify-between text-lg font-medium items-center w-full">
        <ul class="flex space-x-2">
            <li>
                <a href="{{ route('dashboard') }}"><img src="/images/projecthr-logo.webp" alt="Logo" class="max-w-[300px]"></a>
            </li>
        </ul>

        <ul class="flex space-x-6 items-center">
        
        @auth

        {{-- Admin/Consultant Menu --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isConsultant())
                {{-- <li><a href="{{route('dashboard')}}">Dashboard</a></li>
                <x-submenu 
                    title="Campaigns" 
                    :links="[
                        'All Campaigns' => route('jobs.index'),
                        'Create New' => route('jobs.create')
                    ]" 
                />
                <x-submenu 
                    title="Applications" 
                    :links="[
                        'All Applications' => route('admin.applications.index'),
                        'New Application' => route('admin.applications.createStandalone')
                    ]" 
                />
                <x-submenu 
                    title="Users" 
                    :links="[
                        'All Users' => route('admin.users.index'),
                        'Create New User' => route('admin.users.create'),
                        'View Clients' => route('admin.users.clients'),
                        'View Candidates' => route('admin.users.candidates')
                    ]" 
                /> --}}
        @endif

        {{-- Employer Menu --}}
        @if(auth()->user()->isEmployer())
                    <li><a href="{{route('dashboard')}}">Dashboard</a></li>
                    <li><a href="{{route('jobs.index')}}">Campaigns</a></li>
        @endif

        {{-- Candidate Menu --}}
        @if(auth()->user()->isCandidate())
                <li><a href="{{route('dashboard')}}">Dashboard</a></li>
                <li><a href="{{route('jobs.index')}}">Jobs</a></li>
                <li><a href="{{route('my-job-applications.index')}}">My Applications</a></li>
        @endif

        @endauth

            <li>
                <a href="{{route('account.edit')}}">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ asset('storage/'.auth()->user()->profile_picture) }}" class="h-7 w-7 rounded-full object-cover">
                    @else
                        Account
                    @endif
                </a>
            </li>
            <li><form action="{{route('auth.destroy')}}" method="POST"> @csrf @method('DELETE') <button type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                            stroke-width="1.5" stroke="currentColor" 
                            class="w-5 h-5 mr-1">
                            <path stroke-linecap="round" stroke-linejoin="round" 
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3H6.75A2.25 2.25 0 004.5 5.25v13.5A2.25 2.25 0 006.75 21h6.75a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
            </button></form></li>
        <ul class="flex space-x-6 items-center">
    </nav>
</header>
