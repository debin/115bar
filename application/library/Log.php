<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * 日志记录类
 * @author ldb
 * @package library
 */
class Log
{

    /**
     * 记录日志
     * @param array $data [description]
     */
    public static function addLog(array $data)
    {
        // $request_path = ConfigLogPath::getLogPath();
        $request_path = "/opt/log/";

        $file_name = $request_path . 'php_115app_log_'.date("Y-m").'.txt';

        try {
            // create a log channel
            $log = new Logger('115app');
            $log->pushHandler(new StreamHandler($file_name, Logger::DEBUG));
            
            // add records to the log
            $log->addError(json_encode($data));
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }

    }
}
