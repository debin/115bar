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
     * @date(2015-04-15)
     */
    public function indexAction()
    {
        // $key = $this->getRequest()->getQuery("key",'');
        // echo $paginate;
        // exit;

        $this->title = FuncHelper::_("la_102")." › " .FuncHelper::_("la_105");
        $output                       = array();
        // $output                       = cat_html($output);
        $this->getView()->assign("output", $output);
        // $this->getView()->display("topics/index.html");
    }

}
