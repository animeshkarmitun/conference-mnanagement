<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PasswordlessLogin;
use Illuminate\Auth\Access\HandlesAuthorization;

class PasswordlessLoginPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any passwordless logins.
     */
    public function viewAny(User $user)
    {
        return $user->roles()->whereIn('name', ['admin', 'super_admin'])->exists();
    }

    /**
     * Determine whether the user can view the passwordless login.
     */
    public function view(User $user, PasswordlessLogin $passwordlessLogin)
    {
        return $user->roles()->whereIn('name', ['admin', 'super_admin'])->exists();
    }

    /**
     * Determine whether the user can create passwordless logins.
     */
    public function create(User $user)
    {
        return $user->roles()->whereIn('name', ['admin', 'super_admin'])->exists();
    }

    /**
     * Determine whether the user can update the passwordless login.
     */
    public function update(User $user, PasswordlessLogin $passwordlessLogin)
    {
        return $user->roles()->whereIn('name', ['admin', 'super_admin'])->exists();
    }

    /**
     * Determine whether the user can delete the passwordless login.
     */
    public function delete(User $user, PasswordlessLogin $passwordlessLogin)
    {
        return $user->roles()->whereIn('name', ['admin', 'super_admin'])->exists();
    }

    /**
     * Determine whether the user can manage passwordless logins.
     */
    public function manage(User $user)
    {
        return $user->roles()->whereIn('name', ['admin', 'super_admin'])->exists();
    }
}
