<?php

/**
 * Ap定义了如下的7个Hook,
 * 插件之间的执行顺序是先进先Call
 */
class LayoutPlugin extends Yaf_Plugin_Abstract
{

    private $_layoutDir;
    private $_layoutFile;
    private $_layoutVars =array();

    public function __construct($layoutDir = null)
    {
        $this->_layoutFile = '';
        $this->_layoutDir = ($layoutDir) ? $layoutDir : APPLICATION_PATH.'/views/layout/';
    }

    public function  __set($name, $value)
    {
        $this->_layoutVars[$name] = $value;
    }

    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {

    }

    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {

    }

    public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        /* get the body of the response */
        $body = $response->getBody();

        /*clear existing response*/
        $response->clearBody();

        // $class = new ReflectionClass($request);//建立 Person这个类的反射类
        // $properties = $class->getProperties(); //getProperties getMethods
        // foreach($properties as $property) {
        //     echo $property->getName()."<br/>";
        // }
        // exit;
        if (!isset(Yaf_Dispatcher::getInstance()->c->layout)) {
            return;
        }
        $this->_layoutFile = Yaf_Dispatcher::getInstance()->c->layout;

        /* wrap it in the layout */
        $layout = new Yaf_View_Simple($this->_layoutDir);
        $layout->title = Yaf_Dispatcher::getInstance()->c->title;
        $layout->content = $body;
        $layout->assign('layout', $this->_layoutVars);

        /* set the response to use the wrapped version of the content */
        $response->setBody($layout->render($this->_layoutFile));
    }

    public function preDispatch(Yaf_Request_Abstract $request , Yaf_Response_Abstract $response)
    {

    }

    public function preResponse(Yaf_Request_Abstract $request , Yaf_Response_Abstract $response)
    {

    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {

    }

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {

    }
}
