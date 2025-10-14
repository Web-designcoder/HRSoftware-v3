<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    use AuthorizesRequests;

    public function create(Job $job)
    {
        $this->authorize('apply', $job);
        return view('job_application.create', [
            'job' => $job,
            'user' => auth()->user()
        ]);
    }

    public function store(Job $job, Request $request)
    {
        $this->authorize('apply', $job);

        $validated = $request->validate([
            'cv' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:2048',
            'video_intro' => 'nullable|file|mimes:mp4,mov,avi,webm,mkv,flv,wmv,mpeg,mpg,3gp,ogg|max:51200',
            'attention_to_detail' => 'nullable|string',
            'customer_management' => 'nullable|string',
            'market_understanding' => 'nullable|string',
            'sales_and_business_development' => 'nullable|string',
            'ambition' => 'nullable|string',
            'leadership_skills' => 'nullable|string',
            'risk_assessment' => 'nullable|string',
        ]);

        $cvPath = $request->hasFile('cv') ? $request->file('cv')->store('cvs', 'private') : null;
        $videoPath = $request->hasFile('video_intro') ? $request->file('video_intro')->store('videos', 'private') : null;

        $user = $request->user();

        $application = $job->jobApplications()->create([
            'user_id' => $user->id,
            'first_name' => $user->first_name ?? '',
            'last_name' => $user->last_name ?? '',
            'city' => $user->city ?? '',
            'postcode' => $user->postcode ?? '',
            'cv_path' => $cvPath,
            'video_intro' => $videoPath,
            'attention_to_detail' => $validated['attention_to_detail'] ?? null,
            'customer_management' => $validated['customer_management'] ?? null,
            'market_understanding' => $validated['market_understanding'] ?? null,
            'sales_and_business_development' => $validated['sales_and_business_development'] ?? null,
            'ambition' => $validated['ambition'] ?? null,
            'leadership_skills' => $validated['leadership_skills'] ?? null,
            'risk_assessment' => $validated['risk_assessment'] ?? null,
        ]);

        return redirect()
            ->route('job.application.show', [$job, $application])
            ->with('success', 'Application submitted successfully.');
    }

    public function show(Job $job, JobApplication $jobApplication)
    {
        // Make sure this application belongs to the correct job
        if ($jobApplication->job_id !== $job->id) {
            abort(404, 'This application does not belong to this job.');
        }
        
        $this->authorize('view', $jobApplication);
        return view('job_application.show', [
            'job' => $job,
            'application' => $jobApplication
        ]);
    }

    public function destroy(Job $job, JobApplication $jobApplication)
    {
        $this->authorize('delete', $jobApplication);

        // Delete the record instead of updating status
        $jobApplication->delete();

        // Clear cached relationship from the Job model
        $job->unsetRelation('jobApplications');

        return redirect()
            ->route('jobs.show', $job->id)
            ->with('success', 'Application withdrawn successfully.');
    }
}
