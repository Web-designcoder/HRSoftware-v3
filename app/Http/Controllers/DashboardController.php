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
        $stats = [
            'total_users' => User::count(),
            'total_employers' => User::where('role', 'employer')->count(),
            'total_candidates' => User::where('role', 'candidate')->count(),
            'total_jobs' => Job::count(),
            'active_jobs' => Job::whereNull('deleted_at')->count(),
            'total_applications' => JobApplication::count(),
            'pending_applications' => JobApplication::where('status', 'pending')->count(),
        ];

        $recent_jobs = Job::with('employer')
            ->latest()
            ->take(10)
            ->get();

        $recent_applications = JobApplication::with(['job', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.admin', compact('stats', 'recent_jobs', 'recent_applications'));
    }

    /**
     * Employer Dashboard
     */
    private function employerDashboard()
    {
        $user = auth()->user();
        
        // Check if employer profile exists
        if (!$user->employer) {
            return redirect()->route('employer.create')
                ->with('info', 'Please create your employer profile first.');
        }

        $employer = $user->employer;

        $stats = [
            'total_jobs' => $employer->jobs()->count(),
            'active_jobs' => $employer->jobs()->whereNull('deleted_at')->count(),
            'total_applications' => JobApplication::whereHas('job', function ($q) use ($employer) {
                $q->where('employer_id', $employer->id);
            })->count(),
            'pending_applications' => JobApplication::whereHas('job', function ($q) use ($employer) {
                $q->where('employer_id', $employer->id);
            })->where('status', 'pending')->count(),
        ];

        $my_jobs = $employer->jobs()
            ->withCount('jobApplications')
            ->latest()
            ->take(5)
            ->get();

        $recent_applications = JobApplication::with(['job', 'user'])
            ->whereHas('job', function ($q) use ($employer) {
                $q->where('employer_id', $employer->id);
            })
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.employer', compact('stats', 'my_jobs', 'recent_applications'));
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