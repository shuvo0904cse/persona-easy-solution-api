<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Helpers\UtilsHelper;
use App\Http\Resources\GroceryListResource;
use App\Models\Grocery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroceryController extends Controller
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
    public function grocery(Request $request)
    {
        try{
            //filter
            $filterArray = [
                "is_pagination" => true,
                "limit"         => $request->limit,
                "search"        => [
                    "fields"    => ['id', 'title'],
                    "value"     => $request->search
                ]
            ];
            $lists = $this->groceryModel()->lists($filterArray);

            $array = [
                "data"      => GroceryListResource::collection($lists->items()),
                "paginate"  => UtilsHelper::getPaginate($lists)
            ];
            return $this->message::successMessage("", $array);
        } catch (\Exception $e) {
            $this->log::error("grocery", $e);
            return $this->message::errorMessage();
        }
    }

     /**
     * lists
     */
    public function lists()
    {
        try{
            $lists = $this->groceryModel()->lists(null, ['id', 'title']);
            return $this->message::successMessage("", $lists);
        } catch (\Exception $e) {
            $this->log::error("lists", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Details By Id
     */
    public function detailsById($id){
        try{
            //grocery details
            $grocery = $this->groceryModel()->details($id);

            //if grocery not exists
            if(empty($grocery)) return $this->message::errorMessage("Grocery ". config("message.not_exit"));

            return $this->message::successMessage("", $grocery);
        } catch (\Exception $e) {
            $this->log::error("details-grocery", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Store
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:groceries,title'
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        try{
            //store data
            $groceryArray = [
                "title"                      => $request['title']
            ];
            $grocery = $this->groceryModel()->storeData( $groceryArray);

            return $this->message::successMessage(config("message.save_message"), $grocery);
        } catch (\Exception $e) {
            $this->log::error("store-grocery", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Update
     */
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:grocery,title'
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

         //grocery details
         $grocery = $this->groceryModel()->details($id);

         //if grocery not exists
         if(empty($grocery)) return $this->message::errorMessage("Grocery ". config("message.not_exit"));

        try{
            //update data
            $groceryArray = [
                "title"                      => $request['title']
            ];
            $this->groceryModel()->updateData( $groceryArray, $grocery->id );

             //grocery details
            $grocery = $this->groceryModel()->details($id);

            return $this->message::successMessage(config("message.update_message"), $grocery);
        } catch (\Exception $e) {
            $this->log::error("update-grocery", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Delete
     */
    public function delete($id){
        //grocery details
        $grocery = $this->groceryModel()->details($id);

        //if category not exists
        if(empty($grocery)) return $this->message::errorMessage("Grocery ". config("message.not_exit"));

        try{
            //delete Data
            $this->groceryModel()->deleteData( $id );
            return $this->message::successMessage(config("message.delete_message"));
        } catch (\Exception $e) {
            $this->Log::error("delete-grocery", $e);
            return $this->message::errorMessage();
        }
    }

    private function groceryModel(){
        return new Grocery();
    }
}
