<div x-data="campaignCandidates()" x-init="init()" class="mx-auto grid grid-cols-1 lg:grid-cols-4 gap-6">

    {{-- LEFT COLUMN --}}
    <div class="space-y-6 lg:col-span-1">
        <x-card title="Candidate Map" class="p-0 overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <p class="text-xs text-gray-500">Map of candidates in {{ $job->city ?? 'this area' }}</p>
            </div>
            <div class="h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                <span>🗺️ Map Placeholder</span>
            </div>
        </x-card>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="lg:col-span-3">
        <x-card>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-[#04215c]">Campaign Candidates</h2>
                <button @click="openModal = true"
                        class="px-4 py-2 bg-[#04215c] text-white rounded-md hover:bg-[#06318a] transition">
                    + Add Candidates
                </button>
            </div>

            <template x-if="candidates.length === 0">
                <p class="text-gray-500 text-center py-8">No candidates have applied yet.</p>
            </template>

            <div class="space-y-4" x-show="candidates.length > 0">
                <template x-for="candidate in candidates" :key="candidate.id">
                    <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg shadow-sm hover:bg-gray-100 transition">
                        <div class="flex items-center space-x-4">
                            <img :src="candidate.profile_picture"
                                 alt="Candidate"
                                 class="w-16 h-16 rounded-full object-cover border border-gray-200">
                            <div>
                                <h3 class="font-semibold text-gray-800" x-text="candidate.name"></h3>
                                <select @change="updateStatus(candidate.id, $event.target.value)"
                                        class="text-sm border border-gray-300 rounded-md px-2 py-1 mt-1 focus:ring-[#04215c]">
                                    <template x-for="status in ['Shortlist','Interview','Offer','Hired','Rejected']">
                                        <option :value="status"
                                            x-text="status"
                                            :selected="candidate.status === status"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a :href="candidate.view_url"
                               class="inline-block px-3 py-2 bg-[#04215c] text-white text-xs rounded-md hover:bg-[#06318a]">
                                View
                            </a>
                            <button @click="removeCandidate(candidate.id)"
                                    class="px-2 py-2 bg-red-500 text-white text-xs rounded-md hover:bg-red-600">
                                🗑️
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-card>

        {{-- Modal --}}
        <div x-show="openModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div @click.away="openModal=false"
                 class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
                <h3 class="text-lg font-semibold text-[#04215c] mb-3">Add Candidates</h3>
                <select multiple x-ref="candidateSelect"
                        class="w-full border border-gray-300 rounded-md p-2 mb-4"
                        size="8">
                    @foreach(\App\Models\User::where('role','candidate')->orderBy('first_name')->get() as $candidate)
                        <option value="{{ $candidate->id }}">{{ $candidate->name }}</option>
                    @endforeach
                </select>

                <div class="flex justify-end space-x-2">
                    <button @click="openModal=false"
                            class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button @click="attachCandidates()"
                            class="px-4 py-2 bg-[#04215c] text-white rounded-md hover:bg-[#06318a]">
                        Add Selected
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $candidateData = [];
    foreach ($job->jobApplications()->with(['user:id,first_name,last_name,profile_picture'])->get() as $a) {
        $candidateData[] = [
            'id' => $a->user->id,
            'name' => $a->user->first_name . ' ' . $a->user->last_name,
            'profile_picture' => $a->user->profile_picture
                ? asset('storage/' . $a->user->profile_picture)
                : asset('images/default-avatar.png'),
            'status' => $a->status ? $a->status : 'Shortlist',
            'view_url' => route('admin.job.application.show', [$job, $a]),
        ];
    }
@endphp

<script>
function campaignCandidates() {
    return {
        candidates: @json($candidateData),
        openModal: false,
        isLoading: false,
        init() {},

        async attachCandidates() {
            const ids = Array.from(this.$refs.candidateSelect.selectedOptions).map(o => o.value);
            if (!ids.length) return alert('Please select at least one candidate.');

            this.isLoading = true;

            try {
                const response = await fetch('{{ route('admin.jobs.candidates.attach', $job) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ candidate_ids: ids })
                });

                const data = await response.json();

                if (response.ok) {
                    // Append new candidates to list dynamically
                    data.candidates.forEach(c => {
                        if (!this.candidates.find(x => x.id === c.id)) {
                            this.candidates.push(c);
                        }
                    });

                    this.openModal = false;
                    this.toast('✅ Candidates added successfully!');
                } else {
                    this.toast('⚠️ ' + (data.message || 'Failed to add candidates.'));
                }

            } catch (error) {
                console.error(error);
                this.toast('❌ Error adding candidates.');
            } finally {
                this.isLoading = false;
            }
        },

        async removeCandidate(id) {
            if (!confirm('Remove this candidate from campaign?')) return;
            try {
                const res = await fetch(`{{ url('admin/jobs/'.$job->id.'/candidates') }}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                });
                if (res.ok) {
                    this.candidates = this.candidates.filter(c => c.id !== id);
                    this.toast('🗑️ Candidate removed.');
                }
            } catch (err) {
                console.error(err);
                this.toast('Error removing candidate.');
            }
        },

        async updateStatus(id, status) {
            try {
                await fetch(`{{ url('admin/jobs/'.$job->id.'/candidates') }}/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ status })
                });
                this.toast('Status updated.');
            } catch (err) {
                console.error(err);
                this.toast('Error updating status.');
            }
        },

        toast(message) {
            const t = document.createElement('div');
            t.textContent = message;
            t.className = 'fixed bottom-5 right-5 bg-[#04215c] text-white px-4 py-2 rounded-md shadow-lg z-50 animate-fade-in';
            document.body.appendChild(t);
            setTimeout(() => t.remove(), 2500);
        }
    }
}
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fade-in 0.3s ease-out; }
</style>

