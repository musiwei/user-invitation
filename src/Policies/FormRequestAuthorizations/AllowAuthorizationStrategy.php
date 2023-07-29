<?php

namespace Musiwei\UserInvitation\Policies\FormRequestAuthorizations;

use Musiwei\UserInvitation\Contracts\AuthorizationStrategyContract;

class AllowAuthorizationStrategy implements AuthorizationStrategyContract
{
    public function authorize($request): bool
    {
        return true;
    }
}
