<?php

namespace App\Http\Middleware;

use App\Models\UserToken;
use Closure;
use Illuminate\Http\Request;

class TokenAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');

        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return response()->error('Authorization token not provided', 401);
        }

        $token = substr($header, 7);
        $hashed = hash('sha256', $token);
        $record = UserToken::where('token', $hashed)->first();

        if (! $record) {
            return response()->error('Invalid token', 401);
        }

        if ($record->revoked_at || ($record->expires_at && $record->expires_at->isPast())) {
            return response()->error('Token is expired or revoked', 401);
        }

        $record->last_used_at = now();
        $record->saveQuietly();

        $request->setUserResolver(function () use ($record) {
            return $record->user()->firstOrFail();
        });

        return $next($request);
    }
}
