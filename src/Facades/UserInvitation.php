<?php

namespace Musiwei\UserInvitation\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Musiwei\UserInvitation\UserInvitation
 */
class UserInvitation extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Musiwei\UserInvitation\UserInvitation::class;
    }
}
