@props([
    'name',
    'options' => [],
    'selected' => null,
    'allOption' => null,
    'formRef' => null
])

<div class="space-y-2">
    @if($allOption)
        <label class="flex items-center space-x-2">
            <input 
                type="radio" 
                name="{{ $name }}" 
                value="" 
                {{ !request($name) ? 'checked' : '' }}
                @if($formRef) onchange="document.getElementById('{{ $formRef }}').submit()" @endif
                class="text-blue-600"
            >
            <span class="text-sm">{{ $allOption }}</span>
        </label>
    @endif

    @foreach($options as $label => $value)
        <label class="flex items-center space-x-2">
            <input 
                type="radio" 
                name="{{ $name }}" 
                value="{{ $value }}" 
                {{ request($name) == $value || $selected == $value ? 'checked' : '' }}
                @if($formRef) onchange="document.getElementById('{{ $formRef }}').submit()" @endif
                class="text-blue-600"
            >
            <span class="text-sm">{{ $label }}</span>
        </label>
    @endforeach
</div>