<?php

/**
 * 跳转页面
 * @author dbb
 */
class Url
{
    public static function to_503($err = '')
    {
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        header('Retry-After: 1800');
        header("Content-type: text/html; charset=utf-8");
        if (empty($err))
        {
            echo '503 Service Unavailable! From AECMPS 2.0';
        }
        else
        {
            echo $err;
        }
        exit;
    }
    
    public static function to_301($link)
    {
        if (empty($link))
            $link = self::get_home_url();
        Header("HTTP/1.1 301 Moved Permanently");
        Header("Location: $link");
        exit;
    }

    public static function to_302($link)
    {
        if (empty($link))
            $link = self::get_home_url();
        Header("HTTP/1.1 302 Moved Temporarily");
        Header("Location: $link");
    }
    
    public static function to_404($link)
    {
        if (empty($link))
            $link = self::get_home_url();
        Header("HTTP/1.1 404 Moved Temporarily");
        Header("Location: $link");
    }
}
