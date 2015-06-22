<?php

/**
 * redis 操作类
 * call mechon:RedisHelper->getInstance()
 * @author shh
 * @package mxcommon
 */

/**
 * redis 操作类
 * @author shh
 * @package mxcommon_lib
 */
class RedisHelper extends Singleton {

    /**
     * 主机地址
     * @var string
     */
    private $db_host;

    /**
     * 端口
     * @var string
     */
    private $port;

    /**
     * redis对象
     * @var string
     */
    private $redis;

    /**
     * 构造
     */
    function __construct() {
        if(!class_exists('ConfigRedis')){
            throw new Exception("Can not find class ConfigRedis.");
        }
        if(!class_exists('Redis')){
            throw new Exception("Can not find class Redis.");
        }
    }

    /**
     * init
     */
    public function init() {
        if (!$this->redis) {
            $servers = ConfigRedis::getDBMaster();
            $this->db_host = $servers[0];
            $this->port = $servers[1];
            try {
                $status = $this->redis->pconnect($this->db_host, $this->port,$timeout=2.5);
                if (!$status) {
                    //记录 连不上redis
                }
            } catch (Exception $e) {
                throw new Exception($e);

            }
        }
    }
    /**
     * 返回redis对象
     * redis有非常多的操作方法，我们只封装了一部分
     * 拿着这个对象就可以直接调用redis自身方法
     */
    public function redis() {
        return $this->redis;
    }

    /**
     * 设置值
     * @param string $key KEY名称
     * @param string|array $value 获取得到的数据
     * @param int $timeOut 时间
     */
    public function set($key, $value, $timeOut = 0) {
        $value = json_encode($value, TRUE);
        $retRes = $this->redis->set($key, $value);
        if ($timeOut > 0) $this->redis->setTimeout($key, $timeOut);
        return $retRes;
    }

    /**
     * 通过KEY获取数据
     * @param string $key KEY名称
     */
    public function get($key) {
        $result = $this->redis->get($key);
        return json_decode($result, TRUE);
    }

    /**
     * 删除一条数据
     * @param string $key KEY名称
     */
    public function delete($key) {
        return $this->redis->delete($key);
    }


    public function sAdd($key, $value, $timeOut = 0) {
        $value = json_encode($value, TRUE);
        $retRes = $this->redis->sAdd($key, $value);
        if ($timeOut > 0) $this->redis->setTimeout($key, $timeOut);
        return $retRes;
    }
    public function hSet($key, $field, $value, $timeOut = 0) {
        $value = json_encode($value, TRUE);
        $retRes = $this->redis->hSet($key, $field, $value);
        if ($timeOut > 0) $this->redis->setTimeout($key, $timeOut);
        return $retRes;
    }

    public function hGet($key, $value) {
        $result = $this->redis->hGet($key, $value);
        return json_decode($result, TRUE);
    }
    public function hGetAll($key) {
        $result = $this->redis->hGetAll($key);
        return $result;
    }

    public function hDel($key, $value) {
        return $this->redis->hDel($key, $value);
    }

    public function sMembers($key) {
        $retRes = $this->redis->sMembers($key);
        return $retRes;
    }

    public function zAdd($key, $field, $value, $timeOut = 0) {
        $value = json_encode($value, TRUE);
        $retRes = $this->redis->zAdd($key, $field, $value);
        if ($timeOut > 0) $this->redis->setTimeout($key, $timeOut);
        return $retRes;
    }
    public function zCount($key, $str, $end) {
        $retRes = $this->redis->zCount($key, $str, $end);
        return $retRes;
    }
    public function zRevRange($key, $str, $end) {
        $retRes = $this->redis->zRevRange($key, $str, $end);
        return $retRes;
    }
    public function zDelete($key, $value) {
        return $this->redis->zDelete($key, $value);
    }
    public function sRem($key, $value) {
        return $this->redis->sRem($key, $value);
    }

    /**
     * 清空数据
     */
    //public function flushAll() {
    //    return $this->redis->flushAll();
    //}

    /**
     * 数据入队列
     * @param string $key KEY名称
     * @param string|array $value 获取得到的数据
     * @param bool $right 是否从右边开始入
     */
    public function push($key, $value ,$right = true) {
        $value = json_encode($value);
        return $right ? $this->redis->rPush($key, $value) : $this->redis->lPush($key, $value);
    }

    /**
     * 数据出队列
     * @param string $key KEY名称
     * @param bool $left 是否从左边开始出数据
     */
    public function pop($key , $left = true) {
        $val = $left ? $this->redis->lPop($key) : $this->redis->rPop($key);
        return json_decode($val);
    }

    /**
     * 数据自增
     * @param string $key KEY名称
     * @param int value 自增长度
     */
    public function increment($key, $value = 1) {
        return $this->redis->incr($key,$value);
    }

    /**
     * 数据自减
     * @param string $key KEY名称
     * @param int value 自减长度
     */
    public function decrement($key, $value = 1) {
        return $this->redis->decr($key,$value);
    }

    /**
     * key是否存在，存在返回ture
     * @param string $key KEY名称
     */
    public function exists($key) {
        return $this->redis->exists($key);
    }

    //id生成策略函数
    public function getPk($redis, $db, $step, $fp) {
        if($pk = $redis -> lPop('lq:es:store_goods'))  // lq:es:store_goods redis List key
        {
            return $pk;
        }else{
            $sql =       "UPDATE eben_sequence SET sequence=LAST_INSERT_ID(sequence + {$step}) WHERE tablename='eben_goods'";
            $flag           =       $db             ->      query($sql);
            if($flag)
            {
                $sql            =       "SELECT LAST_INSERT_ID() AS lastpk";
                $result         =       $db             ->      query($sql);
                $line           =       $db     ->      fetch_array($result);
                $sequence       =       $line['lastpk'];
                //fwrite($fp, "Push--->\n");
                $str    =       '';
                $redis  ->      multi();
                for ($i = $step; $i> 0; $i--)
                {
                        //var_dump($sequence - $i + 1);
                        $redis  ->      rPush('lq:es:store_goods', $sequence - $i + 1 ); //从已申请的第一个开始进入队列 200 101-200
                        //$str  .=      $sequence - $i + 1 .",";
                }
                $redis  ->      exec();
                $pk     =       getPk($redis, $db, $step, $fp);
                return $pk;
            } else
            {
                fwrite($fp, "error\n");
                return false;
            }
        }
    }

}