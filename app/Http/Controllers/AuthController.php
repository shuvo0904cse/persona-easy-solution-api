<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Helpers\UtilsHelper;
use App\Mail\VerificationEmail;
use App\Models\User;
use App\Models\UserVerificationCode;
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
                'email_verification_token' => Str::random(10),
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
     * Verify User
     */
    public function verifyUser(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'code'  => 'required',
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        //user exists
        $user = $this->userModel()->details($request->email, "email");
        if(empty($user)) return $this->message::errorMessage("User ". config("message.not_exit"));

        //verification code exists
        $code = $this->userVerificationCodeModel()->details($request->code, "code");
        if(empty($code)) return $this->message::errorMessage("Code ". config("message.not_exit"));

        //verification code exists check in timestamp
        if($code->expire_date > Carbon::now()) return $this->message::errorMessage("Code Expired");

        //verification match with user
        $userVerification = $this->userModel()->detailsWithMultiple([
             "user_id"  => $user->id,
             "code"     => $code->code
         ]);

        if(empty($userVerification)) return $this->message::errorMessage("User Verification Not Matched");
        
        DB::beginTransaction();
        try{
            //verification code store
            $userUpdate = [
                'email_verified_at'           => $user->id,
                'status' => 1, // verification code function
            ];
            $code = $this->userModel()->updateData($userUpdate, $user->id);

            //delete user verification

            //send mail
            
            DB::commit();
            return $this->message::successMessage("");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->log::error("verifyUser", $e);
            return $this->message::errorMessage($e->getMessage());
        }
    }

    /**
     * Forgot Password
     */
    public function forgotPassword(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());
        
        //user exists
        $user = $this->userModel()->details($request->email, "email");
        if(empty($user)) return $this->message::errorMessage("User ". config("message.not_exit"));

        try{
            //verification code store
            $verification = [
                'user_id'           => $user->id,
                'verification_code' => 1, // verification code function
                'expire_date'       => 1 // expire date function
            ];
            $code = $this->userVerificationCodeModel()->storeData($verification);

            //send mail


            return $this->message::successMessage("");
        } catch (\Exception $e) {
            $this->log::error("forgotPassword", $e);
            return $this->message::errorMessage($e->getMessage());
        }
    }

    /**
     * Reset Password
     */
    public function resetPassword(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'new_password' => 'required',
            'new_confirm_password' => 'same:new_password',
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

         //user exists
        $user = $this->userModel()->details($request->email, "email");
        if(empty($user)) return $this->message::errorMessage("User ". config("message.not_exit"));

         
        try{
            //password update
            $userPassword = [
                'password'           => Hash::make($request->new_password)
            ];
            $this->userModel()->updateData($userPassword, $user->id);

            //send mail

            return $this->message::successMessage("");
        } catch (\Exception $e) {
            $this->log::error("changePassword", $e);
            return $this->message::errorMessage($e->getMessage());
        }
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'current_password' => 'required',
            'new_password' => 'required',
            'new_confirm_password' => 'same:new_password',
        ], config("message.validation_message"));
        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

         //user exists
        $user = $this->userModel()->details($request->email, "email");
        if(empty($user)) return $this->message::errorMessage("User ". config("message.not_exit"));

         
        try{
            //password update
             $userPassword = [
                'password'           => Hash::make($request->new_password)
            ];
            $this->userModel()->updateData($userPassword, $user->id);

            //send mail

            return $this->message::successMessage("");
        } catch (\Exception $e) {
            $this->log::error("changePassword", $e);
            return $this->message::errorMessage($e->getMessage());
        }
    }

    /**
     * category Model
     */
    private function userModel(){
        return new User();
    }

    /**
     * category Model
     */
    private function userVerificationCodeModel(){
        return new UserVerificationCode();
    }
}
