<?php

namespace Musiwei\UserInvitation\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationAcceptedAndUserRegistered
{

    use Dispatchable, SerializesModels;

    public function __construct(public Authenticatable $user)
    {
    }

}
