<?php

Yaf\Loader::import(ROOT.'/vendor/autoload.php');

use \XS;

define ('XS_APP_ROOT', ROOT.'/conf');

/**
 * 搜索相关
 *
 */
class SearchModel
{

    /**
     * 获取圈子贴
     * @param [array] $data [description]
     * @author ldb
     * @date(2015-04-25)
     */
    public static function getZoneInfo($keyword, $page, $pagesize)
    {
        $offset   = ($page-1)*$pagesize;
        $index_app = '115zone';

        $xs = new XS($index_app); // 建立 XS 对象，项目名称为：demo
        $search = $xs->search; // 获取 搜索对象
        $query = trim($keyword); // 这里的搜索语句很简单，就一个短语

        $search->setQuery($query); // 设置搜索语句
        // $search->setCollapse('id',3);//按字段值折叠搜索结果
        // $search->addWeight('subject', 'xunsearch'); // 增加附加条件：提升标题中包含 'xunsearch' 的记录的权重
        $search->setLimit($pagesize, $offset); // 设置返回结果最多为 $pagesize 条，并跳过前 $offset 条
        // $search->setFuzzy();
        $docs = $search->search(); // 执行搜索，将搜索结果文档保存在 $docs 数组中
        $count = $search->count(); // 获取搜索结果的匹配总数估算值

        // 搜索建议
        $corrected_arr = array();
        if ($page ==1 && $count < 5) {
            $corrected = $search->getCorrectedQuery();
            $corrected_arr = $corrected;
        }
        $words = array();
        $words = $search->getRelatedQuery(null, 6);//最后调用 相关搜索

        $hot_arr = $search->getHotQuery(10, 'currnum'); // 获取前 10 个本周热门词

        $list_data = array();
        foreach ($docs as $key => $doc) {
            $subject      = $search->highlight($doc->subject); // 高亮处理 subject 字段
            $deal_content = $search->highlight($doc->deal_content); // 高亮处理 message 字段
            $id           = $doc->id;
            $rank         = $doc->rank();
            $post_time    = intval($doc->post_time);
            $list_data[] = array(
                'id'           => $id,
                'subject'      => $subject,
                'deal_content' => $deal_content,
                'post_time'    => $post_time,
                'rank'         => $rank,
                );
        }

        $res = array(
            'total'         => $count,
            'list_data'     => $list_data,
            'relation_arr'  => $words,
            'corrected_arr' => $corrected_arr,
            'hot_arr'       => $hot_arr,
            );
        return $res;
    }

    /**
     * 获取推荐圈子贴
     * @param [array] $data [description]
     * @author ldb
     * @date(2015-04-27)
     */
    public static function getMayLikeInfo($keyword, $page, $pagesize)
    {
        $offset   = ($page-1)*$pagesize;
        $index_app = '115zone';
        $xs = new XS($index_app); // 建立 XS 对象，项目名称为：demo
        $search = $xs->search; // 获取 搜索对象
        $query = trim($keyword); // 这里的搜索语句很简单，就一个短语

        $search->setQuery($query); // 设置搜索语句
        $search->setCollapse('subject',1);//按字段值折叠搜索结果
        // $search->addWeight('subject', 'xunsearch'); // 增加附加条件：提升标题中包含 'xunsearch' 的记录的权重
        $search->setLimit($pagesize, $offset); // 设置返回结果最多为 $pagesize 条，并跳过前 $offset 条
        // $search->setFuzzy();
        $docs = $search->search(); // 执行搜索，将搜索结果文档保存在 $docs 数组中
        $count = $search->count(); // 获取搜索结果的匹配总数估算值
        // var_dump($docs);exit;

        $list_data = array();
        foreach ($docs as $key => $doc) {
            $subject      = $doc->subject; // 高亮处理 subject 字段
            $deal_content = $doc->deal_content; // 高亮处理 message 字段
            $id           = $doc->id;
            $rank         = $doc->rank();
            $post_time    = intval($doc->post_time);
            $list_data[] = array(
                'id'           => $id,
                'subject'      => $subject,
                'deal_content' => $deal_content,
                'post_time'    => $post_time,
                'rank'         => $rank,
                );
        }

        $res = array(
            'total'          => $count,
            'list_data'      => $list_data,
            );
        return $res;

    }

}
