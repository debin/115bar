<?php
/**
 * mysql 操作类
 * @author liu.xiaoyi
 * @package mxcommon
 */

class MysqlHelper extends Singleton {

    /**
     * 主机地址
     * @var string
     */
    private $__db_host;

    /**
     * 用户名
     * @var string
     */
    private $__db_user;

    /**
     * 密码
     * @var string
     */
    private $__db_pass;

    /**
     * 端口
     * @var string
     */
    private $__db_port;

    /**
     * 编码
     * @var string
     */
    private $__chartset = 'UTF8';

    /**
     * mysql connection 实例
     * @var object
     */
    private $__connection = null;

    /**
     * mysql 最后连接时间，对于长连接需要考虑超时的问题
     * @var int
     */
    private $__max_connect_time = 60;

    private $__is_trans = false;

    /**
     * 初始化
     */
    function init() {
        $this->initConnection();
    }


    /**
        * @brief initConnection
        * @param array $servers mysql config
        * 连接mysql
        * @return
     */
    public function initConnection(array $servers=array()) {
        if (empty($servers)) {
            $servers = ConfigMysql::getDBMaster();
        }

        // 是否已经连接
        if ($this->__db_host == $servers[0] &
            $this->__db_user == $servers[1] &
            $this->__db_pass == $servers[2] &
            $this->__db_port == $servers[3]
            ) {
            return true;
        }

        // 重新连接
        $this->__db_host = $servers[0];
        $this->__db_user = $servers[1];
        $this->__db_pass = $servers[2];
        $this->__db_port = $servers[3];

        $option = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "'.$this->__chartset.'"',
            PDO::ATTR_TIMEOUT            => true,
            PDO::CASE_LOWER              => true,
        );

        $dns = "mysql:host=".$this->__db_host.";port=".$this->__db_port;

