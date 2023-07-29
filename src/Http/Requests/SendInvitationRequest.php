<?php

namespace Musiwei\UserInvitation\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Musiwei\UserInvitation\Contracts\AuthorizationStrategyContract;
use Musiwei\UserInvitation\Models\Invitation;
use Spatie\Permission\Models\Role;

class SendInvitationRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return config('user-invitation.validation_rules.send_invitation');
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return config('user-invitation.validation_messages.send_invitation');
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            /**
             * Restrict email sending waiting period to at least 10s
             */
            if (Invitation::where('email', $this->input('email'))->exists()) {
                $invitation = Invitation::where('email', $this->input('email'))->first();
                $invitationSentOn = Carbon::parse($invitation->updated_at);
                $now = Carbon::now();
                $diff = $now->diffInSeconds($invitationSentOn);

                $wait = (int) config('user-invitation.waiting_period_to_send_another_email');
                if ($diff < $wait) {
                    $wait -= $diff;
                    $validator->errors()->add('email', "Too Frequent. You can resend in another $wait seconds. ");
                }
            }

            /**
             * Restrict roles to the existing ones only
             */
            $roles = $this->input('roles');
            $existingRoleIds = Role::whereIn('id', $roles)->pluck('id')->toArray();

            if(count($roles) !== count($existingRoleIds)) {
                $validator->errors()->add('roles', 'Sorry, one or more of the roles you try to assign does not exist.');
            }
        });
    }
}
