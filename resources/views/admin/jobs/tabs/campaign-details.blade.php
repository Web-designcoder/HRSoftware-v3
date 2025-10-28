<div
    x-data='campaignDetails({
        "detailsUrl": "{{ route('admin.jobs.details.update', $job) }}",
        "overviewsUrl": "{{ route('admin.jobs.overviews.update', $job) }}",
        "logoUrl": "{{ route('admin.jobs.logo.upload', $job) }}",
        "csrf": "{{ csrf_token() }}",
        "job": {!! json_encode($job->only(["title","location","city","country","managed_by","date_posted","salary","experience","category","employer_id"])) !!},
        "logo": "{{ $job->company_logo_url }}",
        "background": {!! json_encode($job->description ?? '') !!},
        "assignment": {!! json_encode($job->assignment_overview ?? '') !!}
    })'
    class="grid grid-cols-1 lg:grid-cols-3 gap-6"
>
    {{-- COLUMN 1 --}}
    <div class="space-y-6">

        {{-- Manage Logo --}}
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
                    <button type="button"
                            @click="$refs.logoInput.click()"
                            class="px-3 py-2 bg-[#04215c] text-white text-sm rounded-md hover:bg-[#06318a] transition">
                        Upload Logo
                    </button>
                    <p class="text-xs text-gray-500 mt-2">PNG/JPG up to 4MB.</p>
                    <p x-text="flash.logo" class="text-xs mt-2" :class="flash.logo_ok ? 'text-green-600' : 'text-red-600'"></p>
                </div>
            </div>
        </x-card>

        {{-- Campaign Status (UI only for now) --}}
        <x-card title="Campaign Status">
            <select class="w-full rounded-md border-gray-300 focus:ring-[#04215c] focus:border-[#04215c]" disabled>
                <option selected>Draft</option>
                <option>Live</option>
                <option>Unsuccessful</option>
                <option>Complete</option>
                <option>Archived</option>
            </select>
            <p class="text-xs text-gray-500 mt-2">Status controls coming soon.</p>
        </x-card>

        {{-- Candidate Video Assessment --}}
        <x-card title="Candidate Video Assessment"
                x-data="videoCard({
                    getUrl: '{{ $job->candidate_assessment_video_url }}',
                    uploadUrl: '{{ route('admin.jobs.video.candidate.upload', $job) }}',
                    deleteUrl: '{{ route('admin.jobs.video.candidate.delete', $job) }}',
                    csrf: '{{ csrf_token() }}'
                })">
            <template x-if="!url">
                <div class="flex items-center gap-3">
                    <input type="file" class="hidden" x-ref="fileInput" @change="upload">
                    <button class="px-3 py-2 bg-[#04215c] text-white rounded-md hover:bg-[#06318a]" @click="$refs.fileInput.click()">Upload</button>
                    <span class="text-sm text-gray-500">MP4/MOV/AVI up to 50MB.</span>
                </div>
            </template>
            <template x-if="url">
                <div class="space-y-2">
                    <video :src="url" class="w-full rounded" controls></video>
                    <div class="flex gap-2">
                        <button class="px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300" @click="remove">Delete</button>
                    </div>
                </div>
            </template>
            <p class="text-xs mt-2" :class="ok ? 'text-green-600' : 'text-red-600'" x-text="msg"></p>
        </x-card>

        {{-- Employer Video Introduction --}}
        <x-card title="Employer Video Introduction"
                x-data="videoCard({
                    getUrl: '{{ $job->employer_intro_video_url }}',
                    uploadUrl: '{{ route('admin.jobs.video.employer.upload', $job) }}',
                    deleteUrl: '{{ route('admin.jobs.video.employer.delete', $job) }}',
                    csrf: '{{ csrf_token() }}'
                })">
            <template x-if="!url">
                <div class="flex items-center gap-3">
                    <input type="file" class="hidden" x-ref="fileInput" @change="upload">
                    <button class="flex-1 px-3 py-2 bg-[#04215c] text-white rounded-md hover:bg-[#06318a]" @click="$refs.fileInput.click()">Upload</button>
                    <button class="flex-1 px-3 py-2 bg-gray-200 text-gray-800 rounded-md" disabled>Record</button>
                </div>
            </template>
            <template x-if="url">
                <div class="space-y-2">
                    <video :src="url" class="w-full rounded" controls></video>
                    <div class="flex gap-2">
                        <button class="px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300" @click="remove">Delete</button>
                    </div>
                </div>
            </template>
            <p class="text-xs mt-2" :class="ok ? 'text-green-600' : 'text-red-600'" x-text="msg"></p>
        </x-card>

        {{-- Campaign Documents --}}
        <x-card title="Campaign Documents"
                x-data="fileRows({
                    listUrl: '{{ route('admin.jobs.documents.index', $job) }}',
                    uploadUrl: '{{ route('admin.jobs.documents.store', $job) }}',
                    deleteBaseUrl: '{{ url('admin/jobs/'.$job->id.'/documents') }}',
                    reorderUrl: '{{ route('admin.jobs.documents.reorder', $job) }}',
                    csrf: '{{ csrf_token() }}'
                })">
            <div class="space-y-3">
                <template x-for="row in rows" :key="row.id">
                    <div class="flex items-center gap-2">
                        <button class="text-red-500 hover:text-red-700" @click="remove(row.id)">🗑️</button>
                        <input type="text" class="flex-1 rounded-md border-gray-300 text-sm" :value="row.name" disabled>
                        <a :href="row.url" target="_blank" class="px-2 py-1 bg-gray-100 rounded text-sm" x-show="row.url">View</a>
                        <span class="text-xs text-gray-400" x-text="'#'+row.sort_order"></span>
                    </div>
                </template>

                {{-- Add new row --}}
                <div class="flex items-center gap-2">
                    <input x-model="newName" type="text" placeholder="Document name" class="flex-1 rounded-md border-gray-300 text-sm">
                    <input type="file" class="hidden" x-ref="fileInput" @change="create">
                    <button class="px-2 py-1 bg-[#04215c] text-white rounded text-sm" @click="$refs.fileInput.click()">Select File</button>
                </div>

                <div class="flex justify-end">
                    <button class="text-sm bg-[#04215c] text-white px-3 py-1 rounded-md hover:bg-[#06318a]" @click="reorder">
                        Save Documents Order
                    </button>
                </div>
            </div>
        </x-card>

        {{-- Required Candidate Documents --}}
        <x-card title="Required Candidate Documents"
                x-data="fileRows({
                    listUrl: '{{ route('admin.jobs.reqdocs.index', $job) }}',
                    uploadUrl: '{{ route('admin.jobs.reqdocs.store', $job) }}',
                    deleteBaseUrl: '{{ url('admin/jobs/'.$job->id.'/required-docs') }}',
                    reorderUrl: '{{ route('admin.jobs.reqdocs.reorder', $job) }}',
                    csrf: '{{ csrf_token() }}',
                    fileOptional: true
                })">
            <div class="space-y-3">
                <template x-for="row in rows" :key="row.id">
                    <div class="flex items-center gap-2">
                        <button class="text-red-500 hover:text-red-700" @click="remove(row.id)">🗑️</button>
                        <input type="text" class="flex-1 rounded-md border-gray-300 text-sm" :value="row.name" disabled>
                        <a :href="row.url" target="_blank" class="px-2 py-1 bg-gray-100 rounded text-sm" x-show="row.url">Template</a>
                        <span class="text-xs text-gray-400" x-text="'#'+row.sort_order"></span>
                    </div>
                </template>

                {{-- Add new row --}}
                <div class="flex items-center gap-2">
                    <input x-model="newName" type="text" placeholder="Document name (e.g., Resume)" class="flex-1 rounded-md border-gray-300 text-sm">
                    <input type="file" class="hidden" x-ref="fileInput" @change="create">
                    <button class="px-2 py-1 bg-gray-100 rounded text-sm" @click="$refs.fileInput.click()">Upload (optional)</button>
                    <button class="px-2 py-1 bg-[#04215c] text-white rounded text-sm" @click="createWithoutFile">Add</button>
                </div>

                <div class="flex justify-end">
                    <button class="text-sm bg-[#04215c] text-white px-3 py-1 rounded-md hover:bg-[#06318a]" @click="reorder">
                        Save Order
                    </button>
                </div>
            </div>
        </x-card>

        {{-- Key Competency Questions --}}
        <x-card title="Key Competency Questions"
                x-data="questionsBox({
                    listUrl: '{{ route('admin.jobs.questions.index', $job) }}',
                    seedUrl: '{{ route('admin.jobs.questions.seed', $job) }}',
                    createUrl: '{{ route('admin.jobs.questions.store', $job) }}',
                    toggleBaseUrl: '{{ url('admin/jobs/'.$job->id.'/questions') }}',
                    deleteBaseUrl: '{{ url('admin/jobs/'.$job->id.'/questions') }}',
                    reorderUrl: '{{ route('admin.jobs.questions.reorder', $job) }}',
                    csrf: '{{ csrf_token() }}'
                })">
            <div class="space-y-3">
                <div class="text-sm text-gray-600">Toggle defaults or add custom questions.</div>

                <div class="space-y-2">
                    <template x-for="q in items" :key="q.id">
                        <label class="flex items-center justify-between gap-2 text-sm bg-gray-50 px-2 py-1 rounded">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" class="rounded text-[#04215c]" :checked="q.is_enabled" @change="toggle(q.id)">
                                <span x-text="q.question"></span>
                                <span x-show="q.is_default" class="ml-2 text-xs text-gray-500">(default)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400" x-text="'#'+q.sort_order"></span>
                                <button class="text-red-500 hover:text-red-700" @click="remove(q.id)" x-show="!q.is_default">🗑️</button>
                            </div>
                        </label>
                    </template>
                </div>

                <div class="flex items-center gap-2">
                    <input type="text" x-model="newQuestion" placeholder="Add custom question..." class="flex-1 rounded-md border-gray-300 text-sm">
                    <button class="px-3 py-1 bg-[#04215c] text-white rounded text-sm" @click="create">Add</button>
                    <button class="px-3 py-1 bg-gray-200 rounded text-sm" @click="reorder">Save Order</button>
                </div>
            </div>
        </x-card>
    </div>

    {{-- COLUMN 2 --}}
    <div class="space-y-6">
        {{-- Campaign Details (AJAX) --}}
        <x-card title="Campaign Details">
            <div class="space-y-3">
                {{-- Employer --}}
                <label class="block text-sm">Employer</label>
                <select x-model="form.employer_id" class="w-full rounded-md border-gray-300 text-sm focus:ring-[#04215c] focus:border-[#04215c]">
                    <option value="">— Select Employer —</option>
                    @foreach($employers as $employer)
                        <option value="{{ $employer->id }}">{{ $employer->name }} — {{ $employer->city }}, {{ $employer->country }}</option>
                    @endforeach
                </select>

                <label class="block text-sm mt-2">Job Position</label>
                <input x-model="form.title" type="text" class="w-full rounded-md border-gray-300 text-sm">

                <div class="grid grid-cols-2 gap-3 mt-2">
                    <div>
                        <label class="block text-sm">City</label>
                        <input x-model="form.city" type="text" class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm">Location (e.g., Office)</label>
                        <input x-model="form.location" type="text" class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                </div>

                <label class="block text-sm mt-2">Country</label>
                <input x-model="form.country" type="text" class="w-full rounded-md border-gray-300 text-sm" placeholder="Australia">

                <div class="grid grid-cols-2 gap-3 mt-2">
                    <div>
                        <label class="block text-sm">Experience</label>
                        <select x-model="form.experience" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="">—</option>
                            @foreach(\App\Models\Job::$experience as $exp)
                                <option value="{{ $exp }}">{{ ucfirst($exp) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm">Category</label>
                        <select x-model="form.category" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="">—</option>
                            @foreach(\App\Models\Job::$category as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-2">
                    <div>
                        <label class="block text-sm">Date Posted</label>
                        <input x-model="form.date_posted" type="date" class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm">Salary</label>
                        <input x-model="form.salary" type="number" step="0.01" class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                </div>

                <label class="block text-sm mt-2">Managed By (Consultant name)</label>
                <input x-model="form.managed_by" type="text" class="w-full rounded-md border-gray-300 text-sm" placeholder="e.g., John Smith">

                <div class="mt-4 flex items-center gap-3">
                    <button type="button"
                            @click="saveDetails"
                            class="px-3 py-2 bg-[#04215c] text-white text-sm rounded-md hover:bg-[#06318a] transition">
                        Update
                    </button>
                    <span x-text="flash.details" class="text-xs" :class="flash.details_ok ? 'text-green-600' : 'text-red-600'"></span>
                </div>
            </div>
        </x-card>

        {{-- McQuaig (placeholder) --}}
        <x-card title="McQuaig Institute Job Survey">
            <p class="text-sm text-gray-700 mb-4 leading-relaxed">
                Profile Type: <strong>Generalist</strong><br><br>
                (Chart and survey details to come)
            </p>
            <div class="h-40 border border-dashed border-gray-300 rounded-md flex items-center justify-center text-gray-400 text-sm">
                Chart Placeholder
            </div>
        </x-card>

        {{-- Terms & Conditions --}}
        <x-card title="Terms & Conditions"
                x-data="termsBox({
                    getUrl: '{{ route('admin.jobs.terms.get', $job) }}',
                    saveUrl: '{{ route('admin.jobs.terms.update', $job) }}',
                    csrf: '{{ csrf_token() }}',
                    candidateInit: {!! json_encode($job->terms_candidate ?? '') !!},
                    employerInit: {!! json_encode($job->terms_employer ?? '') !!}
                })">
            <div x-data="{ tab: 'candidate' }">
                <div class="border-b border-gray-200 mb-3 flex gap-4">
                    <button type="button" @click="tab='candidate'" :class="tab==='candidate' ? 'text-[#04215c] font-semibold' : 'text-gray-500'">
                        Candidate T&amp;C
                    </button>
                    <button type="button" @click="tab='employer'" :class="tab==='employer' ? 'text-[#04215c] font-semibold' : 'text-gray-500'">
                        Employer T&amp;C
                    </button>
                </div>
                <div x-show="tab==='candidate'">
                    <textarea class="w-full rounded-md border-gray-300 text-sm" rows="6" x-model="$parent.candidate"></textarea>
                </div>
                <div x-show="tab==='employer'">
                    <textarea class="w-full rounded-md border-gray-300 text-sm" rows="6" x-model="$parent.employer"></textarea>
                </div>
                <button class="mt-3 px-3 py-2 bg-[#04215c] text-white text-sm rounded-md hover:bg-[#06318a] transition" @click="$parent.save">
                    Save
                </button>
                <span class="text-xs ml-2" :class="$parent.ok ? 'text-green-600' : 'text-red-600'" x-text="$parent.msg"></span>
            </div>
        </x-card>

        {{-- Background & Assignment Overview (AJAX) --}}
        <x-card title="Background & Assignment Overview">
            <div x-data="{ tab: 'background' }">
                <div class="border-b border-gray-200 mb-3 flex gap-4">
                    <button type="button" @click="tab='background'" :class="tab==='background' ? 'text-[#04215c] font-semibold' : 'text-gray-500'">
                        Background Overview
                    </button>
                    <button type="button" @click="tab='assignment'" :class="tab==='assignment' ? 'text-[#04215c] font-semibold' : 'text-gray-500'">
                        Assignment Overview
                    </button>
                </div>

                <div x-show="tab==='background'" class="space-y-2">
                    <textarea x-model="background" class="w-full rounded-md border-gray-300 text-sm" rows="8" placeholder="Background overview..."></textarea>
                </div>
                <div x-show="tab==='assignment'" class="space-y-2">
                    <textarea x-model="assignment" class="w-full rounded-md border-gray-300 text-sm" rows="8" placeholder="Assignment overview..."></textarea>
                </div>

                <div class="mt-3 flex items-center gap-3">
                    <button type="button"
                            @click="saveOverviews"
                            class="px-3 py-2 bg-[#04215c] text-white text-sm rounded-md hover:bg-[#06318a] transition">
                        Save
                    </button>
                    <span x-text="flash.overviews" class="text-xs" :class="flash.overviews_ok ? 'text-green-600' : 'text-red-600'"></span>
                </div>
            </div>
        </x-card>
    </div>

    {{-- COLUMN 3 --}}
    <div class="space-y-6">
        {{-- Employer Contacts (you already have this wired and working) --}}
        @include('admin.jobs.tabs.partials.employer-contacts', ['job' => $job])

        <x-card title="Fees & Remunerations">
            <p class="text-sm text-gray-600">Currency/fee fields to wire up later (no columns yet).</p>
        </x-card>
    </div>
</div>

{{-- Helpers + Alpine components --}}
<script>
function campaignDetails(cfg) {
    return {
        // state
        form: {
            employer_id: cfg.job.employer_id ?? '',
            title: cfg.job.title ?? '',
            location: cfg.job.location ?? '',
            city: cfg.job.city ?? '',
            country: cfg.job.country ?? 'Australia',
            managed_by: cfg.job.managed_by ?? '',
            date_posted: (cfg.job.date_posted ?? '').substring(0,10),
            salary: cfg.job.salary ?? '',
            experience: cfg.job.experience ?? '',
            category: cfg.job.category ?? ''
        },
        background: cfg.background || '',
        assignment: cfg.assignment || '',
        logo: cfg.logo || null,
        flash: {
            details: '',
            details_ok: false,
            overviews: '',
            overviews_ok: false,
            logo: '',
            logo_ok: false
        },

        async saveDetails() {
            this.flash.details = 'Saving...'; this.flash.details_ok = false;
            try {
                const res = await fetch(cfg.detailsUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': cfg.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });
                if (!res.ok) throw await res.json();
                this.flash.details = 'Saved!';
                this.flash.details_ok = true;
            } catch (e) {
                this.flash.details = 'Failed to save.';
                this.flash.details_ok = false;
                console.error(e);
            }
        },

        async saveOverviews() {
            this.flash.overviews = 'Saving...'; this.flash.overviews_ok = false;
            try {
                const res = await fetch(cfg.overviewsUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': cfg.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        background: this.background,
                        assignment: this.assignment
                    })
                });
                if (!res.ok) throw await res.json();
                this.flash.overviews = 'Saved!';
                this.flash.overviews_ok = true;
            } catch (e) {
                this.flash.overviews = 'Failed to save.';
                this.flash.overviews_ok = false;
                console.error(e);
            }
        },

        async uploadLogo(e) {
            this.flash.logo = 'Uploading...'; this.flash.logo_ok = false;
            const file = e.target.files[0];
            if (!file) return;
            const fd = new FormData();
            fd.append('company_logo', file);
            try {
                const res = await fetch(cfg.logoUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': cfg.csrf, 'Accept': 'application/json' },
                    body: fd
                });
                if (!res.ok) throw await res.json();
                const data = await res.json();
                this.logo = data.url;
                this.flash.logo = 'Uploaded!';
                this.flash.logo_ok = true;
            } catch (err) {
                this.flash.logo = 'Upload failed.';
                this.flash.logo_ok = false;
                console.error(err);
            } finally {
                e.target.value = '';
            }
        }
    }
}

