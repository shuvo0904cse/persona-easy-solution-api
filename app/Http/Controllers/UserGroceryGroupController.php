<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Helpers\UtilsHelper;
use App\Models\UserGroceryGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserGroceryGroupController extends Controller
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
    public function userGroceryGroup(Request $request)
    {
        try{
            //filter
            $filterArray = [
                "is_pagination" => true,
                "limit"         => $request->limit,
                "search"        => [
                    "fields"    => ['id', 'name'],
                    "value"     => $request->search
                ]
            ];
            $lists = $this->userGroceryGroupModel()->with('users')->toSql();
            return $lists;
            // $array = [
            //     "data"      => $lists->items(),
            //     "paginate"  => UtilsHelper::getPaginate($lists)
            // ];
            // return $this->message::successMessage("", $array);
        } catch (\Exception $e) {
            $this->log::error("lists-user-grocery-group", $e);
            return $this->message::errorMessage($e->getMessage().$e->getFile().$e->getLine());
        }
    }

    /**
     * Details By Id
     */
    public function detailsById($id){
        try{
            //details
            $details = $this->userGroceryGroupModel()->details($id);

            //if not exists
            if(empty($details)) return $this->message::errorMessage("User Grocery ". config("message.not_exit"));

            return $this->message::successMessage("", $details);
        } catch (\Exception $e) {
            $this->log::error("details-user-grocery-group", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Store
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'groceries' => 'required|array',
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        DB::beginTransaction();
        try{
            //store data
            $array = [
                "name"                      => $request['name']
            ];
            $grocery = $this->userGroceryGroupModel()->storeData( $array);

            //users grocery
            $amount = $request->amount;
            $groceries = collect($request->groceries)->map(function() use ($amount){
                return ['amount' => $amount];
            });
            dd($groceries);
            $grocery->userGroceries()->sync($request->groceries);

            DB::commit();
            return $this->message::successMessage(config("message.save_message"), $grocery);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->log::error("store-user-grocery-group", $e);
            return $this->message::errorMessage($e->getMessage());
        }
    }

    /**
     * Update
     */
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

         //details
         $details = $this->userGroceryGroupModel()->details($id);

         //if grocery not exists
         if(empty($details)) return $this->message::errorMessage("User Grocery ". config("message.not_exit"));

        DB::beginTransaction();
        try{
            //update data
            $array = [
                "name"                      => $request['name']
            ];
            $this->userGroceryGroupModel()->updateData( $array, $details->id );

             //details
            $details = $this->userGroceryGroupModel()->details($id);

            DB::commit();
            return $this->message::successMessage(config("message.update_message"), $details);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->log::error("update-user-grocery-group", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Delete
     */
    public function delete($id){
        //grocery details
        $details = $this->userGroceryGroupModel()->details($id);

        //if category not exists
        if(empty($details)) return $this->message::errorMessage("User Grocery ". config("message.not_exit"));

        DB::beginTransaction();
        try{
            //delete user groceries
            $details->userGroceries()->detach($details->id);

            //delete Data
            $this->userGroceryGroupModel()->deleteData( $id );

            DB::commit();
            return $this->message::successMessage(config("message.delete_message"));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->Log::error("delete-user-grocery-group", $e);
            return $this->message::errorMessage();
        }
    }

    private function userGroceryGroupModel(){
        return new UserGroceryGroup();
    }
}
