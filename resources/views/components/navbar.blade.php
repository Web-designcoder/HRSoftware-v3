<header class="bg-white p-5 shadow-md sticky top-0 z-10">
    <nav class="flex justify-between text-lg font-medium items-center">
        <ul class="flex space-x-2">
            <li>
                <a href="{{ route('dashboard') }}"><img src="/images/projecthr-logo.webp" alt="Logo" class="max-w-[300px]"></a>
            </li>
        </ul>
        
        @auth

        {{-- Admin/Consultant Menu --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isConsultant())
        <ul class="flex space-x-6 items-center">
                <li><a href="{{route('dashboard')}}">Dashboard</a></li>
                @php
    $navbarCampaignLinks = [
        'All Campaigns' => route('jobs.index'),
        'Create New' => route('jobs.create'),
    ];
@endphp
<x-submenu title="Campaigns" :links="$navbarCampaignLinks" />

                <li><a href="{{ route('admin.applications.index') }}">Applications</a></li>
                @php
    $navbarUserLinks = [
        'All Users' => route('admin.users.index'),
        'Create New User' => route('admin.users.create'),
    ];
@endphp
<x-submenu title="Users" :links="$navbarUserLinks" />

                <li>
                    <a href="{{route('account.edit')}}">
                        @if(auth()->user()->profile_picture)
                            <img src="{{ asset('storage/'.auth()->user()->profile_picture) }}" class="mt-2 h-7 w-7 rounded-full object-cover">
                        @else
                            Account
                        @endif
                    </a>
                </li>
                <li><form action="{{route('auth.destroy')}}" method="POST"> @csrf @method('DELETE') <button type="submit">Logout</button></form></li>
            {{-- @else
                <li><a href="{{route('login')}}">Sign in</a></li>
            @endauth --}}
        </ul>
        @endif

        {{-- Employer Menu --}}
        @if(auth()->user()->isEmployer())
            <ul class="flex space-x-6 items-center">
                    <li><a href="{{route('dashboard')}}">Dashboard</a></li>
                    <li><a href="{{route('jobs.index')}}">Campaigns</a></li>
                    <li>
                        <a href="{{route('account.edit')}}">
                            @if(auth()->user()->profile_picture)
                                <img src="{{ asset('storage/'.auth()->user()->profile_picture) }}" class="mt-2 h-7 w-7 rounded-full object-cover">
                            @else
                                Account
                            @endif
                        </a>
                    </li>
                    <li><form action="{{route('auth.destroy')}}" method="POST"> @csrf @method('DELETE') <button type="submit">Logout</button></form></li>
            </ul>
        @endif

        {{-- Candidate Menu --}}
        @if(auth()->user()->isCandidate())
        <ul class="flex space-x-6 items-center">
                <li><a href="{{route('dashboard')}}">Dashboard</a></li>
                <li><a href="{{route('jobs.index')}}">Jobs</a></li>
                <li><a href="{{route('my-job-applications.index')}}">My Applications</a></li>
                <li>
                    <a href="{{route('account.edit')}}">
                        @if(auth()->user()->profile_picture)
                            <img src="{{ asset('storage/'.auth()->user()->profile_picture) }}" class="mt-2 h-7 w-7 rounded-full object-cover">
                        @else
                            Account
                        @endif
                    </a>
                </li>
                <li><form action="{{route('auth.destroy')}}" method="POST"> @csrf @method('DELETE') <button type="submit">Logout</button></form></li>
        </ul>
        @endif

        @endauth
    </nav>
</header>
