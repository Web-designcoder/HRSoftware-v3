<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
    public function __construct()
    {
    }

    /* ───── INDEX ───── */
    public function index()
    {
        $jobs = Job::with(['employer', 'consultant'])
            ->latest()
            ->paginate(20);

        return view('admin.jobs.index', compact('jobs'));
    }

    /* ───── CREATE ───── */
    public function create()
    {
        $employers = Employer::orderBy('name')->get();
        $consultants = User::where('role', 'consultant')->orderBy('first_name')->get();
        $candidates = User::where('role', 'candidate')->orderBy('first_name')->get();

        return view('admin.jobs.create', compact('employers', 'consultants', 'candidates'));
    }

    /* ───── STORE ───── */
    public function store(Request $request)
    {
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

        if ($request->filled('candidate_ids')) {
            $job->assignedCandidates()->sync($request->candidate_ids);
        }

        return redirect()->route('admin.jobs.index')->with('success', 'Job created successfully.');
    }

    /* ───── EDIT ───── */
    public function edit(Job $job)
    {
        $employers = Employer::orderBy('name')->get();
        $consultants = User::where('role', 'consultant')->orderBy('first_name')->get();
        $candidates = User::where('role', 'candidate')->orderBy('first_name')->get();

        return view('admin.jobs.edit', compact('job', 'employers', 'consultants', 'candidates'));
    }

    /* ───── UPDATE ───── */
    public function update(Request $request, Job $job)
    {
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

        $job->update($validated);

        if ($request->filled('candidate_ids')) {
            $job->assignedCandidates()->sync($request->candidate_ids);
        }

        return redirect()->route('admin.jobs.index')->with('success', 'Job updated successfully.');
    }

    /* ───── DESTROY ───── */
    public function destroy(Job $job)
    {
        if ($job->company_logo && Storage::disk('public')->exists($job->company_logo)) {
            Storage::disk('public')->delete($job->company_logo);
        }

        if ($job->campaign_documents && Storage::disk('public')->exists($job->campaign_documents)) {
            Storage::disk('public')->delete($job->campaign_documents);
        }

        $job->delete();

        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted successfully.');
    }
}
