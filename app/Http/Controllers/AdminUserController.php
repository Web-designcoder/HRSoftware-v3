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
}
