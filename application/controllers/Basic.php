<?php

/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class BasicController extends Yaf_Controller_Abstract
{
    public $layout = 'layout.html';
    public $title  = '';

    /**
     * 如果定义了控制器的init的方法, 会在__construct以后被调用
     */
    public function init()
    {
        //项目开始，未登录跳转到登陆页面
        // $session = Yaf_Session::getInstance();
        // if (!$session->user) {
        //     $this->redirect("/sign/index");
        // }
        $REQUEST_URI = $this->getRequest()->getServer('REQUEST_URI', '');
        if (strpos($REQUEST_URI, '/index.php') === 0) {
            throw new Exception('index.php not allow', YAF_ERR_NOTFOUND_ACTION);
        }

        Yaf_Dispatcher::getInstance()->c = $this;//保存当前控制器
        if ($this->getRequest()->isXmlHttpRequest()) {
            //如果是Ajax请求, 关闭自动渲染, 由我们手工返回Json响应
            Yaf_Dispatcher::getInstance()->autoRender(FALSE);
        }
    }

    public function indexAction()
    {
        // $this->getView()->display("sign/login.html");
    }
}
