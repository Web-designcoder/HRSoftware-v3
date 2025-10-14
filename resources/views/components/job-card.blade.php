<x-card class="mb-4">
    <div class="flex justify-between">
        <h2 class="text-lg font-medium">{{ $job->title }}</h2>
        <div class="text-slate-500">
            ${{ number_format($job->salary) }}
        </div>
    </div>

    <div class="mb-4 flex justify-between text-sm text-slate-500 items-center">
        <div class="flex items-center space-x-4">
            <div>{{$job->employer->company_name}}</div>
            <div>{{$job->location}}</div>
            @if ($job->deleted_at)
                <span class="text-xs text-red-500">Deleted</span>
                
            @endif
        </div>
        <div class="flex space-x-1 text-s">
            <a href="{{ route('jobs.index', ['experience' => $job->experience]) }}">
                <x-tag>{{Str::ucfirst($job->experience)}}</x-tag>
            </a>
            <a href="{{ route('jobs.index', ['category' => $job->category]) }}"> 
                <x-tag>{{$job->category}}</x-tag> 
            </a>
        </div>
    </div>

    {{ $slot }}
</x-card>