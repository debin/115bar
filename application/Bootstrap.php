<?php

/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Ap调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf\Bootstrap_Abstract
{

    //语言包 设置当前用户的语言类型
    public function _initLang()
    {
        // Yaf\Session::getInstance()->start();
        I18nHelper::getInstance()->getUserLang();
    }

    /**
     * 加载配置
     */
    public function _initConfig()
    {
        Yaf\Registry::set('config', Yaf\Application::app()->getConfig());
        // Yaf_Dispatcher::getInstance()->autoRender(FALSE);  // 关闭自动加载模板
        //加载公共函数
        Yaf\Loader::import(APPLICATION_PATH . "/library/func_common.php");
        Yaf\Loader::import(ROOT.'/vendor/autoload.php');
    }

    /**
     * 加载注册插件
     * 重构view路径
     */
    public function _initPlugin(Yaf\Dispatcher $dispatcher)
    {
        //添加配置中的路由
        $router = Yaf\Dispatcher::getInstance()->getRouter();
        $router->addConfig(Yaf\Registry::get("config")->routes);
        // var_dump($router->getRoutes());exit;
        // $user = new UserPlugin();
        // $dispatcher->registerPlugin($user);
    }

    /**
     * 设置页面layout
    */
    public function _initLayout(Yaf\Dispatcher $dispatcher)
    {
        /*layout allows boilerplate HTML to live in /views/layout rather than every script*/
        $layout = new LayoutPlugin();

        /* Store a reference in the registry so values can be set later.
         * This is a hack to make up for the lack of a getPlugin
         * method in the dispatcher.
         */
        Yaf\Registry::set('layout', $layout);

        /*add the plugin to the dispatcher*/
        $dispatcher->registerPlugin($layout);
    }
}
