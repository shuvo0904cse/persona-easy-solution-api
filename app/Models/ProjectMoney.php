<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class ProjectMoney extends Model
{
    use BaseModel;

    protected $table = 'projects_money';

    protected $fillable = [
        'phase_id',
        'title',
        'description',
        'amount',
        'type'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function phase()
    {
        return $this->belongsTo(ProjectPhase::class, 'phase_id', 'id');
    }
}
