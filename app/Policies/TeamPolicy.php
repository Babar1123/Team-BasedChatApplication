<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function view(User $user, Team $team): bool
    {
        return $team->company->owner_id === $user->id
            || $team->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user, $companyId): bool
    {
        return $user->companies()->whereKey($companyId)->exists();
    }

    public function update(User $user, Team $team): bool
    {
        return $team->company->owner_id === $user->id
            || $team->members()->where('user_id', $user->id)->whereIn('role', ['owner', 'admin'])->exists();
    }

    public function delete(User $user, Team $team): bool
    {
        return $team->company->owner_id === $user->id;
    }

    public function manageMembers(User $user, Team $team): bool
    {
        return $team->company->owner_id === $user->id
            || $team->members()->where('user_id', $user->id)->whereIn('role', ['owner', 'admin'])->exists();
    }
}
