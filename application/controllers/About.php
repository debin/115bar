<?php

/*
 * 联系方式
 * @author ldb
 * @date(2015-04-26)
 */
class AboutController extends BasicController
{

    /**
     * 搜索
     *
     * @author ldb
     * @date(2014-12-15)
     */
    public function indexAction()
    {
        // $key = $this->getRequest()->getQuery("key",'');
        $key = $this->getRequest()->getParam("key", '');
        $key = urldecode($key);
        $page = $this->getRequest()->getParam("page", 1);
        $page = intval($page);
        if (empty($page)) {
            $page = 1;
        }
        $pagesize = 20;
        $res = SearchModel::getZoneInfo($key, $page, $pagesize);
        $list_data = $res['list_data'];
        $total = $res['total'];
        if (empty($key)) {
            $total = $pagesize;
        }
        // 分页
        $url = Yaf_Registry::get("config")->webroot . "/s/$key";
        // echo json_encode( get_defined_vars() );
        // $SERVER = $this->getRequest()->getServer();
        $getpage = new PageModel($url, $total, $page, $pagesize,'/');
        $paginate = $getpage->showpage();
        // echo $paginate;
        // exit;

        $this->title = _("la_102")." › " ._("la_105");
        $output                       = array();
        $output['total']              = $total;
        $output['list']               = $list_data;
        $output['data']['page']       = $page;
        $output['data']['pagesize']   = $pagesize;
        $output['data']['page_count'] = 0;
        $output['paginate']           = $paginate;
        $output['key']                = $key;
        // $output                       = cat_html($output);
        $this->getView()->assign("output", $output);
        // $this->getView()->display("topics/index.html");
    }

}
