<?php

namespace Musiwei\UserInvitation\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Musiwei\UserInvitation\Models\Invitation;

class UserInvitationSent
{

    use Dispatchable, SerializesModels;

    public function __construct(public Invitation $invitation)
    {
    }

}
