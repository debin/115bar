<?php

/*
 * 公共函数
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function sample() {
    echo "this is a common function test";
}

/*
 * 处理接受的数据
 * @Params string $value
 */
function returnParam($value){
    $value = trim($value);
    return $value;
    //return strip_tags(htmlspecialchars($data,ENT_QUOTES));
    //return htmlspecialchars(strip_tags($data));
    //return strip_tags($data);
}

/*
 * 返回数据
 * @Param $msg string
 * @Param $data array
 * @Param $result boolean
 * @Param $code int
 *
 */
function callback($code=0, $msg='', $data=array(),$result=true){
    echo json_encode(array(
        "msg"    => $msg,
        "data"   => $data,
        "result" => $result,
        "code"   => $code,
    ));
    exit;
    return;
}

/**
 * 随机字条串
 *
 * @author xie
 * @param int $leng 长度
 * @return string
 */
function randStr($leng = 6) {
    $string_s = "qwertyuipkjhgfdsazxcvbnm123456789";
    $new_str = "";
    for ($i = 0; $i < $leng; $i++) {
        $new_str .= $string_s{rand(0, strlen($string_s) - 1)};
    }
    return $new_str;
}

/**
 * 获取语言包
 *
 * @author ldb
 * @param string $la 语言包标签
 * @param string $lang_type 语言包类型，默认为当前用户语言
 * @return string
 */
function _($la,$lang_type='') {
    return I18nHelper::getInstance()->getLang($la,$lang_type);
}


/**
 * 对一个字符串或者array中的字符串 htmlspecialchars
 *
 * @author ldb
 * @param mixed $mixed 要处理的数据
 * @return mixed
 */

function cat_html($mixed, $quote_style = ENT_QUOTES, $charset = 'UTF-8') {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = cat_html($value, $quote_style, $charset);
        }
    } elseif (is_string($mixed)) {
        $mixed = htmlspecialchars($mixed, $quote_style, $charset);
    }
    return $mixed;
}

/**
 * 对一个字符串或者array中的字符串 escape
 *
 * @author ldb
 * @param mixed $mixed 要处理的数据
 * @return mixed
 */
function cat_escape($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = cat_escape($value);
        }
    } elseif (is_string($mixed)) {
        $mixed = mysql_escape_string($mixed);
    }
    return $mixed;
}

/**
 * 对一个字符串去掉 br nbsp
 *
 * @author ldb
 * @param mixed $mixed 要处理的数据
 * @return mixed
 */
function trim_string($string) {
    $string = trim($string);
    $string = str_replace("<br>",' ',$string);
    $string = str_replace("<br />",' ',$string);
    $string = str_replace("&nbsp;",' ',$string);
    $string = str_replace("  ",' ',$string);
    if (strpos($string, '  ')!==false) {
        $string = trim_string($string);
    }
    return $string;
}


// 添加 nofollow 的回调函数
function addnofollow($matches) {

    // var_dump($matches);
    $string = $matches[0] . '  rel="nofollow" target="_blank" ';
    return $string;
}


/**
 * 格式化
 */
function print_rr($data){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}