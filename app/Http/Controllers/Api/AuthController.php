<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Channel;
use App\Models\Company;
use App\Models\Team;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $company = Company::create([
                'name' => $data['company_name'] ?? ($user->name . "'s Company"),
                'owner_id' => $user->id,
            ]);

            $team = Team::create([
                'company_id' => $company->id,
                'name' => 'General',
            ]);

            Channel::create([
                'team_id' => $team->id,
                'name' => 'Announcements',
                'is_private' => false,
            ]);

            return $user->fresh();
        });

        $token = Str::random(60);
        UserToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addDays(30),
        ]);

        $user = $user->load('companies');

        return response()->success('Registered successfully', [
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->error('Invalid credentials', 401);
        }

        $token = Str::random(60);
        UserToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addDays(30),
        ]);

        return response()->success('Logged in', [
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $header = $request->header('Authorization');
        $token = $header ? substr($header, 7) : null;

        if ($token) {
            $hashed = hash('sha256', $token);
            UserToken::where('token', $hashed)
                ->where('user_id', $user->id)
                ->update(['revoked_at' => now()]);
        }

        return response()->success('Logged out');
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('companies');
        return response()->success('User retrieved', $user);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load('companies');
        return response()->success('User profile retrieved', $user);
    }
}
