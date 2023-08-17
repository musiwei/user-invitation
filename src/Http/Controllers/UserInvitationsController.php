<?php

namespace Musiwei\UserInvitation\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Musiwei\UserInvitation\Events\InvitationAcceptedAndUserRegistered;
use Musiwei\UserInvitation\Events\UserInvitationSent;
use Musiwei\UserInvitation\Http\Requests\RegisterInvitedUserRequest;
use Musiwei\UserInvitation\Http\Requests\SendInvitationRequest;
use Musiwei\UserInvitation\Models\Invitation;
use Musiwei\UserInvitation\Services\UserInvitationService;
use Spatie\Permission\Models\Role;

class UserInvitationsController
{

    use AuthorizesRequests, ValidatesRequests;

    public function __construct(protected UserInvitationService $userInvitationService)
    {
    }

    /**
     * Invite a new user to register via email invitation.
     *
     * @param  SendInvitationRequest  $request
     *
     * @return RedirectResponse
     */
    public function invite(SendInvitationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $token = $this->userInvitationService->generateUniqueToken();
        $currentUserId = Auth::id();

        $baseData = [
            'token'      => $token,
            'email'      => $validated['email'],
            'inviter_id' => $currentUserId,
            'roles'      => $validated['roles'],
        ];

        // Merge extra attributes, you may define your logic by replacing the below function
        $extraAttributes = $this->getExtraAttributesForInvitation($validated);
        $invitationData = array_merge($baseData, $extraAttributes);

        /**
         * KEY LOGIC:
         * If the invitation has already been created, update the existing token, otherwise create a new one.
         * A user can avoid sending multiple invitations but only refresh the valid time
         */
        try {
            $invitation = $this->userInvitationService->getByEmail($validated['email']);
            $this->userInvitationService->update($invitation, $baseData); // Note we use baseData here
        } catch (ModelNotFoundException) {
            $invitation = $this->userInvitationService->create($invitationData); // Note we use invitationData here
        }

        UserInvitationSent::dispatch($invitation);

        $this->userInvitationService->sendEmail($validated['email'], $token);

        return $this->getSuccessfullySentInvitationResponse();
    }

    /**
     * Page to show after user clicked the invitation link
     *
     * @param  string  $token
     *
     * @return Responsable|RedirectResponse
     */
    public function accept(string $token): Responsable|RedirectResponse
    {
        try {
            $invitation = $this->userInvitationService->getByToken($token, true);

            return $this->getAcceptInvitationResponse($invitation);
        } catch (ModelNotFoundException) {
            // Error page
            return $this->getInvitationNotFoundResponse();
        }
    }

    /**
     * Handle invite-to-register request.
     *
     * @param  RegisterInvitedUserRequest  $request
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\RedirectResponse
     */
    public function register(RegisterInvitedUserRequest $request)
    {
        $validated = $request->validated();

        try {
            $invitation = $this->userInvitationService->getByToken($validated['token'], true);

            // When validated, create new user
            $userModel = config('user-invitation.user');
            $userAttributes = [
                'name'     => $validated['name'],
                'email'    => $invitation->email,
                'password' => Hash::make($validated['password']),
            ];

            // Append `locale` if `locale` column exists in user table, value can be set in config
            $localeDbColumnName = (string)config('user-invitation.locale_db_column_name');

            $userModelInstance = new $userModel;
            if (Schema::hasColumn($userModelInstance->getTable(), $localeDbColumnName)) {
                $defaultLocale = (string)config('user-invitation.default_locale');

                $userAttributes[$localeDbColumnName] = $defaultLocale;
            }

            // Merge extra attributes, you may define your logic by replacing the below function
            $extraAttributes = $this->getExtraAttributesForUserCreation($invitation);
            $userData = array_merge($userAttributes, $extraAttributes);

            $user = $userModel::create($userData);

            // Set user to be verified (this is for logical reason: user comes to this page from the link in the invitation email)
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }

            // Assign roles
            foreach ($invitation->roles as $r) {
                $user->assignRole(Role::findById($r));
            }

            // Remove the invitation
            $this->userInvitationService->remove($invitation);

            // Automatically log the user in and go to homepage
            Auth::login($user);

            InvitationAcceptedAndUserRegistered::dispatch($user);

            return $this->getSuccessfulRegistrationResponse($user);
        } catch (ModelNotFoundException) {
            return $this->getInvitationNotFoundResponse();
        }
    }

    /**
     * This method can be overridden in a subclass
     *
     * @param  array  $validated
     *
     * @return array
     */
    protected function getExtraAttributesForInvitation(array $validated): array
    {
        return [];
    }

    /**
     * This method can be overridden in a subclass
     *
     * @param  \Musiwei\UserInvitation\Models\Invitation  $invitation
     *
     * @return array
     */
    protected function getExtraAttributesForUserCreation(Invitation $invitation): array
    {
        return [];
    }

    /**
     * This method can be overridden in a subclass
     *
     * @return RedirectResponse|Responsable
     */
    protected function getSuccessfullySentInvitationResponse(): Responsable|RedirectResponse
    {
        return redirect()->back()->with('success', __('The invitation has been sent. '));
    }

    /**
     * This method can be overridden in a subclass
     *
     * @param  Authenticatable  $user
     *
     * @return RedirectResponse|Responsable
     */
    protected function getSuccessfulRegistrationResponse(Authenticatable $user): Responsable|RedirectResponse
    {
        return redirect(\App\Providers\RouteServiceProviderRouteServiceProvider::HOME);;
    }

    /**
     * This method can be overridden in a subclass
     *
     * @param  Invitation  $invitation
     *
     * @return RedirectResponse|Responsable
     */
    protected function getAcceptInvitationResponse(Invitation $invitation): Responsable|RedirectResponse
    {
        // Registration page
        return Inertia::render(config('user-invitation.view.inertia.accept'), ['invitation' => $invitation]);
    }

    /**
     * This method can be overridden in a subclass
     *
     * @return RedirectResponse|Responsable
     */
    protected function getInvitationNotFoundResponse(): Responsable|RedirectResponse
    {
        return Inertia::render(config('user-invitation.view.inertia.error'));
    }

}
