@props(['title' => null])

<article {{$attributes->class(['rounded-lg bg-white p-4 shadow-sm'])}}>
     @if($title)
        <h2 class="text-lg font-semibold text-[#04215c] mb-3">{{ $title }}</h2>
    @endif
    {{ $slot }}
</article>