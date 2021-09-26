<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Helpers\UtilsHelper;
use App\Http\Resources\NoteListResource;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
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
    public function note(Request $request)
    {
        try{
            //filter
            $filterArray = [
                "is_pagination" => true,
                "limit"         => $request->limit,
                "search"        => [
                    "fields"    => ['id', 'title', 'description'],
                    "value"     => $request->search
                ]
            ];
            $lists = $this->noteModel()->lists($filterArray);

            $array = [
                "data"      => NoteListResource::collection($lists->items()),
                "paginate"  => UtilsHelper::getPaginate($lists)
            ];
            return $this->message::successMessage("", $array);
        } catch (\Exception $e) {
            $this->log::error("lists-note", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Details By Id
     */
    public function detailsById($id){
        try{
            //details
            $details = $this->noteModel()->details($id);

            //if not exists
            if(empty($details)) return $this->message::errorMessage("Note ". config("message.not_exit"));

            return $this->message::successMessage("", $details);
        } catch (\Exception $e) {
            $this->log::error("details-note", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Store
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'type' => 'required|in:NORMAL,EMERGENCY'
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        try{
            //store data
            $array = [
                "title"         => $request['title'],
                "description"   => $request['description'],
                "type"          => $request['type']
            ];
            $grocery = $this->noteModel()->storeData( $array);

            return $this->message::successMessage(config("message.save_message"), $grocery);
        } catch (\Exception $e) {
            $this->log::error("store-note", $e);
            return $this->message::errorMessage($e->getMessage());
        }
    }

    /**
     * Update
     */
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'type' => 'required|in:NORMAL,EMERGENCY'
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

         //details
         $details = $this->noteModel()->details($id);

         //if not exists
         if(empty($details)) return $this->message::errorMessage("Note ". config("message.not_exit"));

        try{
            //update data
            $array = [
                "title"         => $request['title'],
                "description"   => $request['description'],
                "type"          => $request['type'],
            ];
            $this->noteModel()->updateData( $array, $details->id );

             //details
            $details = $this->noteModel()->details($id);

            return $this->message::successMessage(config("message.update_message"), $details);
        } catch (\Exception $e) {
            $this->log::error("update-note", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Delete
     */
    public function delete($id){
        //grocery details
        $details = $this->noteModel()->details($id);

        //if not exists
        if(empty($details)) return $this->message::errorMessage("Note ". config("message.not_exit"));

        try{
            //delete Data
            $this->noteModel()->deleteData( $id );

            return $this->message::successMessage(config("message.delete_message"));
        } catch (\Exception $e) {
            $this->Log::error("delete-note", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Delete
     */
    public function updateStatus($id){
        //grocery details
        $details = $this->noteModel()->details($id);

        //if not exists
        if(empty($details)) return $this->message::errorMessage("Note ". config("message.not_exit"));

        try{
            //update data
            $array = [
                "status"       => $details['status']  == true ? false : true 
            ];
            $this->noteModel()->updateData( $array, $details->id );

             //details
            $details = $this->noteModel()->details($id);

            return $this->message::successMessage(config("message.update_message"), $details);
        } catch (\Exception $e) {
            $this->Log::error("update-status-note", $e);
            return $this->message::errorMessage();
        }
    }

    private function noteModel(){
        return new Note();
    }
}
