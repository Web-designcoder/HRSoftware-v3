{{-- resources/views/admin/users/_form.blade.php --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Profile Picture --}}
    <div>
        <label class="block text-sm font-medium">Profile Picture</label>
        <input type="file" name="profile_picture" class="mt-1 block w-full border border-gray-300 rounded-md">
        @if(isset($user) && $user->profile_picture)
            <img src="{{ asset('storage/'.$user->profile_picture) }}" class="mt-2 h-20 w-20 rounded-full object-cover">
        @endif
    </div>

    {{-- Salutation --}}
    <div>
        <label class="block text-sm font-medium">Salutation</label>
        <input type="text" name="salutation" value="{{ old('salutation', $user->salutation ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md">
    </div>

    {{-- First Name --}}
    <div>
        <label class="block text-sm font-medium">First Name</label>
        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md">
    </div>

    {{-- Last Name --}}
    <div>
        <label class="block text-sm font-medium">Last Name</label>
        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md">
    </div>

    {{-- Phone --}}
    <div>
        <label class="block text-sm font-medium">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md">
    </div>

    {{-- Email --}}
    <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md">
    </div>

    {{-- Address Line 1 --}}
    <div>
        <label class="block text-sm font-medium">Address Line 1</label>
        <input type="text" name="address_line1" value="{{ old('address_line1', $user->address_line1 ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md">
    </div>

    {{-- Address Line 2 --}}
    <div>
        <label class="block text-sm font-medium">Address Line 2</label>
        <input type="text" name="address_line2" value="{{ old('address_line2', $user->address_line2 ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md">
    </div>

    {{-- City --}}
    <div>
        <label class="block text-sm font-medium">City</label>
        <input type="text" name="city" value="{{ old('city', $user->city ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md">
    </div>

    {{-- Postcode --}}
    <div>
        <label class="block text-sm font-medium">Postcode</label>
        <input type="text" name="postcode" value="{{ old('postcode', $user->postcode ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md">
    </div>

    {{-- Country --}}
    <div>
        <label class="block text-sm font-medium">Country</label>
        <input type="text" name="country" value="{{ old('country', $user->country ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md">
    </div>

    {{-- Role --}}
    <div>
        <label class="block text-sm font-medium">Role</label>
        <select name="role" class="mt-1 block w-full border-gray-300 rounded-md">
            @foreach($roles as $key => $label)
                <option value="{{ $key }}" @selected(old('role', $user->role ?? '') == $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- Password --}}
    <div>
        <label class="block text-sm font-medium">Password</label>
        <input type="password" name="password" autocomplete="new-password" class="mt-1 block w-full border-gray-300 rounded-md">
        @if($mode === 'edit')
            <p class="text-xs text-gray-500 mt-1">Leave blank to keep existing password.</p>
        @endif
    </div>

    {{-- Confirm Password --}}
    <div>
        <label class="block text-sm font-medium">Confirm Password</label>
        <input type="password" name="password_confirmation" autocomplete="new-password" class="mt-1 block w-full border-gray-300 rounded-md">
    </div>
</div>

