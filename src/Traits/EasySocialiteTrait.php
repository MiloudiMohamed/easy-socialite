<?php
namespace Devmi\EasySocialite\Traits;

use Devmi\EasySocialite\Models\UserSocial;

trait EasySocialiteTrait
{
    public function social()
    {
        return $this->hasMany(UserSocial::class);
    }
}
