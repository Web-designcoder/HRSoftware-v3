<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'consultant', 'employer', 'candidate']);
    }

    public function view(User $user, Job $job): bool
    {
        if ($user->role === 'admin') return true;

        if ($user->role === 'consultant' && $user->id === $job->consultant_id) return true;

        if ($user->role === 'employer') {
            $employerIds = $user->employers()->pluck('employers.id')->toArray();
            return in_array($job->employer_id, $employerIds, true);
        }

        if ($user->role === 'candidate' && $job->assignedCandidates()->where('user_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool|Response
    {
        return in_array($user->role, ['admin', 'consultant'])
            ? Response::allow()
            : Response::deny('You are not authorised to create jobs.');
    }

    public function update(User $user, Job $job): bool|Response
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'consultant' && $user->id === $job->consultant_id) return true;

        return Response::deny('You are not authorised to update this job.');
    }

    public function delete(User $user, Job $job): bool|Response
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'consultant' && $user->id === $job->consultant_id) return true;

        return Response::deny('You are not authorised to delete this job.');
    }

    public function apply(User $user, Job $job): bool|Response
    {
        if ($user->role !== 'candidate') {
            return Response::deny('Only candidates can apply to jobs.');
        }

        if ($job->hasUserApplied($user)) {
            return Response::deny('You have already applied for this job.');
        }

        // Candidate must have visibility to the job to apply
        if (!$job->assignedCandidates()->where('user_id', $user->id)->exists()) {
            return Response::deny('You are not authorised to apply to this job.');
        }

        return Response::allow();
    }

    public function restore(User $user, Job $job): bool { return $user->role === 'admin'; }
    public function forceDelete(User $user, Job $job): bool { return $user->role === 'admin'; }
}
