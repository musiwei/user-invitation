<?php

use Illuminate\Support\Facades\Route;
use Musiwei\UserInvitation\Http\Controllers\UserInvitationsController;

Route::prefix(config('user-invitation.route.prefix'))
    ->middleware(config('user-invitation.route.middleware'))
    ->name(config('user-invitation.route.name'))
    ->group(function () {
        Route::post('/invite', [UserInvitationsController::class, 'invite']);
        Route::get('/accept-invitation/{token}', [UserInvitationsController::class, 'accept']);
        Route::post('/register', [UserInvitationsController::class, 'register']);
    });
