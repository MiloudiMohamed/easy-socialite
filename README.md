# Easy Socialite

This package helps you to easily integrate your social media login and get started with

It's never been easier

## Installation

#### Notice
This package is depending on the Socialite official laravel package.

The documentation for it can be found on the [Laravel-website](https://laravel.com/docs/5.7/socialite).

so make sure to install it first.

From the command line navigate to your Laravel Project and run:

`composer require devmi/easy-socialite`

Then run your migration

`php artisan migrate`

> Note that the password column changed to be nullable on your users table

Finally use `Devmi\EasySocailite\EasySocialiteTrait` in your User Model

## Usage

Now all what you have to do is to make a request to `/login/{your-service}`

By default **Github**, **Google**, **Twitter**, **Facebook** are activated by default, to de-activate or add a new service you need to publish the configuration file and modify it as your need

After any new social account linked to your user a `Devmi\EasySocailite\SocialAccountLinked` event will be fired containing the following payload

- The user model created
- The linked service name
- The information sent back from the service (such as appId, avatar link ...)

So you can listen to the event and dispatch your listeners

#### Publishing the configuration file
From the command line navigate to your Laravel Project and run:

`php artisan vendor:publish --tag=easysocialite`

The file will be placed under your `config` directory

## Issues and Contribution
If you run into any bug or problem please make sure to open an issue or create a pull request, Thanks!

## Credits

[Bedrani Sidali](https://github.com/bboysidou)

[Miloudi Mohamed](https://github.com/MiloudiMohamed)

## Licence

The MIT License (MIT). Please see License File for more information.
