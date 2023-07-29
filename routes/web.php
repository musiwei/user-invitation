<?php

use Illuminate\Support\Facades\Route;
use Musiwei\UserInvitation\Http\Controllers\UserInvitationsController;

Route::prefix(config('user-invitation.route.prefix'))
    ->middleware(config('user-invitation.route.middleware'))
    ->name(config('user-invitation.route.name'))
    ->group(function () {
        Route::post('/invite', [config('user-invitation.controller'), 'invite']);
        Route::get('/accept-invitation/{token}', [config('user-invitation.controller'), 'accept']);
        Route::post('/register', [config('user-invitation.controller'), 'register']);
    });
