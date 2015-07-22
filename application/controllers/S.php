<?php

/*
 * 搜索主题
 * @author dbb
 * @date(2014-12-15)
 */

class SController extends BasicController
{
    /**
     * 搜索
     *
     * @author ldb
     * @date(2014-12-15)
     */
    public function indexAction() {
        // $key = $this->getRequest()->getQuery("key",'');
        $key = $this->getRequest()->getParam("key", '');
        $key = urldecode($key);
        $page = $this->getRequest()->getParam("page", 1);
        $page = intval($page);
        if (empty($page)) {
            $page = 1;
        }
        $pagesize = 20;
        $res = SearchModel::getZoneInfo($key, $page, $pagesize);//var_dump($res);exit;
        $list_data = $res['list_data'];
        $total = $res['total'];
        if (empty($key)) {
            $total = $pagesize;
        }
        $page_count = ($total<=$pagesize)?1:intval(ceil($total/$pagesize));
        if ($page>$page_count) {
            // $page = $page_count;
            $this->redirect('/s/'.$key.'/'.$page_count);
            Yaf\Dispatcher::getInstance()->autoRender(FALSE);
            return;
        }
        // 分页
        $url = Yaf\Registry::get("config")->webroot . "/s/$key";
        // echo json_encode( get_defined_vars() );
        // $SERVER = $this->getRequest()->getServer();
        $getpage = new PageModel($url, $total, $page, $pagesize, '/');
        $paginate = $getpage->showpage();
        // echo $paginate;
        // exit;

        if (!empty($key)) {
            $this->title = _("la_102")." › "._("la_103"). " › "._("la_104").'_'. $key.' '. $page. "/" . $page_count;
        }else{
            $this->title = _("la_102")." › "._("la_103"). " › "._("la_104")._("la_109");
        }
        $output                       = array();
        $output['total']              = $total;
        $output['list']               = $list_data;
        $output['data']['page']       = $page;
        $output['data']['pagesize']   = $pagesize;
        $output['data']['page_count'] = $page_count;
        $output['paginate']           = $paginate;
        $output['key']                = $key;
        $output['relation_arr']       = $res['relation_arr'];
        $output['corrected_arr']      = $res['corrected_arr'];
        $output['hot_arr']            = $res['hot_arr'];
        // $output                       = cat_html($output);
        $this->getView()->assign("output", $output);
        // $this->getView()->display("topics/index.html");
    }

}
