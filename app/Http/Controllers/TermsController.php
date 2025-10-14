<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TermsController extends Controller
{
    public function show()
    {
        return view('terms');
    }

    public function accept(Request $request)
    {
        $request->validate([
            'agree' => 'accepted'
        ]);

        $user = auth()->user();
        $user->terms_accepted_at = now();
        $user->save();

        return redirect()->route('dashboard')
            ->with('success', 'Thanks for accepting the Terms & Conditions!');
    }
}
