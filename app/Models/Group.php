<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use BaseModel;

    protected $table = 'groups';

    protected $fillable = [
        'title'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function userGroceries()
    {
        return $this->belongsToMany(Grocery::class, "group_groceries")->withPivot('amount', 'unit');;
    }
}
