<?php

use Illuminate\Validation\Rules;

// config for Musiwei/UserInvitation
return [
    /*
     * Define the user class in your application here
     */
    'user'                                 => \App\Models\User::class,

    /*
     * Define generated token length, default 20 letters.
     */
    'token_length'                         => 20,

    /*
     * Define temparary url link valid period, default 72 hours.
     */
    'valid_hour'                           => 72,

    /*
     * Rate limiting for too frequent email sending, user must wait at least X seconds to send another invitation, default 10 seconds.
     */
    'waiting_period_to_send_another_email' => 10,

    /*
     * Locale column name in user table
     *
     * Suggested plugin: https://github.com/akaunting/laravel-language
     */
    'locale_db_column_name'                => 'locale',

    /*
     * Default locale for each new registered user, providing the above locale column exists
     */
    'default_locale'                       => 'en',

    /*
     * Form request fields
     */
    'validation_rules'                     => [
        'register_user'   => [
            'name'     => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'token'    => ['required'],
        ],
        'send_invitation' => [
            'email' => ['required', 'string', 'max:255', 'email', 'unique:users'],
            'roles' => ['required'],
        ],
    ],

    /*
     * Validation messages associated with above
     */
    'validation_messages'                  => [
        'register_user'   => [
            'password.required' => 'Please enter the email address you would like to send the invitation to. ',
            'token.required'    => 'Invitation token is missing, please contact admin. ',
        ],
        'send_invitation' => [
            'email.required' => 'Please enter the email address you would like to send the invitation to. ',
            'email.unique'   => 'The email address you entered has already been registered. ',
        ],
    ],

    /*
     * Inject authorization strategy for each form request. Default always returns true.
     * Default option 1: \Musiwei\UserInvitation\Policies\FormRequestAuthorizations\AllowAuthorizationStrategy::class: returns true
     * Default option 2: \Musiwei\UserInvitation\Policies\FormRequestAuthorizations\AllowAuthorizationStrategy::class: returns false
     *
     * You can also create your own strategy.
     *
     * Recommended, especially when you want to restrict permission that who can invite user to register,
     * use with https://github.com/spatie/laravel-permission for the best result
     *
     * 1. Create the strategy in your application. `App\Policies\FormRequestAuthorizations` recommended for better folder structure
     *
     * 2. It should implement \Musiwei\UserInvitation\Contracts\AuthorizationStrategyContract, code example:
     *
     * class SendInvitationAuthorizationStrategy implements AuthorizationStrategyContract
     * {
     *      public function authorize($request): bool
     *      {
     *              $user = $request->user();
     *
     *              return $user->can('inviteUser', [User::class]);
     *      }
     * }
     *
     * 3. Change the setting below to point to your strategy.
     */
    'authorization_strategies'             => [
        'register_user'   => \Musiwei\UserInvitation\Policies\FormRequestAuthorizations\AllowAuthorizationStrategy::class,
        'send_invitation' => \Musiwei\UserInvitation\Policies\FormRequestAuthorizations\AllowAuthorizationStrategy::class,
    ],

    /*
     * Route settings, this will apply to all the routes in this package
     *
     * Url prefix: e.g. http://example.com/user-invitation/register
     * Middleware: must be an array, e.g. ['auth:sanctum', 'verified']
     * Route name: is useful for grouping the routes, most of the time you should use route name, avoid using url in your application if possible
     */
    'route'                                => [
        'prefix'     => 'user-invitation',
        'middleware' => [],
        'name'       => 'user-invitation',
    ],
];
