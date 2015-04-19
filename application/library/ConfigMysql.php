<?php

/**
 * mysql 配置
 * @author shenghao
 * @package config
 * @version  1.0.1
 */
class ConfigMysql{
    private static $config = array(
        'dev' => array('172.24.0.76','root','yxhkdata2012',3306),
        'ol'  => array('127.0.0.1','lamin','yxhkdata2012',3306),
    );

    public static function getDBMaster(){
        return self::$config[CONFIG_ENV];
    }
}