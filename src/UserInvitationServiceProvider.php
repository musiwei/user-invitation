<?php

namespace Musiwei\UserInvitation;

use Illuminate\Foundation\Http\FormRequest;
use Musiwei\UserInvitation\Contracts\AuthorizationStrategyContract;
use Musiwei\UserInvitation\Http\Requests\RegisterInvitedUserRequest;
use Musiwei\UserInvitation\Http\Requests\SendInvitationRequest;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Musiwei\UserInvitation\Commands\UserInvitationCommand;

class UserInvitationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('user-invitation')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_user_invitation_table')
            ->hasCommand(UserInvitationCommand::class);
    }

    public function register()
    {
        parent::register();

        // RegisterInvitedUserRequest customisable authorization
        $this->app->when(RegisterInvitedUserRequest::class)
            ->needs(AuthorizationStrategyContract::class)
            ->give(function () {
                $strategy = config('user-invitation.authorization_strategies.register_user');

                if (class_exists($strategy)) {
                    return new $strategy;
                }

                return new class implements AuthorizationStrategyContract {
                    public function authorize(FormRequest $request): bool
                    {
                        return true;
                    }
                };
            });

        // SendInvitationRequest customisable authorization
        $this->app->when(SendInvitationRequest::class)
            ->needs(AuthorizationStrategyContract::class)
            ->give(function () {
                $strategy = config('user-invitation.authorization_strategies.send_invitation');

                if (class_exists($strategy)) {
                    return new $strategy;
                }

                return new class implements AuthorizationStrategyContract {
                    public function authorize(FormRequest $request): bool
                    {
                        return true;
                    }
                };
            });
    }

}
