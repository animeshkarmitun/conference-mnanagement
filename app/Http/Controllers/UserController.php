<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Permission-gated access for user management
        $this->middleware(function ($request, $next) {
            $user = auth()->user();

            if (!$user) {
                abort(403, 'Unauthorized');
            }

            // Superadmin bypasses permission checks
            if ($user->hasRole('superadmin')) {
                return $next($request);
            }

            // Map controller methods to permissions
            $action = $request->route()->getActionMethod();
            $permissionMap = [
                'index' => 'users.view',
                'show' => 'users.view',
                'create' => 'users.create',
                'store' => 'users.create',
                'edit' => 'users.edit',
                'update' => 'users.edit',
                'destroy' => 'users.delete',
                'activate' => 'users.activate',
                'deactivate' => 'users.deactivate',
            ];

            $needed = $permissionMap[$action] ?? 'users.view';

            if (!$user->hasPermission($needed)) {
                abort(403, 'Access denied. Missing permission: ' . $needed);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'active'); // Default to active users
        $search = trim((string) $request->get('q', ''));
        $roleFilter = $request->get('role');

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
        
        // Apply role filter
        if (!empty($roleFilter) && $roleFilter !== 'all') {
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('roles.id', $roleFilter);
            });
        }

        // Apply search on name and email
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $query->paginate(10)->appends([
            'status' => $status,
            'q' => $search,
            'role' => $roleFilter,
        ]);
        
        // Get user counts for each category
        $userCounts = [
            'active' => User::where('email_verified_at', '!=', null)->count(),
            'inactive' => User::where('email_verified_at', null)->count(),
            'all' => User::count(),
        ];
        
        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'userCounts', 'status', 'roles', 'search', 'roleFilter'));
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'superadmin')->get();
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
        $roles = Role::where('name', '!=', 'superadmin')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array',
        ]);

        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Sync roles if provided
        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return redirect()->route('users.show', $user)->with('success', 'User updated successfully.');
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

    public function activate(User $user)
    {
        \Log::info('Activate method called for user: ' . $user->id);
        \Log::info('Current email_verified_at: ' . $user->email_verified_at);
        
        // Don't allow activating the current user
        if ($user->id === auth()->id()) {
            \Log::info('Attempted to activate own account');
            return redirect()->back()->with('error', 'You cannot activate your own account.');
        }

        \Log::info('Updating user email_verified_at to: ' . now());
        
        // Use direct assignment instead of mass assignment
        $user->email_verified_at = now();
        $user->save();
        
        \Log::info('User updated. New email_verified_at: ' . $user->fresh()->email_verified_at);
        \Log::info('User activated successfully');
        
        return redirect()->back()->with('success', 'User activated successfully.');
    }

    public function deactivate(User $user)
    {
        // Don't allow deactivating the current user
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        // Use direct assignment instead of mass assignment
        $user->email_verified_at = null;
        $user->save();

        return redirect()->back()->with('success', 'User deactivated successfully.');
    }
} 