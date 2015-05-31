<?php
/**
 * 获取tag
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

$max_num = 5;//tag个数

$xs = new XS('115zone'); // 建立 XS 对象，项目名称为：demo
// $index = $xs->index; // 获取 索引对象
$tokenizer = new XSTokenizerScws;   // 直接创建实例

$dbname = Otable::DB_115;
$db = PgsqlHelper::getInstance();
$servers = ConfigPg::getDBMaster($dbname);
$db ->connect($servers[0],$servers[1],$servers[2],$dbname,$servers[3]);


$sql = 'SELECT "id","subject","abstract","deal_content","image_thumbs","post_time","deal_content" FROM '.Otable::TABLE_115_TOPIC." WHERE need_format!=0 AND tags IS null  LIMIT 2000";

$sql_tmp = 'UPDATE ' . Otable::TABLE_115_TOPIC .' SET tags=? WHERE id=?;';

$count = 0;
$list_arr = $db->getAll($sql,array());
while ( $list_arr ) {
    foreach ($list_arr as $key => $value) {
        $id = $value['id'];
        $deal_content = strip_tags($value['deal_content']);
        $deal_content = trim_string($deal_content);
        try {
            $tops = $tokenizer->getTops($deal_content, $max_num, 'n,v,vn,nr,ns,nt,nz,nz,s,l,i');//http://www.xunsearch.com/scws/docs.php#attr
            // 过滤
            foreach ($tops as $k => $v){
                if (strpos($v['word'], '礼包')!==false) {
                    unset($tops[$k]);
                }elseif (strpos($v['word'], 'baidu')!==false) {
                    unset($tops[$k]);
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            $tops = array();
        }


        $num = count($tops);
        if ($num<$max_num) {
            try {
                $tops_en = $tokenizer->getTops($deal_content, $max_num-$num, 'en');
                // 过滤
                foreach ($tops_en as $k => $v) {
                    if (strpos($v['word'], 'lb')!==false) {
                        unset($tops_en[$k]);
                    }elseif (strpos($v['word'], 'baidu')!==false) {
                        unset($tops_en[$k]);
                    }elseif (strpos($v['word'], 'http')!==false) {
                        unset($tops_en[$k]);
                    }elseif (strpos($v['word'], 'com')!==false) {
                        unset($tops_en[$k]);
                    }elseif (strpos($v['word'], 'www')!==false) {
                        unset($tops_en[$k]);
                    }elseif (strpos($v['word'], 'baidu')!==false) {
                        unset($tops_en[$k]);
                    }elseif (strpos($v['word'], 'baidu')!==false) {
                        unset($tops_en[$k]);
                    }elseif (strpos($v['word'], '115')!==false) {
                        unset($tops_en[$k]);
                    }
                }
            } catch (Exception $e) {
                $tops_en = array();
            }

            $tops = array_merge($tops,$tops_en);
        }

        if (empty($tops)) {
            $vars = array('{}',$id);
        }else{
            $temp = array();
            foreach ( $tops as  $v ) {
                array_push($temp,$v['word']);
            }
            $temp_str = '{'. implode(',', $temp) .'}';
            $vars = array($temp_str,$id);
        }
        $db->query($sql_tmp,$vars);
        $count++;
    }
    $list_arr = $db->getAll($sql,array());
}


echo "over:",$count;