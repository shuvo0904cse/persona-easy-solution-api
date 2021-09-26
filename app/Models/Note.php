<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use BaseModel;

    protected $table = 'notes';

    protected $fillable = [
        'title',
        'description',
        'type'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
