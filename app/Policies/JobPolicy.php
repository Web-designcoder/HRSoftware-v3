<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobPolicy
{
    /**
     * Anyone can view job listings (even guests)
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Anyone can view a specific job
     */
    public function view(?User $user, Job $job): bool
    {
        return true;
    }

    /**
     * Only employers and admins can create jobs
     */
    public function create(User $user): bool
    {
        return $user->isEmployer() || $user->isAdmin();
    }

    /**
     * Only the job owner (employer) or admin can update
     */
    public function update(User $user, Job $job): bool|Response
    {
        // Admins can update any job
        if ($user->isAdmin()) {
            return true;
        }

        // Employers can only update their own jobs
        if ($user->isEmployer() && $job->employer->user_id === $user->id) {
            // Prevent updates if job has applications
            if ($job->jobApplications()->count() > 0) {
                return Response::deny('Cannot modify a job that has applications');
            }
            return true;
        }

        return false;
    }

    /**
     * Only the job owner or admin can delete
     */
    public function delete(User $user, Job $job): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isEmployer() && $job->employer->user_id === $user->id;
    }

    /**
     * Only employers can view their own jobs list
     */
    public function viewAnyEmployer(User $user): bool
    {
        return $user->isEmployer() || $user->isAdmin();
    }

    /**
     * Only candidates can apply to jobs
     */
    public function apply(User $user, Job $job): bool
    {
        // Must be a candidate
        if (!$user->isCandidate()) {
            return false;
        }

        // Can't apply if already applied
        return !$job->hasUserApplied($user);
    }

    /**
     * Restore deleted jobs
     */
    public function restore(User $user, Job $job): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isEmployer() && $job->employer->user_id === $user->id;
    }

    /**
     * Permanently delete
     */
    public function forceDelete(User $user, Job $job): bool
    {
        return $user->isAdmin();
    }
}