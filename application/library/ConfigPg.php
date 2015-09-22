<?php

/**
 * postgres 配置
 * @author ldb
 * @package lirbray
 */
class ConfigPg
{
    public static $config = array(
        'dev' => array(
            "dht" => array('127.0.0.1','blue','blue',3306),
            "115" => array('127.0.0.1','blue','blue',3306),
            ),
        'ol' => array(
            "dht" => array('127.0.0.1','blue','blue',3306),
            "115" => array('192.168.189.6','blue','blue',3306),
            ),
    );

    public static function getDBMaster($db)
    {
        return self::$config[CONFIG_ENV][$db];
    }
}
