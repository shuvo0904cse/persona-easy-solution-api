<?php


namespace App\Helpers;

use Monolog;
use Illuminate\Support\Facades\File;

class LogHelper
{
    CONST DEFAULT_LOG_FILE_NAME = 'error_log';

    /**
     *  Log Info Message
     */
    public static function info( $labelName, $message = 'Change', $value = array('value' => 'message') )
    {
        $logger = self::setLogFile( $labelName );
        if( $logger )$logger->info( $message, $value );
    }

    /**
     *  Log Error Message
     */
    public static function error( $labelName, $message = 'Change', $value = array( 'value' => 'message' ) )
    {
        $logger = self::setLogFile( $labelName );
        if( $logger ) $logger->error( $message, $value );
    }

    /**
     *  Log Alert Message
     */
    public static function alert( $labelName, $value = array('value' => 'message') )
    {
        $logger = self::setLogFile( $labelName );
        if( $logger ) $logger->alert( 'Add Some Info message', $value );
    }

    /**
     *  Log warning Message
     */
    public static function warning( $labelName, $value = array('value' => 'message') )
    {
        $logger = self::setLogFile( $labelName );
        if( $logger ) $logger->warning( 'Add Some Info message', $value );
    }

    /**
     *  Log critical Message
     */
    public static function critical( $labelName, $value = array('value' => 'message') )
    {
        $logger = self::setLogFile( $labelName );
        if( $logger ) $logger->critical( 'Add Some Info message', $value );

    }

    /**
     *  Log emergency Message
     */
    public static function emergency( $labelName, $value = array('value' => 'message') )
    {
        $logger = self::setLogFile( $labelName );
        if( $logger ) $logger->emergency( 'Add Some Info message', $value );
    }


    /**
     *  Log file create for user module
     */
    protected static function setLogFile($labelName = null,$fileName= self::DEFAULT_LOG_FILE_NAME)
    {
        $log = new Monolog\Logger( $labelName );
        $dir = storage_path().'/logs/';

        //log dir permission
        if( !is_dir( $dir ) ) File::makeDirectory($dir, 0777, true, true);

        //pushHandler
        $log->pushHandler( new Monolog\Handler\StreamHandler(
            $dir.$fileName.'-'.date('Y-m-d').'.log'),0777
        );

        return $log;
    }

}
