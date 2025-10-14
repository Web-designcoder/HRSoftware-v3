<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    /**
     * Show the edit account page.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('account.edit', compact('user'));
    }

    /**
     * Update user profile details and attachments.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate all form fields including attachments
        $validated = $request->validate([
            'salutation' => 'nullable|string|max:10',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'profile_picture' => 'nullable|image|max:2048',
            'cv' => 'nullable|file|max:5120',
            'medical_check' => 'nullable|file|max:5120',
            'police_clearance' => 'nullable|file|max:5120',
            'qualifications.*' => 'nullable|file|max:5120',
            'other_files.*' => 'nullable|file|max:5120',
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $validated['profile_picture'] = $path;
        }

        // Handle single file uploads (CV, Medical Check, Police Clearance)
        foreach (['cv', 'medical_check', 'police_clearance'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $validated[$fileField] = $request->file($fileField)->store('attachments', 'public');
            }
        }

        // Handle multiple file uploads (Qualifications, Other Documents)
        foreach (['qualifications', 'other_files'] as $multiField) {
            if ($request->hasFile($multiField)) {
                $paths = [];
                foreach ($request->file($multiField) as $file) {
                    $paths[] = $file->store('attachments', 'public');
                }
                $validated[$multiField] = $paths;
            }
        }

        // Hash and update password if provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        // Update user record
        $user->update($validated);

        // Return with success message
        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Handle live attachment uploads from the account page.
     */
    public function uploadAttachment(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'field' => 'required|string|in:cv,medical_check,police_clearance,qualifications,other_files',
            'file' => 'required|file|max:5120',
        ]);

        $path = $request->file('file')->store('attachments', 'public');

        // Save or append path to the user model
        if (in_array($validated['field'], ['qualifications', 'other_files'])) {
            $existing = $user->{$validated['field']} ?? [];
            $existing[] = $path;
            $user->{$validated['field']} = $existing;
        } else {
            $user->{$validated['field']} = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path),
        ]);
    }

    /**
     * Delete an attachment and update the user record.
     */
    public function deleteAttachment(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'field' => 'required|string|in:cv,medical_check,police_clearance,qualifications,other_files',
            'path' => 'required|string',
        ]);

        $field = $validated['field'];
        $path = $validated['path'];

        // Delete file from storage if it exists
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        // Update user record
        if (in_array($field, ['qualifications', 'other_files'])) {
            $existing = $user->{$field} ?? [];
            $user->{$field} = array_values(array_filter($existing, fn($f) => $f !== $path));
        } else {
            // For single files (cv, medical, police)
            if ($user->{$field} === $path) {
                $user->{$field} = null;
            }
        }

        $user->save();

        return response()->json(['success' => true]);
    }

}
