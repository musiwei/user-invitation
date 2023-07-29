<?php

namespace Musiwei\UserInvitation\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Musiwei\UserInvitation\Models\Invitation;
use Musiwei\UserInvitation\Notifications\InvitationNotification;

class UserInvitationService
{

    /**
     * @param  string  $email
     *
     * @return Invitation
     * @throws ModelNotFoundException
     */
    public function getByEmail(string $email): Invitation
    {
        return Invitation::where('email', $email)->firstOrFail();
    }

    /**
     * @param  Invitation  $invitation
     * @param  array  $values
     *
     * @return bool
     */
    public function update(Invitation $invitation, array $values): bool
    {
        return $invitation->update($values);
    }

    /**
     * @param  array  $values
     *
     * @return Invitation
     */
    public function create(array $values): Invitation
    {
        return Invitation::create($values);
    }

    /**
     * @param  Invitation  $invitation
     *
     * @return bool|null
     */
    public function remove(Invitation $invitation): ?bool
    {
        return $invitation->delete();
    }

    /**
     * @return string
     */
    public function generateUniqueToken(): string
    {
        do {
            $token = Str::random(config('user-invitation.token_length'));
        } while ($this->getByToken($token));

        return $token;
    }

    /**
     * @param  string  $token
     * @param  bool  $firstOrFail
     *
     * @return Invitation|bool
     */
    public function getByToken(string $token, bool $firstOrFail = false): mixed
    {
        if ( ! $firstOrFail) {
            return Invitation::where('token', $token)->first();
        }

        return Invitation::where('token', $token)->firstOrFail();
    }

    /**
     * Send the email invitation that contains the link
     *
     * @param  string  $email
     * @param  string  $token
     *
     * @return void
     */
    public function sendEmail(string $email, string $token): void
    {
        Notification::route('mail', $email)->notify(
            new InvitationNotification($this->generateTemporaryLink($token), config('user-invitation.valid_hour'))
        );
    }

    /**
     * @param  string  $token
     *
     * @return string
     */
    public function generateTemporaryLink(string $token): string
    {
        return URL::temporarySignedRoute(
            'user.accept-invitation',
            now()->addHours(config('user-invitation.valid_hour')),
            ['token' => $token]
        );
    }

}
