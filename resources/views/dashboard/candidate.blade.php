<x-layout>
    <div class="container mx-auto py-6">
        <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
        <p>Welcome back, {{ auth()->user()->first_name }}!</p>

        {{-- <div class="mt-4 space-y-2">
            <a href="{{ route('jobs.index') }}" class="text-blue-600 underline">Browse Jobs</a><br>
            <a href="{{ route('my-job-applications.index') }}" class="text-blue-600 underline">My Applications</a><br>

            @if(auth()->user()->is_employer)
                <a href="{{ route('my-jobs.index') }}" class="text-blue-600 underline">Manage My Jobs</a>
            @endif
        </div> --}}

        <div id="dashboard" class="text-white">
            <div class="column column-2-3">
                <x-card class="text-slate-700 text-center flex justify-center flex-col items-center text-3xl">
                    <h2>{{ auth()->user()->name}}</h2> 
                </x-card>

                <x-card class="!bg-[#5ddfe6] flex justify-between flex-col p-8">
                    <h2 class="text-4xl uppercase">Welcome to your candidate portal</h2>
                    <p class="text-3xl">Please click on the following panels to complete your profile.</p>
                    <p class="italic text-xl text-right">- From the PHR Team</p>
                </x-card>
            </div>

            <div class="column">
                <img src="/images/bg-placeholder.png" alt="placeholder" class="rounded-2xl">
                <x-card class="!bg-[#3b76c4] text-center flex justify-end flex-col items-center text-3xl p-8 relative">
                    <a href="{{ route('account.edit') }}"><h2>Your Candidate Profile</h2></a>
                    <a class="p-1 border border-white rounded-[50%] absolute right-5 top-5" href="{{route('account.edit')}}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>

                </x-card>
            </div>

            <div class="column">
                <img src="/images/bg-placeholder.png" alt="placeholder" class="rounded-2xl">
                <x-card class="!bg-[#0cc0df] text-center flex justify-end flex-col items-center text-3xl p-8 relative">
                    <a href="{{ route('jobs.index') }}"><h2>Your Jobs</h2></a>
                    <a class="p-1 border border-white rounded-[50%] absolute right-5 top-5" href="{{route('jobs.index')}}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </x-card>
            </div>

            <div class="column column-2-3">
                <x-card class="!bg-[#1025a1] flex justify-end flex-col text-3xl p-8 relative">
                    <a href="{{ route('account.edit') }}"><h2>Attachments</h2><a>
                    <a href="{{ route('account.edit') }}" class="p-2 border border-white rounded-full absolute right-5 top-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        </a>

                </x-card>
                
                <x-card class="text-3xl !p-0" style="background-image: url('/images/office.webp'); background-size: cover;">
                    <h2 class="bg-[#8d746b8f] w-full p-4 text-center">Get in touch with a consultant!</h2>
                </x-card>
            </div>
            
            
        </div>
    </div>
</x-layout>
