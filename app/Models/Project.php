<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use BaseModel;

    protected $table = 'projects';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
