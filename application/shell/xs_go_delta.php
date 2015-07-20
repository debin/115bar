<?php

/**
 * 增量索引
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

$search_type = "xunsearch";
$dbname = Otable::DB_115;
$db = PgsqlHelper::getInstance();
$servers = ConfigPg::getDBMaster($dbname);
$db->connect($servers[0], $servers[1], $servers[2], $dbname, $servers[3]);
$sql = 'SELECT * FROM "update_index" WHERE upload_id=? AND "type"=? ;';
$vars = array(CONFIG_ENV,$search_type);
$xun_index = $db->getOne($sql, $vars);
if (isset($xun_index['update_time'])) {
    $update_time = (int)$xun_index['update_time'];
} else {
    echo 'no update_time';
    exit();
}

$xs = new XS('115zone'); // 建立 XS 对象，项目名称为：demo
$index = $xs->index; // 获取 索引对象

// $page = 1;
$pagesize = 1000;
$data = array('update_time' => $update_time);
$total = TopicModel::xs_getInfoCount($data);
$page_count = ($total<=$pagesize)?1:intval(ceil($total/$pagesize));
$count = 0;
for ($i=1; $i <= $page_count; $i++) {
    $list_arr = TopicModel::xs_getInfoByPage($data, $i, $pagesize, "ASC");
    $index->openBuffer(8); // 开启缓冲区，默认 4MB，如 $index->openBuffer(8) 则表示 8MB
    if ($list_arr) {
        foreach ($list_arr as $key => $value) {
            if (isset($value['status']) && $value['status']==0) {
                $subject = $value['subject'];
                $deal_content = strip_tags($value['deal_content']);
                $subject = trim_string($subject);
                $deal_content = trim_string($deal_content);
                $data_tmp = array(
                    'id'           => $value['id'], // 此字段为主键，必须指定
                    'subject'      => $subject,
                    'deal_content' => $deal_content,
                    'post_time'    => $value['post_time'],
                );
                // 创建文档对象
                $doc = new XSDocument;
                $doc->setFields($data_tmp);
                // 添加到索引数据库中
                $res = $index->update($doc);//add update
            }else{
                $index->del($value['id']);
            }
            $count++;
        }
    }
    $index->closeBuffer(); // 关闭缓冲区，必须和 openBuffer 成对使用
}

// 更新索引
if (isset($value['update_time']) && $value['update_time']) {
    $conditon = array('upload_id' => CONFIG_ENV,'type' => $search_type);
    $update_data = array('update_time' => $value['update_time']);
    $db->update("update_index", $update_data, $conditon);
}


echo "xsgodelta update xs delta:",$count;




