<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Money extends Model
{
    use BaseModel, SoftDeletes;

    protected $table = 'money';

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'title',
        'note'
    ];
}
