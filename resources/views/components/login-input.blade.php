<div class="relative" @if(($type ?? '') === 'password') x-data="{ show: false }" @endif>
    {{-- Left-side icon --}}
    @if(($type ?? '') === 'password')
        <!-- padlock -->
        <svg xmlns="http://www.w3.org/2000/svg"
             class="absolute left-3 top-1/2 h-7 w-7 -translate-y-1/2 pointer-events-none"
             fill="none" viewBox="0 0 24 24" stroke="black" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 15v2m0-6a4 4 0 00-4 4v2a4 4 0 008 0v-2a4 4 0 00-4-4zm0 0V7a2 2 0 114 0v2"/>
        </svg>
    @else
        <!-- user -->
        <svg xmlns="http://www.w3.org/2000/svg"
             class="absolute left-3 top-1/2 h-7 w-7 -translate-y-1/2 pointer-events-none"
             fill="none" viewBox="0 0 24 24" stroke="black" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M5.121 17.804A9.968 9.968 0 0112 15c2.21 0 4.245.716 5.879 1.918M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    @endif

    {{-- Input --}}
    <input
        @if(($type ?? '') === 'password')
            :type="show ? 'text' : 'password'"
        @else
            type="{{ $type ?? 'text' }}"
        @endif
        name="{{ $name ?? '' }}"
        id="{{ $name ?? '' }}"
        placeholder="{{ $placeholder ?? '' }}"
        value="{{ old($name ?? '', $value ?? '') }}"
        @class([
            'w-full !rounded-2xl border-0 !py-5 !pl-12 !pr-10 text-sm bg-white/90 ring-1 placeholder:text-slate-400 focus:ring-[#051f5c]',
            'ring-slate-300' => !$errors->has($name ?? ''),
            'ring-red-700' => $errors->has($name ?? ''),
        ])
    >

    {{-- Password toggle --}}
    @if(($type ?? '') === 'password')
        <button type="button"
                class="absolute inset-y-0 right-3 flex items-center"
                @click="show = !show">
            <!-- eye -->
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7"
                 fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <!-- eye-off -->
            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7"
                 fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="1.5" style="display:none;">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 012.044-3.362m2.122-2.122A9.958 9.958 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.132 5.411M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
            </svg>
        </button>
    @endif

    {{-- Error --}}
    @error($name ?? '')
        <div class="mt-1 text-xs text-red-500">{{ $message }}</div>
    @enderror
</div>
