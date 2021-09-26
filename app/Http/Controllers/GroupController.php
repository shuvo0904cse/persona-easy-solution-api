<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Helpers\UtilsHelper;
use App\Http\Resources\GroupListResource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
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
    public function group(Request $request)
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
            $lists = $this->groupModel()->lists($filterArray, "*", ["userGroceries"]);

            $array = [
                "data"      => GroupListResource::collection($lists->items()),
                "paginate"  => UtilsHelper::getPaginate($lists)
            ];
            return $this->message::successMessage("", $array);
        } catch (\Exception $e) {
            return $e->getMessage().$e->getFile().$e->getLine();
            $this->log::error("lists-group", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Details By Id
     */
    public function detailsById($id){
        try{
            //details
            $details = $this->groupModel()->details($id, "id", false, ['userGroceries']);

            //if not exists
            if(empty($details)) return $this->message::errorMessage("Group ". config("message.not_exit"));

            return $this->message::successMessage("", $details);
        } catch (\Exception $e) {
            $this->log::error("details-group", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Store
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'groceries' => 'required|array',
        ], config("message.validation_message"));
        
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        DB::beginTransaction();
        try{
            //store data
            $array = [
                "title"                      => $request['title']
            ];
            $group = $this->groupModel()->storeData( $array);

            //grocery
            $groceryArray = [];
            foreach ($request->groceries as $grocery) {
                $groceryArray[$grocery['grocery_id']] = [
                    'amount'        => isset($grocery['amount']) ? $grocery['amount'] : null,
                    'unit'          => isset($grocery['unit']) ? $grocery['unit'] : null
                ]; 
            };       
            $group->userGroceries()->sync($groceryArray);

            //details
            $details = $this->groupModel()->details($group->id, "id", false, ['userGroceries']);

            DB::commit();
            return $this->message::successMessage(config("message.save_message"), $details);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->log::error("store-group", $e);
            return $this->message::errorMessage($e->getMessage());
        }
    }

    /**
     * Update
     */
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string'
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

         //details
         $details = $this->groupModel()->details($id);

         //if grocery not exists
         if(empty($details)) return $this->message::errorMessage("Group ". config("message.not_exit"));

        DB::beginTransaction();
        try{
            //update data
            $array = [
                "title" => $request['title']
            ];
            $this->groupModel()->updateData( $array, $details->id );

             //grocery
             $groceryArray = [];
             foreach ($request->groceries as $grocery) {
                 $groceryArray[$grocery['grocery_id']] = [
                     'amount'        => isset($grocery['amount']) ? $grocery['amount'] : null,
                     'unit'          => isset($grocery['unit']) ? $grocery['unit'] : null
                 ]; 
             };       
             $details->userGroceries()->sync($groceryArray);

             //details
            $details = $this->groupModel()->details($id, "id", false, ['userGroceries']);

            DB::commit();
            return $this->message::successMessage(config("message.update_message"), $details);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->log::error("update-group", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Delete
     */
    public function delete($id){
        //grocery details
        $details = $this->groupModel()->details($id);

        //if not exists
        if(empty($details)) return $this->message::errorMessage("Group ". config("message.not_exit"));

        DB::beginTransaction();
        try{

            //delete user groceroie
            $details->userGroceries()->detach($details->id);

            //delete Data
            $this->groupModel()->deleteData($details->id);

            DB::commit();
            return $this->message::successMessage(config("message.delete_message"));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->Log::error("delete-group", $e);
            return $this->message::errorMessage();
        }
    }

    private function groupModel(){
        return new Group();
    }
}