        try{

            $this->__connection = new PDO($dns, $this->__db_user, $this->__db_pass, $option);
            //设置错误模式为异常捕获
            $this->__connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        }catch(PDOException $e){
            //捕获连接异常并监控，之后通知调用方无法实用mysql
            throw new Exception($e->getMessage(),$e->getCode());
            // return false;
        }
    }

    /**
        * @brief useDB
        *
        * @return
     */
    public function useDB($database){

        $sql = 'USE '.$database;
        //not Exception ???
        $res = $this->execute($sql);
        if($res === false){
            $create_res = $this->execute('CREATE DATABASE '.$database);
            if($create_res === false){
                throw new Exception($this->getError());
            }
            $res = $this->execute($sql);
        }

        return true;
    }

    /**
     * 执行增、删、改操作
     * @param string $sql
     * @return int
     */
    public function query($sql) {
        $rows = 0;
        try {
            $PDOStatement = $this->__connection->prepare($sql);
            $res          = $PDOStatement->execute();
            $rows         = $PDOStatement->rowCount();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        return $rows;
    }


    /**
     * 获取单条记录
     * @param string $sql
     * @param int $mode
     * @return multitype:|boolean
     */
    public function getOne($sql, $mode =PDO::FETCH_ASSOC) {
        if (!preg_match("/LIMIT/i", $sql)) {
            $sql = preg_replace("/[,;]$/i", '', trim($sql)) . " LIMIT 1 ";
        }
        try{
            $PDOStatement = $this->__connection->prepare($sql);
            $rows         = $PDOStatement->execute();
            $res          = $PDOStatement->fetch(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            throw new Exception($e->getMessage());
        }
        return $res;
    }


    /**
     * 查询整个表的数据，返回结果数组
     * @param string $sql
     * @param int $mode
     * @return multitype:|boolean
     */
    public function getAll($sql, $mode = PDO::FETCH_ASSOC) {
        try{
            $query = $this->__connection->query($sql);
            $query->setFetchMode($mode);    //设置结果集返回格式,此处为关联数组,即不包含index下标
            $res   = $query->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            throw new Exception($e->getMessage());
        }
        return $res;
    }

    /**
     * Insert data into database table
     *
     * Example usage:
     * $user_data = array(
     *      'name' => 'Bennett',
     *      'email' => 'email@address.com',
     *      'active' => 1
     * );
     * $database->insert( 'users_table', $user_data );
     *
     * @access public
     * @param string table name
     * @param array table column => column value
     * @return bool
     *
     */

    /**
     * Insert data into database table
     *
     * Example usage:
     * $user_data = array(
     *      'name' => 'Bennett',
     *      'email' => 'email@address.com',
     *      'active' => 1
     * );
     * $database->insert( 'users_table', $user_data );
     *
     * @param  string $database
     * @param  string $table
     * @param  array  $data     插入数据
     * @return int
     * @author ldb
     */
    public function insert( $database,$table,array $data )
    {
        //Make sure the array isn't empty
        if( empty( $data ) )
        {
            // $this->error = 'the variable' . $data. ' is empty';
            throw new Exception("insert data is empty");
        }

        $sql = "INSERT INTO ". $database . "." .$table;
        $fields = array();
        $values = array();
        foreach( $data as $field => $value )
        {
            $fields[] = $field;
            $values[] = "'".$value."'";
        }
        $fields = ' (' . implode(', ', $fields) . ')';
        $values = '('. implode(', ', $values) .')';

        $sql .= $fields .' VALUES '. $values;

        return $this->query( $sql );
    }

    /**
     * Update data in database table
     *
     * Example usage:
     * $update = array( 'name' => 'Not bennett', 'email' => 'someotheremail@email.com' );
     * $update_where = array( 'user_id' => 44, 'name' => 'Bennett' );
     * $database->update( 'database','users_table', $update, $update_where, 1 );
     *
     * @param  string  $database
     * @param  string  $table
     * @param  array   $set_data 更新数据
     * @param  array   $where    条件
     * @param  integer $limit    限制行数
     * @return int            [description]
     * @author ldb
     */
    public function update( $database,$table,array $set_data, array $where = array(), $limit = 0 )
    {
        if( empty( $set_data ) )
        {
            throw new Exception('the variable $set_data is empty');
        }

        $sql = "UPDATE ". $database . "." . $table ." SET ";
        foreach( $set_data as $field => $value )
        {

            $updates[] = "`$field` = '$value'";
        }
        $sql .= implode(', ', $updates);

        //Add the $where clauses as needed
        if( !empty( $where ) )
        {
            foreach( $where as $field => $value )
            {
                // $value = $value;
                $clause[] = "$field = '$value'";
            }
            $sql .= ' WHERE '. implode(' AND ', $clause);
        }

        // 更新行数
        if( !empty( $limit ) )
        {
            $sql .= ' LIMIT '. $limit;
        }
        return $this->query( $sql );
    }


    /**
        * @brief execute
        * 执行操作
        * @param $sql
        * @param $is_trans 是否支持事务
        *
        * @return
     */
    public function execute($sql){


        try{
            $res = $this->__connection->exec($sql);
        }catch(PDOException $e){
            throw new Exception($e->getMessage());
        }
        return $res;
    }

    public function begin(){
        $this->is_trans = $this->__connection->isTransaction();
        $res = false;
        if($this->is_trans){
            $res = $this->__connection->beginTransaction();
        }
        return $res;
    }

    public function commit(){
        if($this->is_trans){
            $res = $this->__connection->commit();
        }

    }

    public function rollBack(){
        if($this->is_trans){
            $res = $this->__connection->rollBack();
        }

    }


    /**
     * 安全性检测.调用escape存入的,一定要调unescape取出
     * @param string $string
     */
    public function escape($string) {
        return mysql_real_escape_string(trim($string));
    }

    /**
     * unescape
     * @param string $string
     */
    public function unescape($string) {
        return stripslashes($string);
    }

    public function link(){
        return $this->__connection;
    }


    /**
        * @brief getError
        * 获取pdo错误信息
        * @return array
     */
    public function getError(){
        return $this->__connection->errorInfo();
    }


    /**
        * @brief getErrorCode
        * 获取错误码
        * @return
     */
    public function getErrorCode(){
        return $this->__connection->errorCode();
    }


    /**
        * @brief getLastId
        * 获取最后插入的主键id
        * @return
     */
    public function getLastId(){
        return $this->__connection->lastInsertId();
    }

}