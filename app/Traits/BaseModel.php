<?php


namespace App\Traits;

use App\Scopes\QueryForUserIdScope;
use App\Services\UserService;
use Illuminate\Database\Eloquent\SoftDeletes;

trait BaseModel
{
    use SoftDeletes;

    /**
     * Exists Check
     */
    public function existsCheck($columnValue, $columnName = "id")
    {
        return self::where($columnName, $columnValue)->exists();
    }

    /**
     * lists
     */
    public function lists($isPaginate = true, $limit = 20)
    {
        $query = self::query();

        //if paginate
        if($isPaginate) return $query->paginate($limit);

        //if not paginate
        return $query->get();
    }

    /**
     * Details
     */
    public function details($columnValue, $columnName = "id", $withTrashed = false)
    {
        $query = self::where($columnName, $columnValue);

        if($withTrashed == true) $query = $query->withTrashed();

        return $query->first();
    }

    /**
     * Restore Data
     */
    public function restoreData($id)
    {
        return self::withTrashed()->find($id)->restore();
    }

    /**
     * Store Data
     */
    public function storeData($array)
    {
        return self::create($array);
    }

    /**
     * Update Data
     */
    public function updateData($array, $columnValue, $columnName = "id")
    {
        return self::where($columnName, $columnValue)->update($array);
    }

    /**
     * Delete Data
     */
    public function deleteData($columnValue, $columnName= "id", $bForceDelete=false)
    {
        if ($bForceDelete) {
            $data = self::where($columnName, $columnValue)->forceDelete();
        } else {
            $data = self::where($columnName, $columnValue)->delete();
        }
        return $data;
    }

    /**
     * Search
     */
    public function scopeOfSearch($query, $fields = [], $search = [])
    {
        if (!empty($fields)) {
            foreach ($fields as $relation => $field) {
                if (is_array($field)) {
                    $query->orWhereHas($relation, function ($q) use ($field, $search) {
                        $q->where(function ($q) use ($field, $search) {
                            foreach ($field as $relatedField) {
                                foreach ($search as $term) {
                                    $q->orWhere($relatedField, 'like', "%{$term}%");
                                }
                            }
                        });
                    });
                } else {
                    foreach ($search as $term) {
                        $query->orWhere($field, 'like', "%{$term}%");
                    }
                }
            }
            return $query;
        }
    }

      /**
     * Filter
     */
    public function scopeOfFilter($query, $filters = [])
    {
        if (!empty($filters)) {
            foreach ($filters as $key => $filter) {
                $fields = $filters[$key]['fields'];
                $filter = $filters[$key]['filter'];

                if(!empty($filter)){
                    if (is_array($fields)) {
                        foreach ($fields as $relation => $field) {
                            $query->whereHas($relation, function ($q) use ($field, $filter) {
                                $q->where($field, $filter);
                            });
                        }
                    } else {
                        $query->where($fields, $filter);
                    }
                }
            }
            return $query;
        }
    }

    /**
     * boot
     */
    public static function boot()
    {
        parent::boot();

       /*  self::creating(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'user_id')) $model->user_id = $this->userService()->getLoggedInUserId();
            if (Schema::hasColumn($model->getTable(), 'created_by')) $model->user_id = $this->userService()->getLoggedInUserId();
        });

        self::updated(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'updated_by')) $model->user_id = $this->userService()->getLoggedInUserId();
        });

        self::deleted(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'deleted_by')) $model->user_id = $this->userService()->getLoggedInUserId();
        }); */

        return static::addGlobalScope(new QueryForUserIdScope());
    }

    protected function userService(){
        return new UserService();
    }
}
