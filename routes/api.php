<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChannelController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TeamMemberController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth.token')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('profile', [AuthController::class, 'profile']);

    Route::get('company', [CompanyController::class, 'show']);
    Route::put('company', [CompanyController::class, 'update']);
    Route::get('company/teams', [CompanyController::class, 'teams']);

    Route::get('teams', [TeamController::class, 'index']);
    Route::post('teams', [TeamController::class, 'store']);
    Route::get('teams/{team}', [TeamController::class, 'show']);
    Route::put('teams/{team}', [TeamController::class, 'update']);
    Route::delete('teams/{team}', [TeamController::class, 'destroy']);
    Route::get('teams/{team}/members', [TeamMemberController::class, 'index']);
    Route::post('teams/{team}/members', [TeamMemberController::class, 'store']);
    Route::put('teams/{team}/members/{user}/role', [TeamMemberController::class, 'updateRole']);
    Route::delete('teams/{team}/members/{user}', [TeamMemberController::class, 'destroy']);

    Route::get('teams/{team}/channels', [ChannelController::class, 'index']);
    Route::post('teams/{team}/channels', [ChannelController::class, 'store']);
    Route::get('channels/{channel}', [ChannelController::class, 'show']);
    Route::get('channels/{channel}/members', [ChannelController::class, 'members']);
    Route::post('channels/{channel}/members', [ChannelController::class, 'addMember']);
    Route::delete('channels/{channel}/members/{user}', [ChannelController::class, 'removeMember']);
    Route::put('channels/{channel}', [ChannelController::class, 'update']);
    Route::delete('channels/{channel}', [ChannelController::class, 'destroy']);

    Route::get('channels/{channel}/messages', [MessageController::class, 'index']);
    Route::post('channels/{channel}/messages', [MessageController::class, 'store']);
    Route::get('messages/{message}', [MessageController::class, 'show']);
    Route::put('messages/{message}', [MessageController::class, 'update']);
    Route::delete('messages/{message}', [MessageController::class, 'destroy']);
    Route::post('messages/{message}/attachments', [MessageController::class, 'attach']);
    Route::get('messages/{message}/replies', [MessageController::class, 'replies']);
    Route::post('messages/{message}/replies', [MessageController::class, 'reply']);
    Route::get('messages/{message}/reactions', [MessageController::class, 'reactionsIndex']);
    Route::post('messages/{message}/reactions', [MessageController::class, 'storeReaction']);
    Route::delete('messages/{message}/reactions/{reaction}', [MessageController::class, 'destroyReaction']);
});

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth.token')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::get('company', [CompanyController::class, 'show']);
        Route::put('company', [CompanyController::class, 'update']);
        Route::get('company/teams', [CompanyController::class, 'teams']);
        Route::get('teams', [TeamController::class, 'index']);
        Route::post('teams', [TeamController::class, 'store']);
        Route::get('teams/{team}', [TeamController::class, 'show']);
        Route::put('teams/{team}', [TeamController::class, 'update']);
        Route::delete('teams/{team}', [TeamController::class, 'destroy']);
        Route::get('teams/{team}/members', [TeamMemberController::class, 'index']);
        Route::post('teams/{team}/members', [TeamMemberController::class, 'store']);
        Route::put('teams/{team}/members/{user}/role', [TeamMemberController::class, 'updateRole']);
        Route::delete('teams/{team}/members/{user}', [TeamMemberController::class, 'destroy']);
        Route::get('teams/{team}/channels', [ChannelController::class, 'index']);
        Route::post('teams/{team}/channels', [ChannelController::class, 'store']);
        Route::get('channels/{channel}', [ChannelController::class, 'show']);
        Route::get('channels/{channel}/members', [ChannelController::class, 'members']);
        Route::post('channels/{channel}/members', [ChannelController::class, 'addMember']);
        Route::delete('channels/{channel}/members/{user}', [ChannelController::class, 'removeMember']);
        Route::put('channels/{channel}', [ChannelController::class, 'update']);
        Route::delete('channels/{channel}', [ChannelController::class, 'destroy']);
        Route::get('channels/{channel}/messages', [MessageController::class, 'index']);
        Route::post('channels/{channel}/messages', [MessageController::class, 'store']);
        Route::get('messages/{message}', [MessageController::class, 'show']);
        Route::put('messages/{message}', [MessageController::class, 'update']);
        Route::delete('messages/{message}', [MessageController::class, 'destroy']);
        Route::post('messages/{message}/attachments', [MessageController::class, 'attach']);
        Route::get('messages/{message}/replies', [MessageController::class, 'replies']);
        Route::post('messages/{message}/replies', [MessageController::class, 'reply']);
        Route::get('messages/{message}/reactions', [MessageController::class, 'reactionsIndex']);
        Route::post('messages/{message}/reactions', [MessageController::class, 'storeReaction']);
        Route::delete('messages/{message}/reactions/{reaction}', [MessageController::class, 'destroyReaction']);
    });
});
