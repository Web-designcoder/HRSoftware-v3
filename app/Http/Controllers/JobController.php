<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
    use AuthorizesRequests;

    /* ───── INDEX ───── */
    public function index()
    {
        $user = auth()->user();
        $this->authorize('viewAny', Job::class);

        $filters = request()->only('search', 'min_salary', 'max_salary', 'experience', 'category');

        $jobs = Job::with(['employer', 'consultant'])
            ->visibleTo($user)
            ->filter($filters)
            ->latest()
            ->paginate(20);

        return view('job.index', compact('jobs'));
    }

    /* ───── SHOW ───── */
    public function show(Job $job)
    {
        $this->authorize('view', $job);

        $job->load(['employer', 'consultant', 'jobApplications.user', 'assignedCandidates']);

        return view('job.show', compact('job'));
    }

    /* ───── CREATE ───── */
    public function create()
    {
        $this->authorize('create', Job::class);

        $employers = collect();
        $consultants = collect();
        $candidates = collect();

        $user = auth()->user();

        if ($user->isAdmin()) {
            $employers = Employer::orderBy('name')->get();
            $consultants = User::where('role', 'consultant')->orderBy('first_name')->get();
            $candidates = User::where('role', 'candidate')->orderBy('first_name')->get();
        } elseif ($user->isConsultant()) {
            // Consultants can create jobs but must assign themselves as consultant
            $employers = Employer::orderBy('name')->get();
        }

        return view('job.create', compact('employers', 'consultants', 'candidates'));
    }

    /* ───── STORE ───── */
    public function store(Request $request)
    {
        $this->authorize('create', Job::class);

        $validated = $request->validate([
            'title'               => ['required', 'string', 'max:255'],
            'location'            => ['nullable', 'string', 'max:255'],
            'city'                => ['nullable', 'string', 'max:255'],
            'country'             => ['nullable', 'string', 'max:255'],
            'salary'              => ['nullable', 'numeric'],
            'description'         => ['nullable', 'string'],
            'experience'          => ['nullable', 'string', 'max:50'],
            'category'            => ['nullable', 'string', 'max:100'],
            'date_posted'         => ['nullable', 'date'],
            'managed_by'          => ['nullable', 'string', 'max:255'],
            'assignment_overview' => ['nullable', 'string'],
            'company_logo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'campaign_documents'  => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'employer_id'         => ['required', 'exists:employers,id'],
            'consultant_id'       => ['nullable', 'exists:users,id'],
            'candidate_ids'       => ['array'],
            'candidate_ids.*'     => ['exists:users,id'],
        ]);

        if ($request->hasFile('company_logo')) {
            $validated['company_logo'] = $request->file('company_logo')->store('logos', 'public');
        }

        if ($request->hasFile('campaign_documents')) {
            $validated['campaign_documents'] = $request->file('campaign_documents')->store('documents', 'public');
        }

        $validated['date_posted'] = $validated['date_posted'] ?? now();

        $user = auth()->user();
        if ($user->isConsultant()) {
            $validated['consultant_id'] = $user->id;
        }

        $job = Job::create($validated);

        if ($user->isAdmin() && $request->filled('candidate_ids')) {
            $job->assignedCandidates()->sync($request->candidate_ids);
        }

        return redirect()->route('jobs.show', $job)->with('success', 'Job created successfully.');
    }

    /* ───── EDIT ───── */
    public function edit(Job $job)
    {
        $this->authorize('update', $job);

        $employers = collect();
        $consultants = collect();
        $candidates = collect();

        $user = auth()->user();

        if ($user->isAdmin()) {
            $employers = Employer::orderBy('name')->get();
            $consultants = User::where('role', 'consultant')->orderBy('first_name')->get();
            $candidates = User::where('role', 'candidate')->orderBy('first_name')->get();
        } elseif ($user->isConsultant()) {
            $employers = Employer::orderBy('name')->get();
        }

        return view('job.edit', compact('job', 'employers', 'consultants', 'candidates'));
    }

    /* ───── UPDATE ───── */
    public function update(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        $validated = $request->validate([
            'title'               => ['required', 'string', 'max:255'],
            'location'            => ['nullable', 'string', 'max:255'],
            'city'                => ['nullable', 'string', 'max:255'],
            'country'             => ['nullable', 'string', 'max:255'],
            'salary'              => ['nullable', 'numeric'],
            'description'         => ['nullable', 'string'],
            'experience'          => ['nullable', 'string', 'max:50'],
            'category'            => ['nullable', 'string', 'max:100'],
            'date_posted'         => ['nullable', 'date'],
            'managed_by'          => ['nullable', 'string', 'max:255'],
            'assignment_overview' => ['nullable', 'string'],
            'company_logo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'campaign_documents'  => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'employer_id'         => ['required', 'exists:employers,id'],
            'consultant_id'       => ['nullable', 'exists:users,id'],
            'candidate_ids'       => ['array'],
            'candidate_ids.*'     => ['exists:users,id'],
        ]);

        if ($request->hasFile('company_logo')) {
            if ($job->company_logo && Storage::disk('public')->exists($job->company_logo)) {
                Storage::disk('public')->delete($job->company_logo);
            }
            $validated['company_logo'] = $request->file('company_logo')->store('logos', 'public');
        }

        if ($request->hasFile('campaign_documents')) {
            if ($job->campaign_documents && Storage::disk('public')->exists($job->campaign_documents)) {
                Storage::disk('public')->delete($job->campaign_documents);
            }
            $validated['campaign_documents'] = $request->file('campaign_documents')->store('documents', 'public');
        }

        $user = auth()->user();
        if ($user->isConsultant()) {
            $validated['consultant_id'] = $user->id;
        }

        $job->update($validated);

        if ($user->isAdmin()) {
            $job->assignedCandidates()->sync($request->input('candidate_ids', []));
        }

        return redirect()->route('jobs.show', $job)->with('success', 'Job updated successfully.');
    }

    /* ───── DESTROY ───── */
    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);

        if ($job->company_logo && Storage::disk('public')->exists($job->company_logo)) {
            Storage::disk('public')->delete($job->company_logo);
        }

        if ($job->campaign_documents && Storage::disk('public')->exists($job->campaign_documents)) {
            Storage::disk('public')->delete($job->campaign_documents);
        }

        $job->delete();

        return redirect()->route('jobs.index')->with('success', 'Job deleted successfully.');
    }
}
