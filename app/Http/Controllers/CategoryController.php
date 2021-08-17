<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Helpers\UtilsHelper;
use App\Http\Resources\CategoryListResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    private $message;
    private $log;

    public function __construct()
    {
        $this->message = new MessageHelper();
        $this->log = new LogHelper();
    }

     /**
     * index
     */
    public function category(Request $request)
    {
        try{
            $lists = $this->categoryModel()->lists(true, $request->limit, $request->search);

            $array = [
                "data"      => CategoryListResource::collection($lists->items()),
                "paginate"  => UtilsHelper::getPaginate($lists)
            ];
            return $this->message::successMessage("", $array);
        } catch (\Exception $e) {
            $this->log::error("lists-category", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Details By Id
     */
    public function detailsById($id){
        try{
            //category details
            $category = $this->categoryModel()->details($id);

            //if category not exists
            if(empty($category)) return $this->message::errorMessage("Category ". config("message.not_exit"));

            return $this->message::successMessage("", $category);
        } catch (\Exception $e) {
            $this->log::error("details-category", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Store
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name',
            'type' => 'required|in:INCOME,EXPENSE'
        ]);
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        try{
            //store data
            $categoryArray = [
                "user_id"                   => 1,
                "name"                      => $request['name'],
                "icon"                      => $request['icon'],
                "type"                      => $request['type']
            ];
            $category = $this->categoryModel()->storeData( $categoryArray);

            return $this->message::successMessage(config("message.save_message"), $category);
        } catch (\Exception $e) {
            $this->log::error("store-category", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Update
     */
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name',
            'type' => 'required|in:INCOME,EXPENSE'
        ]);
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

         //category details
         $category = $this->categoryModel()->details($id);

         //if category not exists
         if(empty($category)) return $this->message::errorMessage("Category ". config("message.not_exit"));

        try{
            //update data
            $categoryArray = [
                "name"                      => $request['name'],
                "icon"                      => $request['icon'],
                "type"                      => $request['type']
            ];
            $this->categoryModel()->updateData( $categoryArray, $category->id );

             //category details
            $category = $this->categoryModel()->details($id);

            return $this->message::successMessage(config("message.update_message"), $category);
        } catch (\Exception $e) {
            $this->log::error("update-category", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Delete
     */
    public function delete($id){
        //category details
        $category = $this->categoryModel()->details($id);

        //if category not exists
        if(empty($category)) return $this->message::errorMessage("Category ". config("message.not_exit"));

        try{
            //delete Data
            $this->categoryModel()->deleteData( $id );
            return $this->message::successMessage(config("message.delete_message"));
        } catch (\Exception $e) {
            $this->Log::error("delete-category", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * category Model
     */
    private function categoryModel(){
        return new Category();
    }
}
