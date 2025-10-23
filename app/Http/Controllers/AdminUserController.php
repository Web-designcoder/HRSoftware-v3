<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    // Middleware handled via routes/web.php

    public function index(Request $request)
    {
        $allowedRoles = ['admin', 'consultant', 'employer', 'candidate'];

        $role = $request->query('role');
        $q    = $request->query('q');

        $users = User::query()
            ->when($role && in_array($role, $allowedRoles), fn ($query) => $query->where('role', $role))
            ->when($q, function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->appends($request->only('role', 'q'));

        // simple counts for tabs
        $counts = [
            'all'        => User::count(),
            'admin'      => User::where('role', 'admin')->count(),
            'consultant' => User::where('role', 'consultant')->count(),
            'employer'   => User::where('role', 'employer')->count(),
            'candidate'  => User::where('role', 'candidate')->count(),
        ];

        return view('admin.users.index', compact('users', 'role', 'q', 'counts'));
    }

    public function create()
    {
        $user  = new User();
        $roles = ['admin', 'consultant', 'employer', 'candidate'];

        return view('admin.users.create', compact('user', 'roles'));
    }

    public function store(Request $request)
    {
        $roles = ['admin', 'consultant', 'employer', 'candidate'];

        $data = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name'  => ['nullable', 'string', 'max:255'],
            'name'       => ['nullable', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'       => ['required', Rule::in($roles)],
            'password'   => ['required', 'string', 'min:8'],
        ]);

        // Prefer full name from first/last if provided
        if (!empty($data['first_name']) || !empty($data['last_name'])) {
            $composed = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            if ($composed !== '') {
                $data['name'] = $composed;
            }
        }

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = ['admin', 'consultant', 'employer', 'candidate'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $roles = ['admin', 'consultant', 'employer', 'candidate'];

        $data = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name'  => ['nullable', 'string', 'max:255'],
            'name'       => ['nullable', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'       => ['required', Rule::in($roles)],
            'password'   => ['nullable', 'string', 'min:8'],
        ]);

        // Prefer full name from first/last if provided
        if (!empty($data['first_name']) || !empty($data['last_name'])) {
            $composed = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            if ($composed !== '') {
                $data['name'] = $composed;
            } elseif (!empty($data['name'])) {
                // keep provided "name"
            } else {
                $data['name'] = $user->name;
            }
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function clients(Request $request)
    {
        $query = \App\Models\User::where('role', 'employer');

        // Apply filters
        if ($request->filled('job_title')) {
            $query->where('job_title', $request->job_title);
        }
        if ($request->filled('industry')) {
            $query->where('industry', $request->industry);
        }
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        if ($request->filled('office_number')) {
            $query->where('office_number', 'like', "%{$request->office_number}%");
        }
        if ($request->filled('office_email')) {
            $query->where('email', 'like', "%{$request->office_email}%");
        }
        if ($request->filled('has_attachment') && $request->has_attachment === '1') {
            $query->whereNotNull('attachment');
        }

        // Keyword (broad)
        if ($request->filled('keyword')) {
            $q = $request->keyword;
            $query->where(function ($sub) use ($q) {
                $sub->where('company_name', 'like', "%$q%")
                    ->orWhere('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('office_number', 'like', "%$q%");
            });
        }

        $clients = $query->latest()->paginate(20)->withQueryString();

        // Distinct dropdown values
        $jobTitles = collect();

        $industries = collect();

        $countries = \App\Models\User::where('role', 'employer')
            ->whereNotNull('country')->distinct()->orderBy('country')->pluck('country');

        $cities = \App\Models\User::where('role', 'employer')
            ->whereNotNull('city')->distinct()->orderBy('city')->pluck('city');

        return view('admin.users.clients', compact('clients', 'jobTitles', 'industries', 'countries', 'cities'));
    }



    public function candidates(Request $request)
    {
        $query = \App\Models\User::where('role', 'candidate');

        // Filters
        if ($request->filled('industry')) {
            $query->where('industry', $request->industry);
        }
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }
        if ($request->filled('job_title')) {
            $query->where('desired_job_title', $request->job_title);
        }
        if ($request->filled('keyword')) {
            $q = $request->keyword;
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('desired_job_title', 'like', "%$q%");
            });
        }

        $candidates = $query->latest()->paginate(20)->withQueryString();

        // Dropdown options (distinct values)
        $industries = collect();

        $cities = \App\Models\User::where('role', 'candidate')
                        ->whereNotNull('city')
                        ->distinct()
                        ->orderBy('city')
                        ->pluck('city');

        $countries = \App\Models\User::where('role', 'candidate')
                        ->whereNotNull('country')
                        ->distinct()
                        ->orderBy('country')
                        ->pluck('country');

        $jobTitles = collect();

        return view('admin.users.candidates', compact('candidates', 'industries', 'cities', 'countries', 'jobTitles'));
    }

}