{{-- Attachments --}}
<div x-data="attachmentsHandler()" class="mt-8 bg-gray-50 rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-3">Attachments</h3>

    <template x-for="(item, index) in attachmentFields" :key="index">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" x-text="item.label"></label>

            <div
                class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:bg-gray-50"
                @dragover.prevent
                @drop.prevent="handleDrop($event, index)"
                @click="triggerFileDialog(index)"
            >
                <p class="text-sm text-gray-500"
                   x-text="item.multiple ? 'Drop files or click to upload multiple' : 'Drop file or click to upload'"></p>
                <p class="text-xs text-blue-500 mt-1" x-text="item.fileNames.join(', ')"></p>

                <div class="w-full bg-gray-200 rounded-full h-2 mt-3" x-show="item.progress > 0">
                    <div class="bg-blue-600 h-2 rounded-full" :style="`width:${item.progress}%`"></div>
                </div>
            </div>

            <input type="file"
                   :name="item.multiple ? item.name + '[]' : item.name"
                   class="hidden"
                   :multiple="item.multiple"
                   @change="handleSelect($event, index)"
                   form="accountForm" />

            <template x-if="item.existing.length">
                <div class="mt-2 space-y-1">
                    <template x-for="file in item.existing" :key="file">
                        <div class="flex items-center justify-between bg-gray-50 rounded px-2 py-1 mt-1">
                            <a :href="`/storage/${file}`" target="_blank"
                               class="text-blue-600 text-sm underline truncate flex-1 mr-2"
                               x-text="file.split('/').pop()"></a>
                            <button type="button" @click="deleteFile(item, file)"
                                    class="text-red-500 hover:text-red-700" title="Delete file">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21
                                          c.342.052.682.107 1.022.166m-1.022-.166L18.16
                                          19.673A2.25 2.25 0 0115.916 21H8.084a2.25
                                          2.25 0 01-2.244-1.327L4.772 5.79m14.456
                                          0a48.108 48.108 0 00-3.478-.397m-12
                                          .563c.34-.059.68-.114 1.022-.165m0
                                          0A48.11 48.11 0 016.318 5.4m0
                                          0L5.64 19.673A2.25 2.25 0
                                          007.884 21h7.232a2.25 2.25
                                          0 002.244-1.327L19.228 5.79M9.75
                                          4.5v-.75A1.5 1.5 0 0111.25
                                          2.25h1.5a1.5 1.5 0 011.5
                                          1.5v.75m-4.5 0h4.5" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </template>
</div>

{{-- AlpineJS for attachments --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('attachmentsHandler', () => ({
        attachmentFields: [
            { name:'cv', label:'CV', multiple:false, fileNames:[], progress:0, existing:@json($user->cv ? [$user->cv] : []) },
            { name:'medical_check', label:'Recent Medical Check', multiple:false, fileNames:[], progress:0, existing:@json($user->medical_check ? [$user->medical_check] : []) },
            { name:'police_clearance', label:'Recent Police Clearance', multiple:false, fileNames:[], progress:0, existing:@json($user->police_clearance ? [$user->police_clearance] : []) },
            { name:'qualifications', label:'Qualifications / Certificates', multiple:true, fileNames:[], progress:0, existing:@json($user->qualifications ?? []) },
            { name:'other_files', label:'Other Documents', multiple:true, fileNames:[], progress:0, existing:@json($user->other_files ?? []) },
        ],

        triggerFileDialog(i) {
            const inputs = this.$root.querySelectorAll('input[type=file]');
            const input = inputs[i];
            if (input) input.click();
        },
        handleSelect(e,i){ const files=[...e.target.files]; this.upload(files,i); },
        handleDrop(e,i){ const files=[...e.dataTransfer.files]; this.upload(files,i); },

        async upload(files,i){
            const item=this.attachmentFields[i];
            for (const file of files){
                const formData=new FormData();
                formData.append('field',item.name);
                formData.append('file',file);
                try{
                    item.progress=0;
                    const res=await axios.post('{{ route('account.upload') }}',formData,{
                        headers:{'Content-Type':'multipart/form-data'},
                        onUploadProgress:(e)=>{
                            if(e.total){ item.progress=Math.round((e.loaded*100)/e.total); }
                        }
                    });
                    if(res.data.success){
                        item.existing.push(res.data.path);
                        item.fileNames.push(file.name);
                    }
                }catch(err){ console.error('Upload failed',err); }
            }
        },

        async deleteFile(item,file){
            if(!confirm('Delete this file?')) return;
            try{
                const res=await axios.delete('{{ route('account.delete') }}',{ data:{ field:item.name, path:file } });
                if(res.data.success){ item.existing=item.existing.filter(f=>f!==file); }
            }catch(err){ console.error('Delete failed',err); alert('Could not delete file.'); }
        },
    }))
})
</script>
