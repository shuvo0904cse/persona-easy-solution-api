<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use BaseModel;

    protected $table = 'settings';

    protected $fillable = [
        'user_id',
        'generate_default_category'
    ];
}
