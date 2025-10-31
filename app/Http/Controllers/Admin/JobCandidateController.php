<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;

class JobCandidateController extends Controller
{
    // Attach multiple candidates
    public function attach(Request $request, Job $job)
    {
        $request->validate([
            'candidate_ids' => 'required|array',
            'candidate_ids.*' => 'exists:users,id',
        ]);

        // Attach to pivot table only
        $job->assignedCandidates()->syncWithoutDetaching($request->candidate_ids);

        // Build candidate response for UI
        $candidates = User::whereIn('id', $request->candidate_ids)
            ->get(['id', 'first_name', 'last_name', 'profile_picture'])
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->first_name . ' ' . $u->last_name,
                'profile_picture' => $u->profile_picture
                    ? asset('storage/' . $u->profile_picture)
                    : asset('images/default-avatar.png'),
                'status' => 'Shortlist',
                'view_url' => '#', // no application yet
            ]);

        return response()->json([
            'message' => 'Candidates added successfully.',
            'candidates' => $candidates,
        ]);
    }

    // Detach candidate
    public function detach(Job $job, User $candidate)
    {
        $job->jobApplications()->where('user_id', $candidate->id)->delete();

        return response()->json(['message' => 'Candidate removed.']);
    }

    // Update candidate status
    public function updateStatus(Request $request, Job $job, User $candidate)
    {
        $request->validate(['status' => 'required|string']);
        $app = $job->jobApplications()->where('user_id', $candidate->id)->first();
        if ($app) {
            $app->status = $request->status;
            $app->save();
        }

        return response()->json(['message' => 'Status updated.']);
    }
}
