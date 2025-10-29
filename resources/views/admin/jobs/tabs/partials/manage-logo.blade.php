<x-card title="Manage Logo">
    <div class="flex items-start gap-4">
        <div class="w-28 h-28 border border-gray-200 rounded-md bg-gray-50 flex items-center justify-center overflow-hidden">
            <template x-if="logo">
                <img :src="logo" alt="Logo" class="object-contain w-full h-full">
            </template>
            <template x-if="!logo">
                <span class="text-gray-400 text-xs">No Logo</span>
            </template>
        </div>
        <div class="flex-1">
            <input type="file" class="hidden" x-ref="logoInput" @change="uploadLogo">
            <button type="button" @click="$refs.logoInput.click()"
                class="px-3 py-2 bg-[#04215c] text-white text-sm rounded-md hover:bg-[#06318a] transition">
                Upload Logo
            </button>
            <p class="text-xs text-gray-500 mt-2">PNG/JPG up to 4MB.</p>
            <p x-text="flash.logo" class="text-xs mt-2"
               :class="flash.logo_ok ? 'text-green-600' : 'text-red-600'"></p>
        </div>
    </div>
</x-card>
