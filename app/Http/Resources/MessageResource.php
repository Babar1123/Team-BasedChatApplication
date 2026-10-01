<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray($request): array
    {
        $reactions = $this->reactions ?? collect();
        $counts = [];

        foreach ($reactions as $reaction) {
            $counts[$reaction->reaction] = ($counts[$reaction->reaction] ?? 0) + 1;
        }

        $currentUserReaction = $this->when(
            $request->user(),
            $reactions->first(fn ($reaction) => $reaction->user_id === $request->user()->id)?->reaction,
        );

        return [
            'id' => $this->id,
            'channel_id' => $this->channel_id,
            'parent_id' => $this->parent_id,
            'message' => $this->message,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ] : null,
            'parent' => $this->whenLoaded('parent') && $this->parent ? [
                'id' => $this->parent->id,
                'message' => $this->parent->message,
            ] : null,
            'reactions' => $counts,
            'current_user_reaction' => $currentUserReaction,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
