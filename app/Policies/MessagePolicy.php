<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function view(User $user, Message $message): bool
    {
        return $message->channel && $message->channel->team->company->owner_id === $user->id
            || ($message->channel && $message->channel->team->members()->where('user_id', $user->id)->exists())
            || ($message->channel && ! $message->channel->is_private && $message->channel->team->company->owner_id === $user->id);
    }

    public function update(User $user, Message $message): bool
    {
        return $message->user_id === $user->id
            || $message->channel->team->company->owner_id === $user->id
            || $message->channel->team->members()->where('user_id', $user->id)->whereIn('role', ['owner', 'admin'])->exists();
    }

    public function delete(User $user, Message $message): bool
    {
        return $this->update($user, $message);
    }
}
