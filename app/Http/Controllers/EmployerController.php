<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class EmployerController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show the form for creating employer profile
     */
    public function create()
    {
        // Redirect if already has employer profile
        if (auth()->user()->employer) {
            return redirect()->route('dashboard')
                ->with('info', 'You already have an employer profile.');
        }

        return view('employer.create');
    }

    /**
     * Store employer profile
     */
    public function store(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'company_name' => 'required|min:3|unique:employers,company_name',
            'company_description' => 'nullable|string',
            'website' => 'nullable|url',
            'industry' => 'nullable|string',
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            $validated['company_logo'] = $request->file('company_logo')->store('logos', 'public');
        }

        // Create employer profile
        auth()->user()->employer()->create($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Your employer profile has been created!');
    }
}