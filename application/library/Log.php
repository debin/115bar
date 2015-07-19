<?php

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
            $file = fopen($file_name, 'a+');
            fwrite($file, json_encode($data)."\n");
            fclose($file);
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
        return true;
    }
}
