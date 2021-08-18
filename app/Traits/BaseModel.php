<?php


namespace App\Traits;

use App\Scopes\QueryForUserIdScope;
use App\Services\UserService;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

trait BaseModel
{
    use SoftDeletes, UsesUuid;

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
    public function lists($requestData)
    {
        $query = self::query();

        //limit
        $limit = isset($requestData['limit']) && !empty($requestData['limit']) ? $requestData['limit'] : 10; 

        //if search exists
        if(isset($requestData['search'])) $query = $query->ofSearch($requestData['search']['fields'], $requestData['search']['value']);

        //if filter exists
        if(isset($requestData['filter'])) $query = $query->ofFilter($requestData['filter']);

        //if paginate
        if(isset($requestData['is_pagination']) && $requestData['is_pagination'] == true) return $query->paginate($limit);

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
        if (!empty($fields) && !empty($search)) {
            foreach ($fields as $relation => $field) {
                if(is_string($search)) $search = [$search];
                
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

        $userService = new UserService();

        self::creating(function ($model) use($userService) {
            if (Schema::hasColumn($model->getTable(), 'user_id')) $model->user_id = $userService->getLoggedInUserId();
            if (Schema::hasColumn($model->getTable(), 'created_by')) $model->created_by = $userService->getLoggedInUserId();
        });

        self::updated(function ($model) use($userService) {
            if (Schema::hasColumn($model->getTable(), 'updated_by')) $model->updated_by = $userService->getLoggedInUserId();
        });

        self::deleted(function ($model) use($userService) {
            if (Schema::hasColumn($model->getTable(), 'deleted_by')) $model->deleted_by = $userService->getLoggedInUserId();
        }); 

        return static::addGlobalScope(new QueryForUserIdScope());
    }
}
