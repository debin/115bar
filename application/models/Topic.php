<?php

/**
 * 获取和修改用户相关信息
 *
 */
class TopicModel{


    /**
     * 获取信息
     * @param  string $id mongo id
     */
    public static function getInfoById($id){
        $dbname = Otable::DB_115;
        $db = PgsqlHelper::getInstance();
        $servers = ConfigPg::getDBMaster($dbname);
        $db ->connect($servers[0],$servers[1],$servers[2],$dbname,$servers[3]);
        $sql = 'SELECT "id","subject","abstract","deal_content","post_time","deal_content","tags" FROM '.Otable::TABLE_115_TOPIC." WHERE status=0 AND id=?";
        $vars = array(intval($id));
        $result = $db->getOne($sql,$vars);
        return $result;
    }

    /**
     * 获取上下一篇信息
     * @param  int $id  id
     * @param  string $oper    >上一篇 <下一篇
     */
    public static function getNearInfoById($id,$post_time,$oper="<",$sort="DESC"){
        $dbname = Otable::DB_115;
        $db = PgsqlHelper::getInstance();
        $servers = ConfigPg::getDBMaster($dbname);
        $db ->connect($servers[0],$servers[1],$servers[2],$dbname,$servers[3]);
        $sql = 'SELECT "id","subject" FROM '.Otable::TABLE_115_TOPIC." WHERE status=0 AND post_time {$oper} ? ORDER BY post_time {$sort}";
        $vars = array(intval($post_time));
        $result = $db->getOne($sql,$vars);
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

        $sql = 'SELECT "id","subject","abstract","post_time","deal_content","update_time"  FROM '.Otable::TABLE_115_TOPIC;
        $sql = $sql . " WHERE 1=1 ";
        $vars = array();
        if (isset($data['status'])&&!is_null($data['status'])) {
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

    /**
     * 分页查询用户信息2 之获取部分字段
     * @param [array] $data [description]
     * @author ldb
     * @date(2014-12-15)
     */
    public static function getInfoByPage2(array $data,$page=1,$pagesize=20,$sort = "DESC"){
        $dbname = Otable::DB_115;
        $db = PgsqlHelper::getInstance();
        $servers = ConfigPg::getDBMaster($dbname);
        $db ->connect($servers[0],$servers[1],$servers[2],$dbname,$servers[3]);

        $sql = 'SELECT "id","subject","post_time","update_time"  FROM '.Otable::TABLE_115_TOPIC;
        $sql = $sql . " WHERE 1=1 ";
        $vars = array();
        if (isset($data['status'])&&!is_null($data['status'])) {
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



    /**
     * 获取总数 xun_seach
     * @param [array] $data [description]
     * @author ldb
     * @date(2014-12-15)
     */
    public static function xs_getInfoCount(array $data){
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

        if (isset($data['update_time'])&&!is_null($data['update_time'])) {
            $sql = $sql . " AND \"update_time\" > ? ";
            $vars[] = intval($data['update_time']);
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
     * 分页查询用户信息 xun_seach
     * @param [array] $data [description]
     * @author ldb
     * @date(2014-12-15)
     */
    public static function xs_getInfoByPage(array $data,$page=1,$pagesize=20,$sort = "DESC"){
        $dbname = Otable::DB_115;
        $db = PgsqlHelper::getInstance();
        $servers = ConfigPg::getDBMaster($dbname);
        $db ->connect($servers[0],$servers[1],$servers[2],$dbname,$servers[3]);

        $sql = 'SELECT "id","subject","abstract","post_time","deal_content","update_time","status"  FROM '.Otable::TABLE_115_TOPIC;
        $sql = $sql . " WHERE 1=1 ";
        $vars = array();
        if (isset($data['status'])&&!is_null($data['status'])) {
            $sql = $sql . " AND \"status\" = ? ";
            $vars[] = intval($data['status']);
        }

        if (isset($data['update_time'])&&!is_null($data['update_time'])) {
            $sql = $sql . " AND \"update_time\" > ? ";
            $vars[] = intval($data['update_time']);
        }

        $sql = $sql . " ORDER BY \"update_time\" " . $sort;
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