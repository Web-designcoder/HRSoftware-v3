<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

class JobApplicationPolicy
{
    /**
     * Determine if the user can view the application.
     * - Admins: can view all
     * - Consultants: can view if assigned to the job
     * - Employers: can view applications for their own jobs (via pivot)
     * - Candidates: can view their own applications
     */
    public function view(User $user, JobApplication $application): bool
    {
        // Admins can view any application
        if ($user->isAdmin()) {
            return true;
        }

        // Consultants can view if assigned to job
        if ($user->isConsultant() && $application->job->consultant_id === $user->id) {
            return true;
        }

        // ✅ Employers can view if the job belongs to any of their linked companies
        if ($user->isEmployer()) {
            $employerIds = $user->employers()->pluck('employers.id')->toArray();
            return in_array($application->job->employer_id, $employerIds);
        }

        // Candidates can view their own applications
        if ($user->isCandidate() && (int) $application->user_id === (int) $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Only the applicant (candidate) or admin can delete/withdraw an application.
     */
    public function delete(User $user, JobApplication $application): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isCandidate()
            && (int) $application->user_id === (int) $user->id;
    }

    /**
     * Determine if the user can update the application's status (accept/reject/shortlist).
     * - Admins: can update all
     * - Consultants: can update if assigned to the job
     * - Employers: can update for their own jobs
     */
    public function updateStatus(User $user, JobApplication $application): bool
    {
        // Admins can update any application
        if ($user->isAdmin()) {
            return true;
        }

        // Consultants can update applications for jobs they manage
        if ($user->isConsultant() && $application->job->consultant_id === $user->id) {
            return true;
        }

        // ✅ Employers can update if they are linked to the employer company
        if ($user->isEmployer()) {
            $employerIds = $user->employers()->pluck('employers.id')->toArray();
            return in_array($application->job->employer_id, $employerIds);
        }

        return false;
    }
}
