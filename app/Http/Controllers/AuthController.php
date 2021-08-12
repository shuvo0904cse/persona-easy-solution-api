<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    private $message;
    private $log;

    public function __construct()
    {
        $this->message = new MessageHelper();
        $this->log = new LogHelper();
    }

    /**
     * Login
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        try{
            $client = new \GuzzleHttp\Client(['verify' => false]);

            $response = $client->request('POST', 'http://localhost:8000/v1/oauth/token', [
                'form_params' => [
                    "client_secret" => "7kfkXeJknoChs2qoPXoVlbeC96ubeonHWQFZmiIK",
                    "grant_type"    => "password",
                    "client_id"     => 2,
                    "username"      => $request->email,
                    "password"      => $request->password
                ]
            ]);
            $response = $response->getBody()->getContents();
            echo '<pre>';
            print_r($response);
exit();
            $response = Http::post('http://127.0.0.1:8000/v1/oauth/token', [
                "form_params" => [
                    "client_secret" => "7kfkXeJknoChs2qoPXoVlbeC96ubeonHWQFZmiIK",
                    "grant_type"    => "password",
                    "client_id"     => 2,
                    "username"      => $request->email,
                    "password"      => $request->password
                ]
            ]);
            dd($response);
        }catch(Exception $e){
            $this->log::error("login", $e);
            return $this->message::errorMessage();
        }
    }

    /**
     * Register
     */
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'email' => 'required|unique:users',
            'phone_number'  => 'required|string',
            'password' => 'required|min:5|max:8'
        ]);
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        try{
            //store data
            $userArray = [
                "first_name"    => $request['first_name'],
                "last_name"     => isset($request['last_name']) ? $request['last_name'] : null,
                "email"         => $request['email'],
                "phone_number"  => isset($request['phone_number']) ? $request['phone_number'] : null,
                "password"      => Hash::make($request['password'])
            ];

            $user = $this->userModel()->storeData( $userArray);

            return $this->message::successMessage(config("message.save_message"), $user);
        } catch (\Exception $e) {
            $this->log::error("register", $e);
            return $this->message::errorMessage($e->getMessage());
        }
    }

    /**
     * category Model
     */
    private function userModel(){
        return new User();
    }
}
