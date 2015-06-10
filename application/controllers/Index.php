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
    // public function init() {
    //     //$array = array('result'=>ture);
    //     //echo "controller init called<br/>";
    //     //$config = Yaf_Application::app()->getConfig();
    //     //$this->getView()->assign("title", "Agile Platform Demo");
    //     //$this->getView()->assign("webroot", $config->webroot);
    // }

    public function indexAction() {

        $this->title = "礼包搜索"."_"._("la_103")."_"._("la_102");

        $output = array();
        $this->getView()->assign("output", $output);


        // echo 1;exit;
        // 跳转到首页
        // $this->redirect("/t/1");
        // $this->forward("t", "index", array());
        return;
    }

    public function testAction() {
        $dbname = "dht";
        return ;
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

    public function xsAction() {
        return;
        require APPLICATION_PATH.'/vendor/xunsearch/php/lib/XS.php';
        $xs = new XS('demo'); // 建立 XS 对象，项目名称为：demo
        $search = $xs->search; // 获取 搜索对象
        $query = '8 8'; // 这里的搜索语句很简单，就一个短语

        $search->setQuery($query); // 设置搜索语句
        $search->setCollapse('pid',3);
        // $search->addWeight('subject', 'xunsearch'); // 增加附加条件：提升标题中包含 'xunsearch' 的记录的权重
        // $search->setLimit(5, 10); // 设置返回结果最多为 5 条，并跳过前 10 条
        // $search->setFuzzy();
        $docs = $search->search(); // 执行搜索，将搜索结果文档保存在 $docs 数组中
        $count = $search->count(); // 获取搜索结果的匹配总数估算值

        foreach ($docs as $doc)
        {
           $subject = $search->highlight($doc->subject); // 高亮处理 subject 字段
           $message = $search->highlight($doc->message); // 高亮处理 message 字段
           echo $doc->pid.' '. $doc->rank() . '. ' . $subject . " [" . $doc->percent() . "%] - ";
           echo date("Y-m-d", $doc->chrono) . "\n" . $message . "\n";
           var_dump($subject);
        }

        // var_dump($docs);
        exit;
    }


    public function xsaAction() {
        return;
        require APPLICATION_PATH.'/vendor/xunsearch/php/lib/XS.php';
        $xs = new XS('demo'); // 建立 XS 对象，项目名称为：demo
        $index = $xs->index; // 获取 索引对象
        $data = array(
            'pid' => 234, // 此字段为主键，必须指定
            'subject' => '测试文档的标题',
            'message' => '测试文档的内容部分',
            'chrono' => time()
        );

        $data = array(
            'pid' => 188, // 此字段为主键，必须指定
            'subject' => '888',
            'message' => '494fwefewf',
            'chrono' => time()
        );

        // 创建文档对象
        $doc = new XSDocument;
        $doc->setFields($data);

        // 添加到索引数据库中
        $res = $index->update($doc);
        var_dump($res);
        exit;
    }

}