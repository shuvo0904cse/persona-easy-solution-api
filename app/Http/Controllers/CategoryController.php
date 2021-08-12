<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
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
    public function category()
    {
        return "OK";
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
            $this->log::error("storeCategory", $e);
            return $this->message::errorMessage();
        }

    }

    /**
     * Store
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
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
            $this->log::error("storeCategory", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Update
     */
    public function update(Request $request, $id){
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
            $this->log::error("updateDoor", $e);
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
            $this->Log::error("deleteCategory", $e);
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
