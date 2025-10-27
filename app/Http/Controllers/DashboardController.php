<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Route to appropriate dashboard based on role
        return match ($user->role) {
            'admin' => $this->adminDashboard(),
            'consultant' => $this->adminDashboard(),
            'employer' => $this->employerDashboard(),
            'candidate' => $this->candidateDashboard(),
            default => abort(403, 'Invalid user role'),
        };
    }

    /**
     * Admin Dashboard
     */
    private function adminDashboard()
    {
        $user = auth()->user();

        // Superadmin sees everything
        if ($user->isAdmin()) {
            $campaigns = \App\Models\Job::with(['consultant','employer'])
                ->withCount('jobApplications')
                ->latest()
                ->take(4)
                ->get();

            $applications = \App\Models\JobApplication::with(['job.employer','user'])
                ->latest()
                ->take(10)
                ->get();
        }

        // Consultant sees only their assigned campaigns
        if ($user->isConsultant()) {
            $campaigns = \App\Models\Job::with(['consultant','employer'])
                ->withCount('jobApplications')
                ->where('consultant_id', $user->id)
                ->latest()
                ->take(4)
                ->get();

            $applications = \App\Models\JobApplication::with(['job.employer','user'])
                ->whereHas('job', fn($q) => $q->where('consultant_id', $user->id))
                ->latest()
                ->take(10)
                ->get();
        }

        return view('dashboard.admin', compact('campaigns', 'applications'));
    }

    /**
     * Employer Dashboard
     */
    private function employerDashboard()
    {
        $user = auth()->user();

        // ✅ Check new relationship correctly
        $employer = $user->employers()->first();

        if (!$employer) {
            return redirect()->route('employer.create')
                ->with('info', 'Please create your employer company first.');
        }

        $stats = [
            'total_jobs' => $employer->jobs()->count(),
            'active_jobs' => $employer->jobs()->whereNull('deleted_at')->count(),
            'total_applications' => \App\Models\JobApplication::whereHas('job', function ($q) use ($employer) {
                $q->where('employer_id', $employer->id);
            })->count(),
            'pending_applications' => \App\Models\JobApplication::whereHas('job', function ($q) use ($employer) {
                $q->where('employer_id', $employer->id);
            })->where('status', 'pending')->count(),
        ];

        $my_jobs = $employer->jobs()
            ->withCount('jobApplications')
            ->latest()
            ->take(5)
            ->get();

        $recent_applications = \App\Models\JobApplication::with(['job', 'user'])
            ->whereHas('job', function ($q) use ($employer) {
                $q->where('employer_id', $employer->id);
            })
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.employer', compact('stats', 'my_jobs', 'recent_applications', 'employer'));
    }


    /**
     * Candidate Dashboard
     */
    private function candidateDashboard()
    {
        $user = auth()->user();

        $stats = [
            'total_applications' => $user->jobApplications()->count(),
            'pending_applications' => $user->jobApplications()->where('status', 'pending')->count(),
            'accepted_applications' => $user->jobApplications()->where('status', 'accepted')->count(),
            'rejected_applications' => $user->jobApplications()->where('status', 'rejected')->count(),
        ];

        $my_applications = $user->jobApplications()
            ->with(['job', 'job.employer'])
            ->latest()
            ->take(5)
            ->get();

        $recommended_jobs = Job::with('employer')
            ->whereDoesntHave('jobApplications', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.candidate', compact('stats', 'my_applications', 'recommended_jobs'));
    }
}