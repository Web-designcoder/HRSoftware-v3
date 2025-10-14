<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Job $job): bool
    {
        return true;
    }

    // ✅ Only admin or consultant can create
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'consultant']);
    }

    // ✅ Only admin or assigned consultant can update
    public function update(User $user, Job $job): bool|Response
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'consultant' && $user->id === $job->consultant_id) {
            return true;
        }

        return Response::deny('You are not authorised to update this job.');
    }

    // ✅ Only admin or assigned consultant can delete
    public function delete(User $user, Job $job): bool
    {
        return $user->role === 'admin' || ($user->role === 'consultant' && $user->id === $job->consultant_id);
    }

    // Candidates can apply
    public function apply(User $user, Job $job): bool
    {
        return $user->role === 'candidate' && !$job->hasUserApplied($user);
    }

    public function restore(User $user, Job $job): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Job $job): bool
    {
        return $user->role === 'admin';
    }
}
