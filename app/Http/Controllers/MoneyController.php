<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Helpers\UtilsHelper;
use App\Http\Resources\MoneyListResource;
use App\Models\Category;
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
    public function money(Request $request)
    {
        try{
             //filter
             $filterArray = [
                "is_pagination" => true,
                "limit"         => $request->limit,
                "search"        => [
                    "fields"    => ['id', 'amount', 'title', 'note', 'categories' => ['id', 'title']],
                    "value"     => $request->search
                ],
                "filter"        => [
                    ['fields' => ['categories' => 'type'], 'filter' => $request->type],
                ]
            ];
        
            $lists = $this->moneyModel()->lists($filterArray, "*", ['category']);
            
            $array = [
                "data"      => MoneyListResource::collection($lists->items()),
                "paginate"  => UtilsHelper::getPaginate($lists)
            ];
            return $this->message::successMessage("", $array);
        } catch (\Exception $e) {
            $this->log::error("details-money", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Details By Id
     */
    public function detailsById($id){
        try{
            //money details
            $money = $this->moneyModel()->details($id);

            //if money not exists
            if(empty($money)) return $this->message::errorMessage("Money ". config("message.not_exit"));

            return $this->message::successMessage("", $money);
        } catch (\Exception $e) {
            $this->log::error("details-money", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Store
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id'   => 'required',
            'amount'        => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'title'         => 'required|string'
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        //category details
        $category = $this->categoryModel()->details($request->category_id);

        //if category not exists
        if(empty($category)) return $this->message::errorMessage("Category ". config("message.not_exit"));

        try{
            //store data
            $moneyArray = [
                "category_id"         => $request['category_id'],
                "amount"              => $request['amount'],
                "title"               => $request['title'],
                "note"                => $request['note']
            ];
            $money = $this->moneyModel()->storeData( $moneyArray);

            return $this->message::successMessage(config("message.save_message"), $money);
        } catch (\Exception $e) {
            $this->log::error("store-money", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Update
     */
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'category_id'   => 'required',
            'amount'        => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'title'         => 'required|string'
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

         //money details
         $money = $this->moneyModel()->details($id);

         //if money not exists
         if(empty($money)) return $this->message::errorMessage("Money ". config("message.not_exit"));

        try{
            //update data
            $moneyArray = [
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
            $this->log::error("update-money", $e);
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
            $this->Log::error("delete-money", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * category Model
     */
    private function moneyModel(){
        return new Money();
    }

    /**
     * category Model
     */
    private function categoryModel(){
        return new Category();
    }
}
