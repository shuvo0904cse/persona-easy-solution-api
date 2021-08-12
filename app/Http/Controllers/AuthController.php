<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Http\Requests\StoreUserRequest;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
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
    public function register(StoreUserRequest $request){

        dd($request->first_name);
        $firstName = $request->first_name;
        $lastName = $request->last_name;
        $email = $request->email;
        $password = $request->password;

        try{
            $response = Http::post('/v1/oauth/token', [
                "form_params" => [
                    "client_secret" => "ZdhI39mfo2u2tEa6LjvOBQGEqXBJxTa8M5pkjO1B",
                    "grant_type"    => "password",
                    "client_id"     => 2,
                    "username"      => $email,
                    "password"      => $password
                ]
            ]);
            dd($response);
        }catch(Exception $e){
            $this->log::error("login", $e);
            return $this->message::errorMessage();
        }
    }
}
