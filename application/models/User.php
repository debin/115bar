<?php

/**
 * 获取和修改用户相关信息
 *
 */
class UserModel{
    /**
     * 写入用户
     * @param array $data [description]
     * @author ldb
     * @date(2014-12-15)
     */
    public static function add(array $data){
        $mysql = MysqlHelper::getInstance();
        $row = $mysql->insert(Otable::DB_LAMIN,Otable::TABLE_LAMIN_USER,$data);
        return $row;
    }

    /**
     * 编辑用户
     * @param  string $id mongo_id
     * @param  array  $data     修改的数据
     * @author ldb
     * @date(2014-12-15)
     */
    public static function edit($id,array $data){
        $mysql = MysqlHelper::getInstance();
        $condition = array('id'=>$id);
        $row = $mysql->update(Otable::DB_LAMIN,Otable::TABLE_LAMIN_USER,$data,$condition);
        return $row;
    }

    /**
     * 获取用户信息
     * @param  string $username 用户名
     * @param  array  $data     修改的数据
     * @param  int  $valid     有效状态
     * @author ldb
     * @date(2014-12-15)
     */
    public static function getUserByUsername($username){
        $mysql = MysqlHelper::getInstance();
        $sql = 'SELECT `id`,`username`,`password`,`encrypt`,`valid`,`create_user_id`,`create_time`,`update_user_id`,`update_time` FROM '.Otable::DB_LAMIN.".".Otable::TABLE_LAMIN_USER." WHERE `valid`=1 AND `username`='$username'";
        $result = $mysql->getOne($sql);
        return $result;
    }

    /**
     * 获取用户信息
     * @param  string $id mongo id
     */
    public static function getUserById($id){
        $mysql = MysqlHelper::getInstance();
        $sql = 'SELECT `id`,`username`,`password`,`encrypt`,`valid`,`create_user_id`,`create_time`,`update_user_id`,`update_time` FROM '.Otable::DB_LAMIN.".".Otable::TABLE_LAMIN_USER." WHERE `valid`=1 AND id=$id";
        $result = $mysql->getOne($sql);
        return $result;
    }


    /**
     * 获取用户总数
     * @param [array] $data [description]
     * @author ldb
     * @date(2014-12-15)
     */
    public static function GetInfoCount(array $data){
        $mysql = MysqlHelper::getInstance();

        $sql = "SELECT  COUNT(*) AS `count` FROM ".Otable::DB_LAMIN.".".Otable::TABLE_LAMIN_USER;
        $sql = $sql . " WHERE 1=1 ";

        if (isset($data['valid'])&&!is_null($data['valid'])) {
            $sql = $sql . " AND `valid`=" . intval($data['valid']);
        }
        $result = $mysql->getOne($sql);
        $count = 0;
        if (isset($result['count'])) {
            $count = intval($result['count']);
        }
        return $count;
    }

    /**
     * 分页查询用户信息
     * @param [array] $data [description]
     * @author ldb
     * @date(2014-12-15)
     */
    public static function GetInfoByPage(array $data,$page=1,$pagesize=20,$sort = "DESC"){
        $mysql = MysqlHelper::getInstance();

        $sql = "SELECT `id`,`username`,`password`,`encrypt`,`valid`,`create_user_id`,`create_time`,`update_user_id`,`update_time`  FROM ".Otable::DB_LAMIN.".".Otable::TABLE_LAMIN_USER;
        $sql = $sql . " WHERE 1=1 ";

        if (isset($data['valid'])&&!is_null($data['valid'])) {
            $sql = $sql . " AND `valid`=" . intval($data['valid']);
        }

        $sql = $sql . " ORDER BY `create_time` " . $sort;
        if ($page && $pagesize) {
            $offset   = ($page-1)*$pagesize;
            $sql = $sql ." LIMIT $offset,$pagesize";
        }
        $result = $mysql->getAll($sql);
        return $result;
    }

    /**
     * 密码的加密函数
     * @param string $salt 密码的盐
     * @param string $password md5之后的密码
     * @author ldb
     * @date(2014-12-19)
     */
    public static function encryptPsw($salt,$password){
        $pass = md5($salt.$password);
        return $pass;
    }

    /**
     * 创建数据库
     */
    public static function create(){
        $mysql = MysqlHelper::getInstance();
        $sql = 'create database IF NOT EXISTS '.Otable::DB_LAMIN;
        $sql_1 = 'create table IF NOT EXISTS '.Otable::DB_LAMIN.'.'.Otable::TABLE_LAMIN_LIST.'('
                .'id int(4) not null auto_increment primary key,'
                .' valid int(1) not null,'
                .' project_id int(4) not null,'
                .' project_list_id int(4) not null,'
                .' search_index varchar(10000),'
                .' list varchar(10000),'
                .' create_user_id int(4),'
                .' create_time int(10),'
                .' update_user_id int(4),'
                .' update_time int(10),'
                .' la_key varchar(9),'
                .' la_id int(4) not null'
                .')';
        $sql_2 = 'create table IF NOT EXISTS '.Otable::DB_LAMIN.'.'.Otable::TABLE_LAMIN_PROJECT.'('
                .'id int(4) not null auto_increment primary key,'
                .' valid int(1) not null,'
                .' project_name varchar(20) not null,'
                .' create_user_id int(4),'
                .' create_time int(10),'
                .' update_user_id int(4),'
                .' update_time int(10)'
                .')';
        $sql_3 = 'create table IF NOT EXISTS '.Otable::DB_LAMIN.'.'.Otable::TABLE_LAMIN_PROJECT_LIST.'('
                .'id int(4) not null auto_increment primary key,'
                .' valid int(1) not null,'
                .' project_id int(4) not null,'
                .' project_list_id varchar(20) not null,'
                .' project_list_name varchar(20) not null,'
                .' create_user_id int(4),'
                .' create_time int(10),'
                .' update_user_id int(4),'
                .' update_time int(10)'
                .')';
        $sql_4 = 'create table IF NOT EXISTS '.Otable::DB_LAMIN.'.'.Otable::TABLE_LAMIN_TYPE.'('
                .'id int(4) not null auto_increment primary key,'
                .' valid int(1) not null,'
                .' lamin_name varchar(20) not null,'
                .' create_user_id int(4),'
                .' create_time int(10),'
                .' update_user_id int(4),'
                .' update_time int(10)'
                .')';
        $sql_5 = 'create table IF NOT EXISTS '.Otable::DB_LAMIN.'.'.Otable::TABLE_LAMIN_USER.'('
                .'id int(4) not null auto_increment primary key,'
                .' username varchar(20) not null,'
                .' password varchar(50) not null,'
                .' encrypt varchar(6) not null,'
                .' valid int(1) not null,'
                .' create_user_id int(4),'
                .' create_time int(10),'
                .' update_user_id int(4),'
                .' update_time int(10)'
                .')';
        $mysql->execute($sql);
        $mysql->execute($sql_1);
        $mysql->execute($sql_2);
        $mysql->execute($sql_3);
        $mysql->execute($sql_4);
        $mysql->execute($sql_5);
        return true;
    }
}