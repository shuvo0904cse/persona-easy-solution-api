<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class UserVerificationCode extends Model
{
    use BaseModel;

    protected $table = 'user_verification_codes';

    protected $fillable = [
        'user_id',
        'verification_code',
        'expire_date'
    ];
}
