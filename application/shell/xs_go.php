<?php

ini_set('memory_limit','256M');

include_once __DIR__."./../library/Environment.php";
include_once __DIR__."./../library/Singleton.php";
include_once __DIR__."./../library/Otable.php";
include_once __DIR__."./../library/ConfigPg.php";
include_once __DIR__."./../library/PgsqlHelper.php";
include_once __DIR__."./../models/Topic.php";
include_once __DIR__."./../vendor/xunsearch/php/lib/XS.php";

$xs = new XS('115zone'); // 建立 XS 对象，项目名称为：demo
$index = $xs->index; // 获取 索引对象

// 执行清空操作
$index->clean();

// $page = 1;
$pagesize = 400;
$data = array('status'=>0);
$total = TopicModel::getInfoCount($data);
$page_count = ($total<=$pagesize)?1:intval(ceil($total/$pagesize));

$count = 0;
for ($i=1; $i <= $page_count; $i++) { 
    $list_arr = TopicModel::getInfoByPage($data,$i,$pagesize);
    $index->openBuffer(8); // 开启缓冲区，默认 4MB，如 $index->openBuffer(8) 则表示 8MB
    if ($list_arr) {
        foreach ($list_arr as $key => $value) {
            $data = array(
                'id'           => $value['id'], // 此字段为主键，必须指定
                'subject'      => $value['subject'],
                'deal_content' => strip_tags($value['deal_content']),
                'post_time'    => $value['post_time'],
            );
            // 创建文档对象
            $doc = new XSDocument;
            $doc->setFields($data);
            // 添加到索引数据库中
            $res = $index->add($doc);//add update
            $count++;
        }
    }
    $index->closeBuffer(); // 关闭缓冲区，必须和 openBuffer 成对使用
}

echo "over:",$count;




