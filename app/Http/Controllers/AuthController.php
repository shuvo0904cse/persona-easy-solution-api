<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Helpers\UtilsHelper;
use App\Mail\VerificationEmail;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());
        
        try{
            //credential check
            $credentials = ['email' => $request->email, 'password' => $request->password, 'status' => "ACTIVE"];
            if (! $token = Auth::attempt($credentials)) return $this->message::errorMessage("Unauthorized");

            //return success message
            return $this->message::loginMessage($token);
        }catch(Exception $e){
            $this->log::error("login", $e);
            return $this->message::errorMessage($e->getMessage());
        }
    }

    /**
     * Register
     */
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|unique:users',
            'phone_number'  => 'required|unique:users',
            'password' => 'required'
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        DB::beginTransaction();

        try{
            //store data
            $userArray = [
                "name"          => $request['name'],
                "email"         => $request['email'],
                "phone_number"  => isset($request['phone_number']) ? $request['phone_number'] : null,
                "password"      => Hash::make($request['password']),
                'email_verification_token' => Str::random(40),
                "email_verified_at" => null,
                "status"        => "PENDING"
            ];
            $user = $this->userModel()->storeData( $userArray);

            //mail
            Mail::to($user->email)->send(new VerificationEmail($user));

            DB::commit();
            return $this->message::successMessage("Please check your email to activate your account", $user);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->log::error("register", $e);
            return $this->message::errorMessage($e->getMessage());
        }
    }

    /**
     * Verify Email
     */
    public function VerifyEmail($token){
        
        //if token null
        if($token == null) return $this->message::errorMessage("Invalid Login attempt");
        
        //token verify
        $user = $this->userModel()->details($token, 'email_verification_token');
        if(empty($user)) return $this->message::errorMessage("Invalid Login attempt");
        
        try{
            //update user
            $userArray = [
                "email_verified_at"         => Carbon::now(),
                'email_verification_token'  => null,
                "status"                    => "ACTIVE"
            ];
            $user = $this->userModel()->updateData($userArray, $user->id);

            return $this->message::successMessage("Your account is activated, you can log in now");
        } catch (\Exception $e) {
            $this->log::error("VerifyEmail", $e);
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
