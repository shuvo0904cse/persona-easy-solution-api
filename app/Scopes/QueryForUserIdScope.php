<?php


namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class QueryForUserIdScope implements Scope
{
    /**
     * Table and Column name
     */
    public function apply(Builder $builder, Model $model)
    {
        $columnName = $model->getTable().".user_id";
        if (Schema::hasColumn($model->getTable(), 'user_id')){
            $builder->where($columnName, Auth::user()->id);
        }
    }
}
