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

        $attached = [];
        foreach ($request->candidate_ids as $candidateId) {
            $attached[] = $candidateId;
            $job->jobApplications()->firstOrCreate(['user_id' => $candidateId]);
        }

        return response()->json([
            'message' => 'Candidates added successfully.',
            'candidates' => $job->jobApplications()
                ->with(['user:id,first_name,last_name,profile_picture'])
                ->get()
                ->map(fn($a) => [
                    'id' => $a->user->id,
                    'name' => $a->user->first_name . ' ' . $a->user->last_name,
                    'profile_picture' => $a->user->profile_picture
                        ? asset('storage/' . $a->user->profile_picture)
                        : asset('images/default-avatar.png'),
                    'status' => $a->status ?? 'Shortlist',
                    'view_url' => route('admin.job.application.show', [$job, $a]),
                ])
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
