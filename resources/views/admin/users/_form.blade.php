@php
    $isEdit = ($mode ?? '') === 'edit';
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">First name</label>
        <input name="first_name" value="{{ old('first_name', $user->first_name) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" />
        @error('first_name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Last name</label>
        <input name="last_name" value="{{ old('last_name', $user->last_name) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" />
        @error('last_name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" />
        @error('email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
        <select name="role" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" required>
            @foreach($roles as $r)
                <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>
                    {{ ucfirst($r) }}
                </option>
            @endforeach
        </select>
        @error('role') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Password {{ $isEdit ? '(leave blank to keep current)' : '' }}
        </label>
        <input type="password" name="password" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" {{ $isEdit ? '' : 'required' }} />
        @error('password') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
    </div>
</div>
