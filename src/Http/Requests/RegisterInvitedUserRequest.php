<?php

namespace Musiwei\UserInvitation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Musiwei\UserInvitation\Contracts\AuthorizationStrategyContract;

class RegisterInvitedUserRequest extends FormRequest
{

    public function __construct(protected AuthorizationStrategyContract $authorizationStrategy, ...$params)
    {
        parent::__construct(...$params);
    }

    public function authorize(): bool
    {
        return $this->authorizationStrategy->authorize($this);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return config('user-invitation.validation_rules.register_user');
    }

    public function messages(): array
    {
        return config('user-invitation.validation_messages.register_user');
    }

}
