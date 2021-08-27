<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Helpers\UtilsHelper;
use App\Http\Resources\ProjectListResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
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
    public function project(Request $request)
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
            $lists = $this->projectModel()->lists($filterArray);

            $array = [
                "data"      => ProjectListResource::collection($lists->items()),
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
            $details = $this->projectModel()->details($id);

            //if not exists
            if(empty($details)) return $this->message::errorMessage("Project ". config("message.not_exit"));

            return $this->message::successMessage("", $details);
        } catch (\Exception $e) {
            $this->log::error("details-project", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Store
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string'
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        try{
            //store data
            $array = [
                "title"         => $request['title'],
                "description"   => $request['description'],
                "start_date"    => $request['start_date'],
                "end_date"      => $request['end_date'],
            ];
            $grocery = $this->projectModel()->storeData( $array);

            return $this->message::successMessage(config("message.save_message"), $grocery);
        } catch (\Exception $e) {
            $this->log::error("store-project", $e);
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
         $details = $this->projectModel()->details($id);

         //if not exists
         if(empty($details)) return $this->message::errorMessage("Project ". config("message.not_exit"));

        try{
            //update data
            $array = [
                "title"         => $request['title'],
                "description"   => $request['description'],
                "start_date"    => $request['start_date'],
                "end_date"      => $request['end_date'],
            ];
            $this->projectModel()->updateData( $array, $details->id );

             //details
            $details = $this->projectModel()->details($id);

            return $this->message::successMessage(config("message.update_message"), $details);
        } catch (\Exception $e) {
            $this->log::error("update-project", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Delete
     */
    public function delete($id){
        //grocery details
        $details = $this->projectModel()->details($id);

        //if not exists
        if(empty($details)) return $this->message::errorMessage("Project ". config("message.not_exit"));

        try{
            //delete Data
            $this->projectModel()->deleteData( $id );

            return $this->message::successMessage(config("message.delete_message"));
        } catch (\Exception $e) {
            $this->Log::error("delete-project", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Delete
     */
    public function updateStatus($id){
        //grocery details
        $details = $this->projectModel()->details($id);

        //if not exists
        if(empty($details)) return $this->message::errorMessage("Note ". config("message.not_exit"));

        try{
            //update data
            $array = [
                "status"       => $details['status'] 
            ];
            $this->projectModel()->updateData( $array, $details->id );

             //details
            $details = $this->projectModel()->details($id);

            return $this->message::successMessage(config("message.update_message"), $details);
        } catch (\Exception $e) {
            $this->Log::error("update-project-note", $e);
            return $this->message::errorMessage();
        }
    }

    private function projectModel(){
        return new Project();
    }
}
