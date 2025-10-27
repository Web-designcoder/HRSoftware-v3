<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class EmployerController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show the form for creating an employer company
     */
    public function create()
    {
        $user = auth()->user();

        // ✅ Updated relationship
        if ($user->employers()->exists()) {
            return redirect()->route('dashboard')
                ->with('info', 'You are already linked to an employer company.');
        }

        return view('employer.create');
    }

    /**
     * Store a new employer company and link the user to it.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:3|unique:employers,name',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'industry' => 'nullable|string|max:100',
        ]);

        // ✅ Create company (employer)
        $employer = Employer::create($validated);

        // ✅ Link current user as a contact
        auth()->user()->employers()->attach($employer->id, [
            'position' => 'Owner / Primary Contact',
            'permission_level' => 'level3',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Your employer company has been created and linked to your account!');
    }

    /**
     * Optional: show employer dashboard/profile page
     */
    public function show(Employer $employer)
    {
        $this->authorize('view', $employer);
        return view('employer.show', compact('employer'));
    }
}
