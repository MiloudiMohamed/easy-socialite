<?php

namespace Devmi\EasySocialite\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Devmi\EasySocialite\Models\UserSocial;
use Devmi\EasySocialite\Events\SocialAccountLinked;

class SocialLoginController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $providerUser = Socialite::driver($provider)->user();
        $user = $this->getExistingUser($providerUser);

        if (!$user) {
            $user = $this->createNewUser($providerUser);
        }

        if ($this->shouldCreateNewProvider($user, $provider)) {
            $this->createNewProvider($user, $provider, $providerUser);

            event(new SocialAccountLinked($user, $provider, $providerUser));
        }

        Auth::login($user, false);
        return redirect()->intended();
    }

    protected function shouldCreateNewProvider(User $user, $provider)
    {
        return ! (bool) $user->social->where('provider', $provider)->count();
    }

    protected function getExistingUser($providerUser)
    {
        return User::where('email', $providerUser->getEmail())->orWhereHas('social', function($builder) use ($providerUser) {
            $builder->where('provider_id', $providerUser->getId());
        })->first();
    }

    protected function createNewUser($providerUser)
    {
        return User::create([
            'email' => $providerUser->getEmail(),
            'name' => $providerUser->getName(),
        ]);
    }

    protected function createNewProvider(User $user, $provider, $providerUser)
    {
        $user->social()->create([
            'provider_id' => $providerUser->getId(),
            'provider' => $provider,
        ]);
    }
}
