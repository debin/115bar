<?php
/**
 * Redis连接配置
 * @package mxcommon_config
 */
class ConfigRedis {

    /**
     * dbmaster Redis
     * @var array
     */
    private static $dbmaster = array(
        // DEV
        'dev' => array("203.195.196.161", 6379),
        // ONLINE
        'ol' => array("192.168.189.6", 6379),
    );

    /**
     * get Percona master
     * @return [type] [description]
     */
    public static function getDBMaster() {
        return self::$dbmaster[CONFIG_ENV];
    }

}