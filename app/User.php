<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Notifications;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'address', 'phone', 'qb_customer_id', 'pricelevels_id', 'password', 'type', 'pricelist_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sendEmailVerificationNotification()
    {
        for($i = 3; $i > 0; $i--){
            try {
                $this->notify(new Notifications\VerifyEmail);
                break;
            } catch (\Exception $e) {
                var_dump($e);
            }
        }
    }
}
