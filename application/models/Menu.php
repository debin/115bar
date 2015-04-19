<?php

/**
 * 菜单相关
 *
 */
class MenuModel{

    private static $__actionConfig;//actionConfig内容

    /**
     * 获取菜单
     * @param array $data [description]
     * @author ldb
     * @date(2014-12-15)
     */
    public static function getMenu(){
        $user_id = Yaf_Session::getInstance()->user;
        $user = UserModel::getUserById($user_id,array('privilege'),1);

        $privilege_id = isset($user['privilege'])?$user['privilege']:'';
        $privilege_info = User_GroupModel::getInfoById($privilege_id,array());
        $privilege_list = isset($privilege_info['privilege_list'])?$privilege_info['privilege_list']:array();

        $list = array();
        $config_list = require ROOT."/conf/actionConfig.php";

        foreach ($config_list as $key=>$config) {
            foreach ($config as $k=>$val) {
                if (in_array($k,$privilege_list) && $val['is_show']==1) {
                   $list[$key][$k] = $val;
                }
            }
        }
        return $list;
    }

    /**
     * 获取当前用户拥有的权限子项
     * @param array $data [description]
     * @return array 权限列表
     * @author ldb
     * @date(2014-12-15)
     */
    public static function getItem(){
        $user_id = Yaf_Session::getInstance()->user;
        $user = User_UserModel::getUserById($user_id,array('privilege'));

        $privilege_id = isset($user['privilege'])?$user['privilege']:'';
        $privilege_info = User_GroupModel::getInfoById($privilege_id,array());
        $privilege_list = isset($privilege_info['privilege_list'])?$privilege_info['privilege_list']:array();
        return $privilege_list;
    }

    /**
     * 获取当前用户不拥有的权限子项
     * @param array $data [description]
     * @author ldb
     * @date(2014-12-15)
     */
    public static function getNotItem(){
        $privilege_list = MenuModel::getItem();

        $config_list = self::getActionConfig();

        $list = array();
        foreach ($config_list as $config) {
            foreach ($config as $key => $value) {
                if (!in_array($key,$privilege_list)) {
                    array_push($list, $key);
                }
            }
        }
        return $list;
    }

    /**
     * 获取actionConfig 文件的配置项
     * @return array actionConfig
     * @author ldb
     * @date(2014-12-15)
     */
    public static function getActionConfig(){
        if (!empty(self::$__actionConfig)) {
            // none
        }else{
            $config_list = require ROOT."/conf/actionConfig.php";
            self::$__actionConfig = $config_list;
        }
        return self::$__actionConfig;
    }

    /**
     * 格式化 actionConfig 文件
     * @return array actionConfig
     * @author ldb
     * @date(2014-12-19)
     */
    public static function getActionConfigFormat(){
        $privilege_list = MenuModel::getActionConfig();

        $template = array();
        foreach ($privilege_list as $key => $privilege) {
            $list = array();
            foreach ($privilege as $k => $value) {
                $list[$k] = _($value['lang']);
            }
            $template[$key] = array(
                'type'=>_($key),
                'list'=>$list,
                );
        }
        return $template;
    }
}