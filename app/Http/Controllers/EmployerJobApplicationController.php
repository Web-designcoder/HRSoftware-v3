<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EmployerJobApplicationController extends Controller
{
    use AuthorizesRequests;

    public function show(Job $job, JobApplication $jobApplication)
    {
        // Ensure the application belongs to this job
        if ($jobApplication->job_id !== $job->id) {
            abort(404);
        }

        // Eager-load relations for the view
        $jobApplication->load(['user', 'job.employer', 'job.consultant']);

        // Authorize viewing from employer/consultant/admin perspectives
        $this->authorize('view', $jobApplication);

        return view('employer.applications.show', [
            'application' => $jobApplication,
            'job' => $jobApplication->job,
        ]);
    }
}
