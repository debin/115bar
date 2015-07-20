<?php

/*
 * sitemap列表
 * @author dbb
 * @date(2014-12-15)
 */

class SmController extends BasicController
{

    /**
     * 如果定义了控制器的init的方法, 会在__construct以后被调用
     */
    public function init()
    {
        Yaf_Dispatcher::getInstance()->autoRender(FALSE);
    }

    /**
     * 登录模板
     *
     * @author dbb
     * @date(2014-12-15)
     */
    public function indexAction()
    {
        $page = $this->getRequest()->getParam("page", 0);
        $page = intval($page);
        $pagesize = 20000;
        $data = array('status'=>0);
        $output  = array();

        if (empty($page)) {
            $total = TopicModel::getInfoCount($data);
            $page_count = ($total<=$pagesize)?1:intval(ceil($total/$pagesize));
            // $page_count = 200;
            $output['page_count']  = $page_count;
            // var_dump($output);exit;
            $this->getView()->assign("output", $output);
            $this->getView()->display("sm/index.html");
        } else {
            $list_arr = TopicModel::getInfoByPage2($data, $page, $pagesize, "ASC");

            $output['list']               = $list_arr;

            $output['data']['page']       = $page;
            $output['data']['pagesize']   = $pagesize;

            // $output                       = cat_html($output);
            $this->getView()->assign("output", $output);
            // var_dump($output);exit;
            $this->getView()->display("sm/list.html");
        }

    }

}
