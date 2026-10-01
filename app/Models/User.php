<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'owner_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(UserToken::class);
    }

    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function channelMemberships(): HasMany
    {
        return $this->hasMany(ChannelMember::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }
}
