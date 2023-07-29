<?php

namespace Musiwei\UserInvitation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
        'inviter_id',
        'roles',
        'extra_attributes'
    ];

    protected $casts = [
        'roles' => 'array',
        'extra_attributes' => 'array',
    ];
}
