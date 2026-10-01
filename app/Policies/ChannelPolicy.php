<?php

namespace App\Policies;

use App\Models\Channel;
use App\Models\User;

class ChannelPolicy
{
    public function view(User $user, Channel $channel): bool
    {
        if ($channel->team->company->owner_id === $user->id) {
            return true;
        }

        $isTeamMember = $channel->team->members()->where('user_id', $user->id)->exists();

        if (! $channel->is_private) {
            return $isTeamMember;
        }

        return $isTeamMember || $channel->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user, $teamId): bool
    {
        return $user->companies()->whereHas('teams', function ($q) use ($teamId) {
            $q->where('id', $teamId);
        })->exists();
    }

    public function update(User $user, Channel $channel): bool
    {
        return $channel->team->company->owner_id === $user->id
            || $channel->team->members()->where('user_id', $user->id)->whereIn('role', ['owner', 'admin'])->exists();
    }

    public function delete(User $user, Channel $channel): bool
    {
        return $channel->team->company->owner_id === $user->id
            || $channel->team->members()->where('user_id', $user->id)->whereIn('role', ['owner', 'admin'])->exists();
    }

    public function manageMembers(User $user, Channel $channel): bool
    {
        return $this->update($user, $channel);
    }
}
