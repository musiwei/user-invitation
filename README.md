# Invite to register

[![Latest Version on Packagist](https://img.shields.io/packagist/v/musiwei/user-invitation.svg?style=flat-square)](https://packagist.org/packages/musiwei/user-invitation)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/musiwei/user-invitation/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/musiwei/user-invitation/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/musiwei/user-invitation/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/musiwei/user-invitation/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/musiwei/user-invitation.svg?style=flat-square)](https://packagist.org/packages/musiwei/user-invitation)

When your application doesn't allow user to register, invite to register is commonly used that adds an extra layer of security but still allows the new users to join.  

## Installation

You can install the package via composer:

### Step 1: add below to the end of your composer.json

```json
"repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:musiwei/user-invitation.git"
        }
    ]
```

### Step 2: run below in your command line 

```bash
composer require musiwei/user-invitation:dev-main
```

### Step 3: migrations and publish config

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="user-invitation-migrations"
php artisan migrate
```

Publish the config file:

```bash
php artisan vendor:publish --tag="user-invitation-config"
```

(Recommended) Publish the tranlsation file in laravel-vue-i18n (https://github.com/xiCO2k/laravel-vue-i18n) way:

Warning: this will publish the file to your project's `lang` folder.

```bash
php artisan vendor:publish --tag="user-invitation-translation-laravel-vue-i18n"
```

Publish the translation file the regular Laravel way:

```bash
php artisan vendor:publish --tag="user-invitation-translation"
```

This is the contents of the published config file:

```php
return [
    /*
     * Define the user class in your application here
     */
    'user'                                 => \App\Models\User::class,

    /*
     * Define the controller class in your application here
     */
    'controller'                           => \Musiwei\UserInvitation\Http\Controllers\UserInvitationsController::class,

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
            'email'   => ['required', 'string', 'max:255', 'email', 'unique:users'],
            'roles.*' => ['required', 'in:1,3'],
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
            'email.email'    => 'Please enter a valid email address. ',
            'email.unique'   => 'The email address you entered has already been registered. ',
            'roles.required' => 'A role must be assigned. ',
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

    /**
     * Default view settings, when you do not override, the below views will be used as responses.
     */
    'view' => [
        'inertia' => [
            'accept' => 'User/AcceptInvitation',
            'error' => 'Error/InvitationNotFound',
        ],
    ],
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="user-invitation-views"
```

## Usage

You only need to create the views to link to the endpoint this controller provides:
`Musiwei\UserInvitation\Http\Controllers\UserInvitationsController`

### Event
This package embraces event-driven and provides the below events:

- `UserInvitataionSent` triggered once an invitation has been sent, you will receive the invitation object in the event.
- `InvitationAcceptedAndUserRegistered` triggered once a user is registered, you will receive the newly created user in the event.

You may add a listener to listen to these events then implement your logic.   

### Modify the response

By default, the controller returns redirection or Inertia responses for Laravel Jetstream. To change the responses, you can inherit the controller and modify the corresponding methods as described below. 

#### Example

```php
class YourController extends UserInvitationsController
{
    protected function getSuccessfullySentInvitationResponse(): Responsable|RedirectResponse
    {
        return redirect()->back()->with('success', __('The invitation has been sent. '));
    }
}
```
#### Controller actions you can replace

You can customise responses for each endpoint.

- `getExtraAttributesForInvitation` is the extra fields you'd like to save into invitation
- `getExtraAttributesForUserCreation` is the extra field your'd like to save into user table populating from an Invitation model.
The rest are the customisable responses. 

Check the `Musiwei\UserInvitation\Http\Controllers\UserInvitationsController` for more information. 

```php
/**
 * This method can be overridden in a subclass
 *
 * @return array
 */
protected function getExtraAttributesForInvitation(): array
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
    return redirect()->route('dashboard')->with(
        'success',
        __('Congratulations, you have completed registration. ')
    );
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
    return Inertia::render('User/AcceptInvitation', ['invitation' => $invitation]);
}

/**
 * This method can be overridden in a subclass
 *
 * @return RedirectResponse|Responsable
 */
protected function getInvitationNotFoundResponse(): Responsable|RedirectResponse
{
    return Inertia::render('Error/InvitationNotFound');
}
```

## 

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Siwei Mu](https://github.com/musiwei)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
