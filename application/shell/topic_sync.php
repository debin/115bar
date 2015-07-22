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

$index_type = "topicsync";
$dbname = Otable::DB_115;
$pagesize = 1000;

$db_old = PgsqlHelper::getInstance();
$servers = ConfigPg::$config['dev'][$dbname];
$db_old ->connect($servers[0], $servers[1], $servers[2], $dbname, $servers[3]);

$db_new = new PgsqlHelper();
$servers = ConfigPg::$config['ol'][$dbname];
$db_new ->connect($servers[0], $servers[1], $servers[2], $dbname, $servers[3]);

$sql = "SELECT  COUNT(*) AS count FROM ".Otable::TABLE_115_TOPIC;
$vars = array();
$result = $db_old->getOne($sql, $vars);
$total = 0;
if (isset($result['count'])) {
    $total = intval($result['count']);
}
$page_count = ($total<=$pagesize)?1:intval(ceil($total/$pagesize));

// $page_count = 1;
$count = 0;
for ($page = 1; $page <= $page_count; $page++) {

    $offset   = ($page-1)*$pagesize;
    $sql = 'SELECT "id","subject","abstract","post_time","deal_content","update_time","tags","status" FROM '.Otable::TABLE_115_TOPIC . ' ORDER BY "update_time" ASC,"id" ASC LIMIT ? OFFSET ? ';
    $vars = array($pagesize,$offset);
    $list_arr = $db_old->getAll($sql, $vars);
    if ($list_arr) {
        foreach ($list_arr as $key => $value) {
            $db_new->insert(Otable::TABLE_115_TOPIC,$value);
            $count++;
        }
    }
}
// 更新索引
$sql = 'SELECT * FROM "update_index" WHERE upload_id=? AND "type"=? ;';
$vars = array(CONFIG_ENV, $index_type);
$xun_index = $db_new->getOne($sql, $vars);
if ($xun_index) {
    $conditon = array('upload_id' => CONFIG_ENV, 'type'=> $index_type);
    $update_data = array('update_time' => $update_time);
    $db_new->update("update_index", $update_data, $conditon);
} else {
    $insert_data = array(
        'upload_id'   => CONFIG_ENV,
        'type'        => $index_type,
        'create_time' => $update_time,
        'update_time' => $update_time,
        );
    $db_new->insert("update_index", $insert_data);
}

echo "update topic sync  total:",$count;
