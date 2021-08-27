<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use BaseModel;

    protected $table = 'settings';

    protected $fillable = [
        'generate_default_category'
    ];
}
