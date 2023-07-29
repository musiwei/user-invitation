<?php

use Illuminate\Support\Facades\Route;
use Musiwei\UserInvitation\Http\Controllers\UserInvitationsController;

Route::middleware('web')->group(function () {
    Route::prefix(config('user-invitation.route.prefix', 'user-invitation'))
        ->middleware(config('user-invitation.route.middleware', []))
        ->name(config('user-invitation.route.name', 'user-invitation'))
        ->group(function () {
            Route::post(
                '/invite',
                [
                    config(
                        'user-invitation.controller',
                        UserInvitationsController::class
                    ),
                    'invite',
                ]
            )->name('invite');

            Route::get(
                '/accept-invitation/{token}',
                [
                    config(
                        'user-invitation.controller',
                        UserInvitationsController::class
                    ),
                    'accept',
                ]
            )->name('accept');

            Route::post(
                '/register',
                [
                    config(
                        'user-invitation.controller',
                        UserInvitationsController::class
                    ),
                    'register',
                ]
            )->name('register');
        });
});
