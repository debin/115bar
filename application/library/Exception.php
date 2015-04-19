<?php

/**
 * 异常文件
 * @author dbb
 */
class Exception extends Exception {

    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
        $this->getCodeConfig();
    }

    private $_errCode = array();

    private function getCodeConfig() {
        $this->_errCode = require_once ROOT."/conf/errcode.php";
    }

    public function getErrorMessage() {
        $err = parent::getMessage();
        $code = parent::getCode();
        $code_msg = empty($this->_errCode[$code]) ? 0 : $this->_errCode[$code];
        if (DEBUGS) {
            header("Content-type: text/html; charset=utf-8");
            echo $err . "<br>";
            $str = '';
            $str .= "code:" . $code . '<br>msg:' . $code_msg . '<br>';
            $str .= '<span style="color:blue;">File:</span> <span style="color:red;">' . parent::getFile() . '</span><br>';
            $str .= 'File line:' . parent::getLine() . '<br>';
            $str .= parent::getTraceAsString();
            echo $str;
            exit;
        } else {
            Pub_Url::to_503($this->getCode());
        }
    }
}
