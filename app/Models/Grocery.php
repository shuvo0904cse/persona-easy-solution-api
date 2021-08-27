<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Grocery extends Model
{
    use BaseModel;

    protected $table = 'groceries';

    protected $fillable = [
        'title'
    ];
}
