<?php
namespace Devmi\EasySocialite\Events;

use App\User;
use Illuminate\Queue\SerializesModels;

class SocialAccountLinked
{
    use SerializesModels;

    public $user;
    public $provider;
    public $providerUser;

    public function __construct(User $user, $provider, $providerUser)
    {
        $this->user = $user;
        $this->provider = $provider;
        $this->providerUser = $providerUser;
    }
}