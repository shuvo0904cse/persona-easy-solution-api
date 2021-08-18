<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class MoneyModel extends Model
{
    use BaseModel;

    protected $table = 'monies';

    protected $fillable = [
        'category_id',
        'amount',
        'title',
        'note'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
    }
}
