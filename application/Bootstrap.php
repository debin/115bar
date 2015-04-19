<?php

/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Ap调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract {

    //语言包 设置当前用户的语言类型
    public function _initLang() {
        Yaf_Session::getInstance()->start();
        I18nHelper::getInstance()->getUserLang();
    }

    /**
     * 加载配置
     */
    public function _initConfig() {
        Yaf_Registry::set('config', Yaf_Application::app()->getConfig());
        // Yaf_Dispatcher::getInstance()->autoRender(FALSE);  // 关闭自动加载模板
        //加载公共函数
        Yaf_Loader::import(APPLICATION_PATH . "/library/func_common.php");
    }

    /**
     * 加载注册插件
     * 重构view路径
     */
    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        //添加配置中的路由
        $router = Yaf_Dispatcher::getInstance()->getRouter();
        $router->addConfig(Yaf_Registry::get("config")->routes);
        // $user = new UserPlugin();
        // $dispatcher->registerPlugin($user);
    }

    /**
     * 设置页面layout
    */
    public function _initLayout(Yaf_Dispatcher $dispatcher){
        /*layout allows boilerplate HTML to live in /views/layout rather than every script*/
        $layout = new LayoutPlugin();

        /* Store a reference in the registry so values can be set later.
         * This is a hack to make up for the lack of a getPlugin
         * method in the dispatcher.
         */
        Yaf_Registry::set('layout', $layout);

        /*add the plugin to the dispatcher*/
        $dispatcher->registerPlugin($layout);
    }
}
