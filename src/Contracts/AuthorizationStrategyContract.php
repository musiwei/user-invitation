<?php

namespace Musiwei\UserInvitation\Contracts;

use Illuminate\Foundation\Http\FormRequest;

interface AuthorizationStrategyContract
{
    public function authorize(FormRequest $request): bool;
}
