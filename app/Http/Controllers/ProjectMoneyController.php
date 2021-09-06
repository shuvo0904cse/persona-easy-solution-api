<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Helpers\UtilsHelper;
use App\Http\Resources\ProjectMoneyListResource;
use App\Models\Project;
use App\Models\ProjectMoney;
use App\Models\ProjectPhase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectMoneyController extends Controller
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
    public function projectMoney(Request $request)
    {
        try{
            //filter
            $filterArray = [
                "is_pagination" => true,
                "limit"         => $request->limit,
                "search"        => [
                    "fields"    => ['id', 'title', 'description', 'start_date', 'end_date'],
                    "value"     => $request->search
                ]
            ];
            $lists = $this->projectMoneyModel()->lists($filterArray);

            $array = [
                "data"      => ProjectMoneyListResource::collection($lists->items()),
                "paginate"  => UtilsHelper::getPaginate($lists)
            ];
            return $this->message::successMessage("", $array);
        } catch (\Exception $e) {
            $this->log::error("lists-project", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Details By Id
     */
    public function detailsById($id){
        try{
            //details
            $details = $this->projectMoneyModel()->details($id);

            //if not exists
            if(empty($details)) return $this->message::errorMessage("Project Money ". config("message.not_exit"));

            return $this->message::successMessage("", $details);
        } catch (\Exception $e) {
            $this->log::error("details-project-money", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Store
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'phase_id' => 'required',
            'title' => 'required|string',
            'amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'type'  => 'required|in:INCOME,EXPENSE'
        ], config("message.validation_message"));
        
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        try{
            //store data
            $array = [
                "phase_id"      => $request['phase_id'],
                "title"         => $request['title'],
                "description"   => $request['description'],
                "amount"        => $request['amount'],
                "type"          => $request['type']
            ];
            $grocery = $this->projectMoneyModel()->storeData( $array);

            return $this->message::successMessage(config("message.save_message"), $grocery);
        } catch (\Exception $e) {
            $this->log::error("store-project-money", $e);
            return $this->message::errorMessage($e->getMessage());
        }
    }

    /**
     * Update
     */
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'phase_id' => 'required',
            'title' => 'required|string',
            'amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'type'  => 'required|in:INCOME,EXPENSE'
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

         //details
         $details = $this->projectMoneyModel()->details($id);

         //if not exists
         if(empty($details)) return $this->message::errorMessage("Project Money". config("message.not_exit"));

        try{
            //update data
            $array = [
                "phase_id"      => $request['phase_id'],
                "title"         => $request['title'],
                "description"   => $request['description'],
                "amount"        => $request['amount'],
                "type"          => $request['type']
            ];
            $this->projectMoneyModel()->updateData( $array, $details->id );

             //details
            $details = $this->projectMoneyModel()->details($id);

            return $this->message::successMessage(config("message.update_message"), $details);
        } catch (\Exception $e) {
            $this->log::error("update-project-money", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Delete
     */
    public function delete($id){
        //grocery details
        $details = $this->projectMoneyModel()->details($id);

        //if not exists
        if(empty($details)) return $this->message::errorMessage("Project Phase". config("message.not_exit"));

        try{
            //delete project pase
            $this->projectMoneyModel()->deleteData( $id );

            //delete project phase money

            return $this->message::successMessage(config("message.delete_message"));
        } catch (\Exception $e) {
            $this->Log::error("delete-project-money", $e);
            return $this->message::errorMessage();
        }
    }

    private function projectMoneyModel(){
        return new ProjectMoney();
    }
}
