<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Company;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->user()->companies()->first();
        $teams = Team::where('company_id', $company->id)->get();
        return response()->success('Teams fetched', $teams);
    }

    public function store(CreateTeamRequest $request)
    {
        $company = $request->user()->companies()->first();
        $team = Team::create(array_merge($request->validated(), ['company_id' => $company->id]));
        return response()->success('Team created', $team);
    }

    public function show(Request $request, Team $team)
    {
        $company = $request->user()->companies()->first();
        if ($team->company_id !== $company->id) {
            return response()->error('Forbidden', 403);
        }
        return response()->success('Team fetched', $team);
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        $company = $request->user()->companies()->first();
        if ($team->company_id !== $company->id) {
            return response()->error('Forbidden', 403);
        }
        $team->update($request->validated());
        return response()->success('Team updated', $team);
    }

    public function destroy(Request $request, Team $team)
    {
        $company = $request->user()->companies()->first();
        if ($team->company_id !== $company->id) {
            return response()->error('Forbidden', 403);
        }
        $team->delete();
        return response()->success('Team deleted');
    }
}
