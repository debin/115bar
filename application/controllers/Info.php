<?php

/*
 * 信息页
 * @author ldb
 * @date(2015-04-15)
 */

class InfoController extends BasicController {

    /**
     * 登录模板
     *
     * @author dbb
     * @date(2014-12-15)
     */
    public function indexAction() {
        $id = $this->getRequest()->getParam("id", 0);
        $id = intval($id);

        $redis = RedisHelper::getInstance();
        $redis_key = "115:info:".$id;
        $timeout = 300;
        $output = $redis->get($redis_key);

        if (1) {
            $detail = TopicModel::getInfoById($id);

            // 推荐
            $tags = isset($detail['tags'])?$detail['tags']:'';
            $tags = trim($tags,"{}");
            $tag_arr = ($tags)?explode (',',$tags):array();

            $maylike_list = array();
            if (!empty($tags)) {
                $tags = str_replace(",", " OR ", $tags);
                $maylike_res = SearchModel::getMayLikeInfo($tags,1,8);
                $maylike_list = $maylike_res['list_data'];
                foreach ($maylike_list as $key => $value) {
                    if ($value['id'] == $id) {
                        unset($maylike_list[$key]);
                    }
                }
            }

            // 上一篇
            $prev_info = TopicModel::getNearInfoById($id,'>',"ASC");
            $next_info = TopicModel::getNearInfoById($id,'<');


            // 最近更新
            $data = array('status'=>0);
            $latest_list_arr = TopicModel::getInfoByPage($data,1,5);

            $output                 = array();
            $output['id']           = $id;
            $output['detail']       = $detail;
            $output['latest_list']  = $latest_list_arr;
            $output['maylike_list'] = $maylike_list;
            $output['tag_arr']      = $tag_arr;
            $output['prev_info']    = $prev_info;
            $output['next_info']    = $next_info;
            $redis->set($redis_key,$output,$timeout);

        }

        // title
        $subject = !empty($output['detail']['subject'])?$output['detail']['subject']:$output['detail']['abstract'];
        $search = array(" ","|","!","»");
        $subject = str_replace($search,'',$subject);
        $this->title = $subject."_"._("la_103")."_"._("la_102");

        $this->getView()->assign("output", $output);
        // $this->getView()->display("sign/login.html");
    }

    /**
     * 账户登录
     *
     * @author ldb
     * @date(2015-04-15)
     */
    public function detailAction() {
        echo $_GET['ff'];
        echo time();exit;
        $username = returnParam($_POST['username']);
        $password = returnParam($_POST['password']);
        $vcode = isset($_POST['vcode'])?strtolower(trim($_POST['vcode'])):'';

        $session = Yaf_Session::getInstance();
        if (empty($vcode) || $session->vcode != $vcode) {
            $msg = _("lj_219");//验证码错误
            return callback(0,$msg);
        }
        if(empty($username) || empty($password)){
            $msg = _("la_2048");//参数错误
            return callback(0,$msg);
        }
        $ret = User_UserModel::getUserByUsername($username);
        if(empty($ret)){
            $msg = _("la_3281");//该用户不存在
            return callback(0,$msg);
        }
        // $pass = md5($ret['encrypt'].$password);
        $pass = User_UserModel::encryptPsw($ret['encrypt'],$password);
        if(!isset($ret['password']) || $ret['password'] !== $pass){
            $msg = _("la_406");//密码错误
            return callback(0,$msg);
        }

        //登录成功，session所需要的值
        $session->del("vcode");
        $session->username = $username;
        $session->user = $ret['id'];
        $session->login_time = time();
        callback(1);
    }

    /**
     * 退出账户
     *
     * @author dbb
     * @date(2014-12-15)
     */
    public function logoutAction(){
        $session = Yaf_Session::getInstance();
        $session->del("user");
        $this->redirect("/sign/index");
    }

    //暂时不做
    public function repasswordAction(){
        $this->redirect("/sign/index");
    }

    /**
     * 验证码
     */
    public function ajaxvcodeAction(){
        header("Content-type: image/png");

        //创建真彩色画板
        $msg = _("la_3182");//重新获取
        $im = @imagecreatetruecolor(65, 27) or die($msg);
        //获取背景颜色
        $background_color = imagecolorallocate($im, 255, 255, 255);
        //填充背景颜色
        imagefill($im,0,0,$background_color);

        //逐行炫耀背景，全屏用1或0
        for($i=0;$i<27;$i++){
            //获取随机淡色
            $line_color = imagecolorallocate($im,rand(200,255),rand(200,255),rand(200,255));
            //画线
            imageline($im,0,$i,65,$i,$line_color);
        }

        //设置字体大小
        $font_size=14;

        //设置印上去的文字
        $Str[0] = "ABCDEFGHIJKLMNPQRSTWXY";
        $Str[1] = "abcdefghjkmnprstwxy";
        $Str[2] = "234567892345678923456789";

        //获取第1个随机文字
        $i = rand(0,2);
        $imstr[0]["s"] = $Str[$i][rand(0,strlen($Str[$i])-1)];
        $imstr[0]["x"] = rand(2,5);
        $imstr[0]["y"] = rand(1,4);

        //获取第2个随机文字
        $i = rand(0,2);
        $imstr[1]["s"] = $Str[$i][rand(0,strlen($Str[$i])-1)];
        $imstr[1]["x"] = $imstr[0]["x"]+$font_size-1+rand(0,1);
        $imstr[1]["y"] = rand(1,3);

        //获取第3个随机文字
        $i = rand(0,2);
        $imstr[2]["s"] = $Str[$i][rand(0,strlen($Str[$i])-1)];
        $imstr[2]["x"] = $imstr[1]["x"]+$font_size-1+rand(0,1);
        $imstr[2]["y"] = rand(1,4);

        //获取第4个随机文字
        $i             = rand(0,2);
        $imstr[3]["s"] = $Str[$i][rand(0,strlen($Str[$i])-1)];
        $imstr[3]["x"] = $imstr[2]["x"]+$font_size-1+rand(0,1);
        $imstr[3]["y"] = rand(1,3);

        $vcode = '';
        foreach($imstr as $value) {
            $vcode .= $value['s'];
        }

        // Yii::app()->session['vcode'] = $vcode;
        Yaf_Session::getInstance()->vcode = strtolower($vcode);

        //写入随机字串
        for($i=0;$i<4;$i++){
            //获取随机较深颜色
            $text_color = imagecolorallocate($im,rand(50,180),rand(50,180),rand(50,180));
            //画文字
            imagechar($im,$font_size,$imstr[$i]["x"],$imstr[$i]["y"],$imstr[$i]["s"],$text_color);
        }

        //显示图片
        imagepng($im);
        //销毁图片
        imagedestroy($im);
        return;
    }


    /**
     * 第一次初始化 生成一个超管和超管组
     * @return
     */
    public function ajaxinitAction() {

        //创建数据库
        User_UserModel::create();

        // 用户总数
        $total = User_UserModel::GetInfoCount(array("valid"=>1));
        if ($total>0) {
            echo "已经初始化过";
            return;
        }
        // 插入一个用户
        $username = "root";
        $init_psw = "123456";

        $psw = md5($init_psw);
        $encrypt = randStr(6);
        $password = User_UserModel::encryptPsw($encrypt,$psw);

        $user_data = array(
            "username"       => $username,
            "password"       => $password,
            "encrypt"        => $encrypt,
            "valid"          => 1,
            );
        $ret = User_UserModel::add($user_data);
        echo $username," : ",$init_psw;
        sleep(1);
        return;
    }

}
