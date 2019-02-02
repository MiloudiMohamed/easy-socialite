<?php

namespace Devmi\EasySocialite\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserSocial extends Model
{
    protected $table = 'users_social';

    protected $fillable = ['user_id', 'provider_id', 'provider'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
