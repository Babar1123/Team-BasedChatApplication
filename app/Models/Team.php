<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
    ];

    protected $dates = ['deleted_at'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    public function members()
    {
        return $this->hasMany(TeamMember::class);
    }
}