function videoCard(cfg){
    return {
        url: cfg.getUrl || null,
        msg: '',
        ok: true,
        async upload(e){
            this.msg = 'Uploading...'; this.ok = true;
            const f = e.target.files[0];
            if(!f) return;
            const fd = new FormData();
            fd.append('video', f);
            try{
                const res = await fetch(cfg.uploadUrl, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': cfg.csrf, 'Accept':'application/json'},
                    body: fd
                });
                const json = await res.json();
                if(!json.ok) throw json;
                this.url = json.url;
                this.msg = 'Uploaded!';
                this.ok = true;
            }catch(err){
                console.error(err);
                this.msg = 'Upload failed.';
                this.ok = false;
            }finally{
                e.target.value = '';
            }
        },
        async remove(){
            this.msg = 'Deleting...';
            try{
                const res = await fetch(cfg.deleteUrl, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': cfg.csrf, 'Accept':'application/json'}
                });
                const json = await res.json();
                if(!json.ok) throw json;
                this.url = null;
                this.msg = 'Deleted';
                this.ok = true;
            }catch(e){
                console.error(e);
                this.msg = 'Delete failed.';
                this.ok = false;
            }
        }
    }
}

function fileRows(cfg){
    return {
        rows: [],
        order: [],
        newName: '',
        async init(){
            await this.refresh();
        },
        async refresh(){
            try{
                const res = await fetch(cfg.listUrl, { headers: {'Accept':'application/json'} });
                const json = await res.json();
                if(!json.ok) throw json;
                this.rows = json.items;
                this.order = this.rows.map(r => r.id);
            }catch(e){
                console.error(e);
                toast('Failed to load documents', 'error');
            }
        },
        async create(e){
            if(!this.newName){ toast('Enter a document name', 'error'); e.target.value=''; return; }
            const f = e.target.files[0];
            if(!f){ return; }
            const fd = new FormData();
            fd.append('name', this.newName);
            fd.append('file', f);
            try{
                const res = await fetch(cfg.uploadUrl, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': cfg.csrf, 'Accept':'application/json'},
                    body: fd
                });
                const json = await res.json();
                if(!json.ok) throw json;
                this.rows.push(json.item);
                this.order = this.rows.map(r => r.id);
                this.newName = '';
                toast('Document added');
            }catch(e){
                console.error(e);
                toast('Failed to add document', 'error');
            }finally{
                e.target.value = '';
            }
        },
        async createWithoutFile(){
            if(!cfg.fileOptional){ return; }
            if(!this.newName){ toast('Enter a document name', 'error'); return; }
            // send with empty file: use fetch with FormData but no file
            const fd = new FormData();
            fd.append('name', this.newName);
            try{
                const res = await fetch(cfg.uploadUrl, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': cfg.csrf, 'Accept':'application/json'},
                    body: fd
                });
                const json = await res.json();
                if(!json.ok) throw json;
                this.rows.push(json.item);
                this.order = this.rows.map(r => r.id);
                this.newName = '';
                toast('Requirement added');
            }catch(e){
                console.error(e);
                toast('Failed to add requirement', 'error');
            }
        },
        async remove(id){
            if(!confirm('Remove?')) return;
            try{
                const res = await fetch(`${cfg.deleteBaseUrl}/${id}`, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': cfg.csrf, 'Accept':'application/json'}
                });
                const json = await res.json();
                if(!json.ok) throw json;
                this.rows = this.rows.filter(r => r.id !== id);
                this.order = this.rows.map(r => r.id);
                toast('Removed');
            }catch(e){
                console.error(e);
                toast('Failed to remove', 'error');
            }
        },
        async reorder(){
            try{
                const res = await fetch(cfg.reorderUrl, {
                    method: 'PATCH',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': cfg.csrf,'Accept':'application/json'},
                    body: JSON.stringify({ ids: this.order })
                });
                const json = await res.json();
                if(!json.ok) throw json;
                toast('Order saved');
            }catch(e){
                console.error(e);
                toast('Failed to save order', 'error');
            }
        }
    }
}

