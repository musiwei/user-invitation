<?php

use Illuminate\Support\Facades\Route;

Route::prefix(config('user-invitation.route.prefix'))
    ->middleware(config('user-invitation.route.middleware'))
    ->name(config('user-invitation.route.name'))
    ->group(function () {
        Route::post('/invite', [config('user-invitation.controller'), 'invite'])->name('invite');
        Route::get('/accept-invitation/{token}', [config('user-invitation.controller'), 'accept'])->name('accept');
        Route::post('/register', [config('user-invitation.controller'), 'register'])->name('register');
    });
