<?php

/*
 * 主题列表
 * @author ldb
 * @date(2015-04-15)
 */

class TController extends BasicController
{
    /**
     * 登录模板
     *
     * @author ldb
     * @date(2015-04-15)
     */
    public function indexAction()
    {
        $page = $this->getRequest()->getParam("page", 0);
        $page = intval($page);
        if (empty($page)) {
            $page = 1;
        }
        $pagesize = 20;

        $redis = RedisHelper::getInstance();
        $redis_key = "115:tlist:".$page;
        $timeout = 300;
        $output = $redis->get($redis_key);

        if (!$output){
            $data = array('status'=>0);
            $total = TopicModel::getInfoCount($data);
            $page_count = ($total<=$pagesize)?1:intval(ceil($total/$pagesize));
            if ($page>$page_count) {
                // $page = $page_count;
                $this->redirect('/t/'.$page_count);
                Yaf\Dispatcher::getInstance()->autoRender(FALSE);
                return;
            }
            $list_arr = TopicModel::getInfoByPage($data, $page, $pagesize);

            // 分页
            $url = Yaf\Registry::get("config")->webroot . "/t";
            // echo json_encode( get_defined_vars() );
            // $SERVER = $this->getRequest()->getServer();
            $getpage = new PageModel($url, $total, $page, $pagesize,'/');
            $paginate = $getpage->showpage();
            // echo $page->showpage();

            // var_dump($total);exit;

            $output                       = array();
            $output['total']              = $total;
            $output['list']               = $list_arr;
            $output['data']['page']       = $page;
            $output['data']['pagesize']   = $pagesize;
            $output['data']['page_count'] = $page_count;
            $output['paginate']           = $paginate;
            $redis->set($redis_key, $output, $timeout);
        }

        $this->title = _("la_102")." › "._("la_103").' ' .$page. "/" . $output['data']['page_count'];

        // $output                       = cat_html($output);
        $this->getView()->assign("output", $output);
        // $this->getView()->display("topics/index.html");
    }

}
