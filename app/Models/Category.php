<?php

namespace App\Models;

use App\Scopes\QueryForUserIdScope;
use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use BaseModel;

    protected $table = 'categories';

    protected $fillable = [
        'title',
        'icon',
        'type'
    ];
}
