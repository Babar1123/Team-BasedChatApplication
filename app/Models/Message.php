<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'channel_id',
        'user_id',
        'parent_id',
        'message',
    ];

    protected $dates = ['deleted_at'];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id');
    }

    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class, 'message_id');
    }

    public function reactions()
    {
        return $this->hasMany(MessageReaction::class);
    }
}
