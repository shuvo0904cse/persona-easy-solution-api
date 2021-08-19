<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class UserGroceryGroup extends Model
{
    use BaseModel;

    protected $table = 'user_grocery_groups';

    protected $fillable = [
        'user_id',
        'name'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function userGroceries()
    {
        return $this->belongsToMany(Grocery::class, "user_groceries")->withPivot('amount', 'unit');;
    }
}
