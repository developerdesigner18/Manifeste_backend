<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    public $timestamps=false;
    protected $fillable = [
        'login_email',
        'code'
    ];
}
