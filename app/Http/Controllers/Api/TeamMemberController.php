<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamMemberRequest;
use App\Http\Resources\TeamMemberResource;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TeamMemberController extends Controller
{
    public function index(Request $request, Team $team)
    {
        Gate::authorize('manageMembers', $team);

        return response()->success('Team members fetched', TeamMemberResource::collection($team->members()->with('user')->get()));
    }

    public function store(StoreTeamMemberRequest $request, Team $team)
    {
        Gate::authorize('manageMembers', $team);

        $data = $request->validated();
        $user = User::findOrFail($data['user_id']);

        if ($team->company_id !== $user->companies()->first()?->id) {
            return response()->error('User must belong to the same company.', 422);
        }

        $member = TeamMember::firstOrCreate([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ], [
            'role' => $data['role'] ?? 'member',
            'joined_at' => now(),
        ]);

        if ($member->wasRecentlyCreated) {
            $member->update(['role' => $data['role'] ?? 'member']);
        }

        return response()->success('Team member added', new TeamMemberResource($member->load('user')));
    }

    public function updateRole(Request $request, Team $team, User $user)
    {
        Gate::authorize('manageMembers', $team);

        $request->validate([
            'role' => ['required', 'in:owner,admin,member'],
        ]);

        $membership = $team->members()->where('user_id', $user->id)->firstOrFail();
        $membership->update(['role' => $request->input('role')]);

        return response()->success('Member role updated', new TeamMemberResource($membership->load('user')));
    }

    public function destroy(Request $request, Team $team, User $user)
    {
        Gate::authorize('manageMembers', $team);

        $membership = $team->members()->where('user_id', $user->id)->firstOrFail();
        $membership->delete();

        return response()->success('Team member removed');
    }
}
