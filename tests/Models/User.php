<?php
namespace Devmi\EasySocialite\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Devmi\EasySocialite\Traits\EasySocialiteTrait;

class User extends Authenticatable
{
    use EasySocialiteTrait;
    protected $guarded = [];
}