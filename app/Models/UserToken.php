<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    use HasFactory;

    protected $table = 'user_tokens';

    protected $fillable = [
        'user_id',
        'token',
        'device_name',
        'expires_at',
        'last_used_at',
        'revoked_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /**
     * When creating a token record, ensure token is stored hashed.
     */
    public static function booted()
    {
        static::creating(function ($token) {
            if (! empty($token->token) && strlen($token->token) !== 64) {
                $token->token = hash('sha256', $token->token);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
