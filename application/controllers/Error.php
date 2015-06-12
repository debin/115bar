<?php

/**
 * 当有未捕获的异常, 则控制流会流到这里
 */
class ErrorController extends Yaf_Controller_Abstract {

    public function init() {
        // Yaf_Dispatcher::getInstance()->c = $this;//保存当前控制器
        Yaf_Dispatcher::getInstance()->disableView();
    }

    public function errorAction($exception) {
        /* error occurs */
        switch ($exception->getCode()) {
        case YAF_ERR_NOTFOUND_MODULE:
        case YAF_ERR_NOTFOUND_CONTROLLER:
        case YAF_ERR_NOTFOUND_ACTION:
        case YAF_ERR_NOTFOUND_VIEW:
            //echo 404, ":", $exception->getMessage();
            $array           = array();
            $array["result"] = false;
            $array["code"]   = -99;
            $array["msg"]    = $exception->getMessage();
            $array["data"]   = $exception->getTrace();
            // $array["data"]   = $exception->getTraceAsString();
            // echo json_encode($array);
            // break;
            $this->getView()->display("error/404.html");
            // exit;
            return;
        default :
            $array           = array();
            $array["result"] = false;
            $array["code"]   = -98;
            $array["msg"]    = $exception->getMessage();
            $array["data"]   = $exception->getTrace();
            // $array["data"] = $exception->getTraceAsString();
            // echo json_encode($array);
            
            // 文件日志
            Log::addLog($array);

            // 邮件提醒  5分钟一次
            $subject = "网站异常:".date("m-d H:i");
            $text = $exception->getTraceAsString();
            MailHelper::getInstance()->sendTip($subject,$text);

            $this->getView()->display("error/error.html");
            return;
        }
        return;
        // $ex = new Exception($exception->getMessage(), $exception->getCode());
        // $ex->getMessage();
    }

    private function _pageNotFound(){
        $this->getResponse()->setHeader('HTTP/1.0 404 Not Found');
        $this->_view->error = 'Page was not found';
    }

    private function _unknownError(){
        $this->getResponse()->setHeader('HTTP/1.0 500 Internal Server Error');
        $this->_view->error = 'Application Error';
    }
}
