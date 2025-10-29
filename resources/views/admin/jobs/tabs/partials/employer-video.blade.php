<x-card title="Employer Video Introduction">
    <div 
        x-data="{
            employerVideo: '{{ $job->employer_intro_video_url }}' || null,
            uploading: false,

            async uploadEmployerVideo(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                const formData = new FormData();
                formData.append('video', file);

                this.uploading = true;

                const res = await fetch('{{ route('admin.jobs.video.employer.upload', $job) }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });

                let data;
                try {
                    data = await res.json();
                } catch (err) {
                    const text = await res.text();
                    console.error('Upload failed:', text);
                    alert('Upload failed — check console for details.');
                    this.uploading = false;
                    return;
                }

                if (data.ok && data.url) {
                    this.employerVideo = data.url;
                } else {
                    alert('Video upload failed.');
                }

                this.uploading = false;
            },

            async deleteEmployerVideo() {
                if (!confirm('Delete this video?')) return;

                const res = await fetch('{{ route('admin.jobs.video.employer.delete', $job) }}', {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                });

                const data = await res.json();
                if (data.ok) {
                    this.employerVideo = null;
                } else {
                    alert('Could not delete video.');
                }
            }
        }"
    >
        <template x-if="!employerVideo">
            <div class="text-sm text-gray-500">No video uploaded yet.</div>
        </template>

        <template x-if="employerVideo">
            <div class="relative">
                <video controls class="rounded-md w-full mt-2">
                    <source :src="employerVideo" type="video/mp4">
                </video>
                <!-- Bin icon only shows when video exists -->
                <button 
                    @click="deleteEmployerVideo"
                    class="absolute top-2 right-2 text-white bg-red-600 hover:bg-red-700 rounded-full p-2"
                    title="Delete video"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>

        <div class="mt-3 flex gap-2 items-center">
            <input 
                type="file" 
                class="hidden" 
                x-ref="employerVideoInput" 
                accept="video/mp4,video/webm,video/quicktime,video/x-msvideo" 
                @change="uploadEmployerVideo">

            <button 
                @click="$refs.employerVideoInput.click()" 
                class="w-1/2 px-3 py-2 bg-[#04215c] text-white text-sm rounded-md hover:bg-[#06318a] flex items-center justify-center"
                :disabled="uploading"
            >
                <template x-if="uploading">
                    <svg class="animate-spin h-4 w-4 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </template>
                <span x-text="uploading ? 'Uploading...' : 'Upload'"></span>
            </button>

            <button 
                @click.prevent="alert('Record video feature coming soon')" 
                class="w-1/2 px-3 py-2 border border-[#04215c] text-[#04215c] text-sm rounded-md hover:bg-[#f3f3f3]">
                Record
            </button>
        </div>

        <p class="mt-2 text-xs text-gray-500">
            Allowed file formats: <span class="font-medium text-gray-700">MP4, WEBM, MOV, AVI</span> (max 50MB)
        </p>
    </div>
</x-card>
