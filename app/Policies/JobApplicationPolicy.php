<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

class JobApplicationPolicy
{
    /**
     * Determine if user can view the application
     * - Admins: can view all
     * - Employers: can view applications for their jobs
     * - Candidates: can view their own applications
     */
    public function view(User $user, JobApplication $application): bool
    {
        // Admins can view any application
        if ($user->isAdmin()) {
            return true;
        }

        // Employers can view applications for their jobs
        if ($user->isEmployer() && $application->job->employer->user_id === $user->id) {
            return true;
        }

        // Candidates can view their own applications
        if ($user->isCandidate() && $application->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Only the applicant (candidate) can delete/withdraw their application
     */
    public function delete(User $user, JobApplication $application): bool
    {
        // Admins can delete any application
        if ($user->isAdmin()) {
            return true;
        }

        // Candidates can only delete their own applications
        return $user->isCandidate() && $application->user_id === $user->id;
    }

    /**
     * Only employers can update application status (accept/reject)
     */
    public function updateStatus(User $user, JobApplication $application): bool
    {
        // Admins can update any application
        if ($user->isAdmin()) {
            return true;
        }

        // Employers can update applications for their jobs
        return $user->isEmployer() && $application->job->employer->user_id === $user->id;
    }
}