function questionsBox(cfg){
    return {
        items: [],
        newQuestion: '',
        async init(){
            // seed defaults if empty
            await this.refresh(true);
        },
        async refresh(seedIfEmpty = false){
            try{
                const res = await fetch(cfg.listUrl, { headers: {'Accept':'application/json'} });
                const json = await res.json();
                if(!json.ok) throw json;
                this.items = json.items;

                if(seedIfEmpty && this.items.length === 0){
                    await fetch(cfg.seedUrl, {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': cfg.csrf, 'Accept':'application/json'}
                    });
                    await this.refresh(false);
                }
            }catch(e){
                console.error(e);
                toast('Failed to load questions', 'error');
            }
        },
        async create(){
            if(!this.newQuestion) return;
            try{
                const res = await fetch(cfg.createUrl, {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': cfg.csrf,'Accept':'application/json'},
                    body: JSON.stringify({ question: this.newQuestion })
                });
                const json = await res.json();
                if(!json.ok) throw json;
                this.items.push(json.item);
                this.newQuestion = '';
                toast('Question added');
            }catch(e){
                console.error(e);
                toast('Failed to add question', 'error');
            }
        },
        async toggle(id){
            try{
                const res = await fetch(`${cfg.toggleBaseUrl}/${id}/toggle`, {
                    method: 'PATCH',
                    headers: {'X-CSRF-TOKEN': cfg.csrf, 'Accept':'application/json'}
                });
                const json = await res.json();
                if(!json.ok) throw json;
                const idx = this.items.findIndex(i => i.id === id);
                if(idx !== -1) this.items[idx].is_enabled = json.is_enabled;
            }catch(e){
                console.error(e);
                toast('Failed to toggle', 'error');
            }
        },
        async remove(id){
            if(!confirm('Delete question?')) return;
            try{
                const res = await fetch(`${cfg.deleteBaseUrl}/${id}`, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': cfg.csrf, 'Accept':'application/json'}
                });
                const json = await res.json();
                if(!json.ok) throw json;
                this.items = this.items.filter(i => i.id !== id);
                toast('Deleted');
            }catch(e){
                console.error(e);
                toast('Failed to delete', 'error');
            }
        },
        async reorder(){
            const ids = this.items.map(i => i.id);
            try{
                const res = await fetch(cfg.reorderUrl, {
                    method: 'PATCH',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': cfg.csrf,'Accept':'application/json'},
                    body: JSON.stringify({ ids })
                });
                const json = await res.json();
                if(!json.ok) throw json;
                toast('Order saved');
            }catch(e){
                console.error(e);
                toast('Failed to save order', 'error');
            }
        }
    }
}

function termsBox(cfg){
    return {
        candidate: cfg.candidateInit || '',
        employer: cfg.employerInit || '',
        msg: '',
        ok: true,
        async save(){
            this.msg = 'Saving...';
            try{
                const res = await fetch(cfg.saveUrl, {
                    method: 'PATCH',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': cfg.csrf,'Accept':'application/json'},
                    body: JSON.stringify({
                        terms_candidate: this.candidate,
                        terms_employer: this.employer
                    })
                });
                const json = await res.json();
                if(!json.ok) throw json;
                this.msg = 'Saved';
                this.ok = true;
            }catch(e){
                console.error(e);
                this.msg = 'Failed';
                this.ok = false;
            }
        }
    }
}
</script>
