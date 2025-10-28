<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobContactController extends Controller
{
    /**
     * Return currently attached employer contacts for this job (JSON).
     */
    public function index(Job $job): JsonResponse
    {
        $contacts = $job->contacts()
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.email')
            ->orderBy('users.first_name')
            ->get()
            ->map(fn ($u) => [
                'id'    => $u->id,
                'name'  => trim($u->first_name.' '.$u->last_name),
                'email' => $u->email,
            ]);

        return response()->json([
            'ok'       => true,
            'contacts' => $contacts,
            'primary'  => $job->primary_contact_id,
        ]);
    }

    /**
     * Attach one or many employer users to this job.
     */
    public function attach(Request $request, Job $job): JsonResponse
    {
        $data = $request->validate([
            'user_ids'   => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        // optional: restrict to employer users of the same employer account
        // $allowed = User::where('role', 'employer')
        //     ->whereHas('employers', fn($q) => $q->where('employers.id', $job->employer_id))
        //     ->whereIn('id', $data['user_ids'])
        //     ->pluck('id')
        //     ->all();

        $job->contacts()->syncWithoutDetaching($data['user_ids']);

        return response()->json(['ok' => true]);
    }

    /**
     * Detach an employer user from this job.
     */
    public function detach(Job $job, User $user): JsonResponse
    {
        $job->contacts()->detach($user->id);

        // If primary was removed, clear it
        if ($job->primary_contact_id === $user->id) {
            $job->primary_contact_id = null;
            $job->save();
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Set primary contact to one of the already attached contacts.
     */
    public function setPrimary(Request $request, Job $job): JsonResponse
    {
        $data = $request->validate([
            'primary_contact_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        // Ensure that chosen user is already attached as contact
        $isAttached = $job->contacts()->where('users.id', $data['primary_contact_id'])->exists();
        if (! $isAttached) {
            return response()->json([
                'ok' => false,
                'message' => 'Selected user is not attached as a contact for this job.',
            ], 422);
        }

        $job->primary_contact_id = $data['primary_contact_id'];
        $job->save();

        return response()->json(['ok' => true]);
    }
}
