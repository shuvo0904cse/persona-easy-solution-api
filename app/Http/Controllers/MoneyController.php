<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Models\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MoneyController extends Controller
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
    public function money()
    {
        return "OK";
    }

    /**
     * Details By Id
     */
    public function detailsById($id){
        try{
            //money details
            $money = $this->moneyModel()->details($id);

            //if category not exists
            if(empty($money)) return $this->message::errorMessage("Money ". config("message.not_exit"));

            return $this->message::successMessage("", $money);
        } catch (\Exception $e) {
            $this->log::error("details", $e);
            return $this->message::errorMessage();
        }

    }

    /**
     * Store
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|int',
            'amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'title' => 'required|string'
        ]);
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        try{
            //store data
            $moneyArray = [
                "user_id"             => 1,
                "category_id"         => $request['category_id'],
                "amount"              => $request['amount'],
                "title"               => $request['title'],
                "note"                => $request['note']
            ];
            $money = $this->moneyModel()->storeData( $moneyArray);

            return $this->message::successMessage(config("message.save_message"), $money);
        } catch (\Exception $e) {
            $this->log::error("store", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Update
     */
    public function update(Request $request, $id){
         //money details
         $money = $this->moneyModel()->details($id);

         //if money not exists
         if(empty($money)) return $this->message::errorMessage("Money ". config("message.not_exit"));

        try{
            //update data
            $moneyArray = [
                "user_id"             => 1,
                "category_id"         => $request['category_id'],
                "amount"              => $request['amount'],
                "title"               => $request['title'],
                "note"                => $request['note']
            ];
            $this->moneyModel()->updateData( $moneyArray, $money->id );

             //money details
            $money = $this->moneyModel()->details($id);

            return $this->message::successMessage(config("message.update_message"), $money);
        } catch (\Exception $e) {
            $this->log::error("update", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Delete
     */
    public function delete($id){
        //money details
        $money = $this->moneyModel()->details($id);

        //if category not exists
        if(empty($money)) return $this->message::errorMessage("Money ". config("message.not_exit"));

        try{
            //delete Data
            $this->moneyModel()->deleteData( $id );
            return $this->message::successMessage(config("message.delete_message"));
        } catch (\Exception $e) {
            $this->Log::error("delete", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * category Model
     */
    private function moneyModel(){
        return new Money();
    }
}
