<?php

/**
 * 同步主题表过来
 */
ini_set('memory_limit','256M');

include_once __DIR__."./../library/func_common.php";
include_once __DIR__."./../library/Environment.php";
include_once __DIR__."./../library/Singleton.php";
include_once __DIR__."./../library/Otable.php";
include_once __DIR__."./../library/ConfigPg.php";
include_once __DIR__."./../library/PgsqlHelper.php";
include_once __DIR__."./../models/Topic.php";
include_once __DIR__."./../vendor/xunsearch/php/lib/XS.php";

$index_type = "topicsync";
$dbname = Otable::DB_115;
$pagesize = 1000;

$db_old = PgsqlHelper::getInstance();
$servers = ConfigPg::$config['dev'][$dbname];
$db_old ->connect($servers[0],$servers[1],$servers[2],$dbname,$servers[3]);

$db_new = new PgsqlHelper();
$servers = ConfigPg::$config['ol'][$dbname];
$db_new ->connect($servers[0],$servers[1],$servers[2],$dbname,$servers[3]);

// update_time
$sql = 'SELECT * FROM "update_index" WHERE upload_id=? AND "type"=? ;';
$vars = array('ol',$index_type);
$xun_index = $db_new->getOne($sql,$vars);
if (isset($xun_index['update_time'])) {
    $update_time = (int)$xun_index['update_time'];
}else{
    echo 'no update_time';
    exit();
}


$sql = "SELECT  COUNT(*) AS count FROM ".Otable::TABLE_115_TOPIC . ' WHERE update_time>?';
$vars = array($update_time);
$result = $db_old->getOne($sql,$vars);
$total = 0;
if (isset($result['count'])) {
    $total = intval($result['count']);
}
$page_count = ($total<=$pagesize)?1:intval(ceil($total/$pagesize));
// var_dump($total);exit;
// $page_count = 1;
$count = 0;
for ($page=1; $page <= $page_count; $page++) {

    $offset   = ($page-1)*$pagesize;
    $sql = 'SELECT "id","subject","abstract","post_time","deal_content","update_time","tags","status" FROM '.Otable::TABLE_115_TOPIC . ' WHERE update_time>? ORDER BY "update_time" ASC,"id" ASC LIMIT ? OFFSET ? ';
    $vars = array($update_time,$pagesize,$offset);
    $list_arr = $db_old->getAll($sql,$vars);

    if ($list_arr) {
        foreach ($list_arr as $key => $value) {
            $sql_tmp = "SELECT  id FROM ".Otable::TABLE_115_TOPIC . ' WHERE id=?';
            $vars_tmp = array($value['id']);
            $has_id = $db_new->getOne($sql_tmp,$vars_tmp);
            if ($has_id) {
                $conditon = array('id'=>$value['id']);
                $db->update(Otable::TABLE_115_TOPIC,$value,$conditon);
            }else{
                $db_new->insert(Otable::TABLE_115_TOPIC,$value);
            }
            $count++;
        }
    }
}
// 更新索引
if (isset($value['update_time'])&&$value['update_time']) {
    $conditon = array('upload_id'=>CONFIG_ENV,'type'=>$index_type);
    $update_data = array('update_time'=>$value['update_time']);
    $db->update("update_index",$update_data,$conditon);
}

echo "update topic sync delta total:",$count;