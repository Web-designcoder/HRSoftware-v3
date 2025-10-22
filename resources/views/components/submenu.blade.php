@props(['title', 'links' => []])

<li x-data="{ open: false }"
    @mouseenter="open = true"
    @mouseleave="open = false"
    class="relative">

    <!-- Dropdown Trigger -->
    <button class="flex items-center gap-1 hover:text-blue-600 transition">
        {{ $title }}
        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-4 w-4 transform transition-transform"
             :class="{ 'rotate-180': open }"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open"
         x-transition
         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-100 z-50">
        <ul class="py-1 text-sm text-gray-700">
            @foreach($links as $text => $url)
                <li>
                    <a href="{{ $url }}"
                       class="block px-4 py-2 hover:bg-gray-100">
                        {{ $text }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</li>
