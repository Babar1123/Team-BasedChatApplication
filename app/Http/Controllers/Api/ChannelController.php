<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateChannelRequest;
use App\Http\Requests\UpdateChannelRequest;
use App\Models\Channel;
use App\Models\ChannelMember;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ChannelController extends Controller
{
    public function index(Request $request, Team $team)
    {
        $company = $request->user()->companies()->first();
        if ($team->company_id !== $company->id) {
            return response()->error('Forbidden', 403);
        }

        $channels = Channel::where('team_id', $team->id)->get();

        return response()->success('Channels fetched', $channels);
    }

    public function store(CreateChannelRequest $request, Team $team)
    {
        $company = $request->user()->companies()->first();
        if ($team->company_id !== $company->id) {
            return response()->error('Forbidden', 403);
        }

        $channel = Channel::create(array_merge($request->validated(), ['team_id' => $team->id, 'is_private' => (bool) $request->boolean('is_private')]));

        return response()->success('Channel created', $channel);
    }

    public function show(Request $request, Channel $channel)
    {
        if (! Gate::allows('view', $channel)) {
            return response()->error('Forbidden', 403);
        }

        return response()->success('Channel fetched', $channel);
    }

    public function update(UpdateChannelRequest $request, Channel $channel)
    {
        if (! Gate::allows('update', $channel)) {
            return response()->error('Forbidden', 403);
        }

        $channel->update($request->validated());

        return response()->success('Channel updated', $channel);
    }

    public function destroy(Request $request, Channel $channel)
    {
        if (! Gate::allows('delete', $channel)) {
            return response()->error('Forbidden', 403);
        }

        $channel->delete();

        return response()->success('Channel deleted');
    }

    public function members(Request $request, Channel $channel)
    {
        if (! Gate::allows('view', $channel)) {
            return response()->error('Forbidden', 403);
        }

        return response()->success('Channel members fetched', $channel->members()->with('user')->get());
    }

    public function addMember(Request $request, Channel $channel)
    {
        if (! Gate::allows('manageMembers', $channel)) {
            return response()->error('Forbidden', 403);
        }

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($request->input('user_id'));
        $isTeamMember = $channel->team->members()->where('user_id', $user->id)->exists();

        if (! $isTeamMember) {
            return response()->error('User must belong to the same team before joining a private channel.', 422);
        }

        $member = ChannelMember::firstOrCreate([
            'channel_id' => $channel->id,
            'user_id' => $user->id,
        ]);

        return response()->success('Channel member added', $member->load('user'));
    }

    public function removeMember(Request $request, Channel $channel, User $user)
    {
        if (! Gate::allows('manageMembers', $channel)) {
            return response()->error('Forbidden', 403);
        }

        $channel->members()->where('user_id', $user->id)->delete();

        return response()->success('Channel member removed');
    }
}
