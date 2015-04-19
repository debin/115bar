<?php

/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class IndexController extends BasicController {

    /**
     * 如果定义了控制器的init的方法, 会在__construct以后被调用
     */
    public function init() {
        //$array = array('result'=>ture);
        //echo "controller init called<br/>";
        //$config = Yaf_Application::app()->getConfig();
        //$this->getView()->assign("title", "Agile Platform Demo");
        //$this->getView()->assign("webroot", $config->webroot);
    }

    public function indexAction() {
        // echo 1;exit;
        // 跳转到首页
        // $this->redirect("/t/index");
        $this->forward("t", "index", array());
        return;
    }

    public function testAction() {
        $dbname = "dht";
        $db = PgsqlHelper::getInstance();
        $servers = ConfigPg::getDBMaster($dbname);
        // $a ->connect("203.195.196.161","root","123456","test");
        $db ->connect($servers[0],$servers[1],$servers[2],$dbname,$servers[3]);

        $sql = "SELECT * FROM test WHERE id IN(?,?) LIMIT ?;";
        $sql = "SELECT * FROM test WHERE id <? AND id>? LIMIT ?;";

        $vars = array(3,1,10);
        // 查询
        $res  = $db->getAll($sql,$vars);
        foreach ($res as $key => $value) {
            // var_dump($value);
            // echo '<br/>';
        }

        // 插入
        $js = json_encode(array("aa"=>456,"bb"=>999));
        $insert_data = array("a"=>8,"b"=>4,"c"=>$js,"d"=>9);
        // $db->insert("test",$insert_data);

        // 更新
        $condition = array("id"=>6);
        $update_data = array("b"=>99,"d"=>999);
        // $db->update("test",$update_data,$condition);
        // echo 222;//exit;
        $this->getView()->display("index/index.html");
    }

}