<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class ProjectPhase extends Model
{
    use BaseModel;

    protected $table = 'projects_phase';

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'amount',
        'status'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
