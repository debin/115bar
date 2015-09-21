<?php

/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class IndexController extends BasicController
{

    /**
     * 如果定义了控制器的init的方法, 会在__construct以后被调用
     */
    // public function init() {
    //     //$array = array('result'=>ture);
    //     //echo "controller init called<br/>";
    //     //$config = Yaf_Application::app()->getConfig();
    //     //$this->getView()->assign("title", "Agile Platform Demo");
    //     //$this->getView()->assign("webroot", $config->webroot);
    // }

    public function indexAction()
    {

        $this->title = FuncHelper::_("la_102")." › ".FuncHelper::_("la_103")." › ".FuncHelper::_("la_104");

        $output = array();
        $this->getView()->assign("output", $output);


        // echo 1;exit;
        // 跳转到首页
        // $this->redirect("/t/1");
        // $this->forward("t", "index", array());
        return;
    }
}
