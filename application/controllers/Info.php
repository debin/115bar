<?php

/*
 * 信息页
 * @author ldb
 * @date(2015-04-15)
 */

class InfoController extends BasicController
{
    /**
     * 信息详情页面
     *
     * @author ldb
     * @date(2015-04-15)
     */
    public function indexAction()
    {
        $id = $this->getRequest()->getParam("id", 0);
        $id = intval($id);

        if (abs($id) > 99999999) {
            throw new Exception('Id is out of range for type integer:'.$id,YAF\ERR\NOTFOUND\ACTION);
        }

        $redis = RedisHelper::getInstance();
        $redis_key = "115:info:".$id;
        $timeout = 600;
        $output = $redis->get($redis_key);

        if (!$output) {
            $detail = TopicModel::getInfoById($id);

            if (empty($detail)) {
                throw new Exception('No this info:'.$id, YAF\ERR\NOTFOUND\ACTION);
            }

            // deal_content rel="nofollow"
            $pattern                = '/<a[^>]+(?>)/i';
            $callback               = array('FuncHelper', 'addnofollow');
            $deal_content           = preg_replace_callback($pattern, $callback, $detail['deal_content']);
            $deal_content           = nl2br($deal_content);
            $detail['deal_content'] = $deal_content;


            // 推荐
            $tags    = isset($detail['tags'])?$detail['tags']:'';
            $tags    = trim($tags, "{}");
            $tag_arr = ($tags)?explode (',',$tags):array();

            $maylike_list = array();
            if (!empty($tags)) {
                $tags         = str_replace(",", " OR ", $tags);
                $maylike_res  = SearchModel::getMayLikeInfo($tags, 1, 8);
                $maylike_list = $maylike_res['list_data'];
                foreach ($maylike_list as $key => $value) {
                    if ($value['id'] == $id) {
                        unset($maylike_list[$key]);
                    }
                }
            }

            // 上一篇
            $prev_info = TopicModel::getNearInfoById($id, $detail['post_time'], '>', "ASC");
            $next_info = TopicModel::getNearInfoById($id, $detail['post_time'], '<');


            // 最近更新
            $data = array('status'=>0);
            $latest_list_arr = TopicModel::getInfoByPage($data, 1, 5);

            $output                 = array();
            $output['id']           = $id;
            $output['detail']       = $detail;
            $output['latest_list']  = $latest_list_arr;
            $output['maylike_list'] = $maylike_list;
            $output['tag_arr']      = $tag_arr;
            $output['prev_info']    = $prev_info;
            $output['next_info']    = $next_info;
            $redis->set($redis_key, $output, $timeout);

        }

        // title
        $subject = !empty($output['detail']['subject'])?$output['detail']['subject']:$output['detail']['abstract'];
        $search = array(" ","|","!","»");
        $subject = str_replace($search, '', $subject);
        $this->title = FuncHelper::_("la_102")." › ".FuncHelper::_("la_103")." › ".$subject;

        $this->getView()->assign("output", $output);
        // $this->getView()->display("sign/login.html");
    }

}
