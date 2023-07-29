<?php

namespace Musiwei\UserInvitation\Policies\FormRequestAuthorizations;

use Musiwei\UserInvitation\Contracts\AuthorizationStrategyContract;

class DenyAuthorizationStrategy implements AuthorizationStrategyContract
{
    public function authorize($request): bool
    {
        return false;
    }
}
