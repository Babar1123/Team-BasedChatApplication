<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id',
        'name',
        'is_private',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function members()
    {
        return $this->hasMany(ChannelMember::class);
    }
}
