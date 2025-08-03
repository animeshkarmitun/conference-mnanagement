<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'active'); // Default to active users
        
        $query = User::with(['roles']);
        
        // Filter users based on status
        switch ($status) {
            case 'active':
                $query->where('email_verified_at', '!=', null)
                      ->orderBy('created_at', 'desc'); // Most recent first
                break;
                
            case 'inactive':
                $query->where('email_verified_at', null)
                      ->orderBy('created_at', 'desc'); // Most recent first
                break;
                
            case 'all':
            default:
                $query->orderBy('created_at', 'desc'); // Most recent first
                break;
        }
        
        $users = $query->paginate(10);
        
        // Get user counts for each category
        $userCounts = [
            'active' => User::where('email_verified_at', '!=', null)->count(),
            'inactive' => User::where('email_verified_at', null)->count(),
            'all' => User::count(),
        ];
        
        return view('users.index', compact('users', 'userCounts', 'status'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(), // Auto-verify for admin-created users
        ]);

        // Assign roles if provided
        if (isset($validated['roles'])) {
            $user->roles()->attach($validated['roles']);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['roles']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'array',
        ]);

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
        ]);

        // Sync roles if provided
        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Don't allow deletion of the current user
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
} 