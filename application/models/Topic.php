<?php

/**
 * 获取和修改用户相关信息
 *
 */
class TopicModel{
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
    public static function getInfoById($id){
        $mysql = MysqlHelper::getInstance();
        $sql = 'SELECT `id`,`username`,`password`,`encrypt`,`valid`,`create_user_id`,`create_time`,`update_user_id`,`update_time` FROM '.Otable::DB_LAMIN.".".Otable::TABLE_LAMIN_USER." WHERE `valid`=1 AND id=$id";
        $result = $mysql->getOne($sql);
        return $result;
    }


    /**
     * 获取总数
     * @param [array] $data [description]
     * @author ldb
     * @date(2014-12-15)
     */
    public static function getInfoCount(array $data){
        $dbname = Otable::DB_115;
        $db = PgsqlHelper::getInstance();
        $servers = ConfigPg::getDBMaster($dbname);
        $db ->connect($servers[0],$servers[1],$servers[2],$dbname,$servers[3]);


        $sql = "SELECT  COUNT(*) AS count FROM ".Otable::TABLE_115_TOPIC;
        $sql = $sql . " WHERE 1=1 ";

        $vars = array();
        if (isset($data['status'])&&!is_null($data['status'])) {
            $sql = $sql . " AND \"status\" = ? ";
            $vars[] = intval($data['status']);
        }
        $sql = $sql . " LIMIT 1;";
        $result = $db->getOne($sql,$vars);
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
    public static function getInfoByPage(array $data,$page=1,$pagesize=20,$sort = "DESC"){
        $dbname = Otable::DB_115;
        $db = PgsqlHelper::getInstance();
        $servers = ConfigPg::getDBMaster($dbname);
        $db ->connect($servers[0],$servers[1],$servers[2],$dbname,$servers[3]);

        $sql = 'SELECT "id","subject","abstract","image_thumbs","post_time"  FROM '.Otable::TABLE_115_TOPIC;
        $sql = $sql . " WHERE 1=1 ";
        $vars = array();
        if (isset($data['valid'])&&!is_null($data['valid'])) {
            $sql = $sql . " AND \"status\" = ? ";
            $vars[] = intval($data['status']);
        }

        $sql = $sql . " ORDER BY \"post_time\" " . $sort;
        if ($page && $pagesize) {
            $offset   = ($page-1)*$pagesize;
            $sql = $sql ." LIMIT ? OFFSET ?";
            $vars[] = $pagesize;
            $vars[] = $offset;
        }
        $result = $db->getAll($sql,$vars);
        return $result;
    }
}