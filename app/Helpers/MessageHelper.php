<?php


namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class MessageHelper
{
   /**
     * Throw New Exception
     */
    public static function throwExceptionMessage($message= "")
    {
        throw new \Exception($message,Response::HTTP_BAD_REQUEST );
    }

    /**
     * Throw Exception
     */
    public static function throwException($exception)
    {
        $message = config("message.400");
        if(!is_string($exception)){
            $message = $exception->getMessage();
            if($exception->getCode() == 0){
                $message = config("message.400");
            }
        } else {
            $message = $exception;
        }
        throw new \Exception($message,Response::HTTP_BAD_REQUEST );
    }

    /**
     * Json Response
     */
    public static function jsonResponse($results = null)
    {
        return new JsonResponse($results, Response::HTTP_OK);
    }

    /**
     * Error Message
     */
    public static function errorMessage($message = null, $errors = null)
    {
        return new JsonResponse([
            'key'       => config("setting.bad_request_key"),
            'message'   => empty($message) ? config("message.400") : $message,
            'errors'    => $errors,
            'timestamp' => Carbon::now(),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Validation Error Message
     */
    public static function validationErrorMessage($message = null, $errors = null)
    {
        return new JsonResponse([
            'key'       => config("setting.validation_key"),
            'message'   => empty($message) ? config("message.invalid_input") : $message,
            'errors'    => $errors,
            'timestamp' => Carbon::now(),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Success Message
     */
    public static function successMessage($message = null, $results = null)
    {
        return new JsonResponse([
            'key'       => config("setting.success_key"),
            'message'   => empty($message) ? config("message.executed_successfully") : $message,
            'results'   => $results,
            'timestamp' => Carbon::now()->toDateTimeString(),
        ], Response::HTTP_OK);
    }

    /**
     * Login Message
     */
    public static function loginMessage($token)
    {
        return new JsonResponse([
            'token'         => $token,
            'token_type'    => 'bearer',
            'expires_in'    => Auth::factory()->getTTL() * 60 * 60 * 7
        ], Response::HTTP_OK);
    }
}
