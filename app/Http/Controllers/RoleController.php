<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'system'); // Default to system roles
        
        $query = Role::with(['users']);
        
        // Filter roles based on status
        switch ($status) {
            case 'system':
                $query->whereIn('name', ['superadmin', 'admin'])
                      ->orderBy('name', 'asc');
                break;
                
            case 'user':
                $query->whereNotIn('name', ['superadmin', 'admin'])
                      ->orderBy('name', 'asc');
                break;
                
            case 'all':
            default:
                $query->orderBy('name', 'asc');
                break;
        }
        
        $roles = $query->paginate(10);
        
        // Get role counts for each category
        $roleCounts = [
            'system' => Role::whereIn('name', ['superadmin', 'admin'])->count(),
            'user' => Role::whereNotIn('name', ['superadmin', 'admin'])->count(),
            'all' => Role::count(),
        ];
        
        return view('roles.index', compact('roles', 'roleCounts', 'status'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'permissions' => $validated['permissions'] ?? [],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $role->load(['users']);
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
        ]);

        // Update permissions if provided
        if (isset($validated['permissions'])) {
            $role->permissions = $validated['permissions'];
            $role->save();
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Don't allow deletion of system roles
        if (in_array($role->name, ['superadmin', 'admin'])) {
            return redirect()->route('roles.index')->with('error', 'System roles cannot be deleted.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'Cannot delete role that has assigned users.');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    public function assignUsers(Role $role)
    {
        $users = User::with(['roles'])->get();
        return view('roles.assign-users', compact('role', 'users'));
    }

    public function updateUserAssignments(Request $request, Role $role)
    {
        $validated = $request->validate([
            'users' => 'array',
            'users.*' => 'exists:users,id',
        ]);

        $userIds = $validated['users'] ?? [];
        $role->users()->sync($userIds);

        return redirect()->route('roles.index')->with('success', 'User assignments updated successfully.');
    }
} 