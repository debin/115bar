<?php

/*
 * 公共函数
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function sample() {
    echo "this is a common function test";
}

/*
 * 处理接受的数据
 * @Params string $value
 */
function returnParam($value){
    $value = trim($value);
    return $value;
    //return strip_tags(htmlspecialchars($data,ENT_QUOTES));
    //return htmlspecialchars(strip_tags($data));
    //return strip_tags($data);
}

/*
 * 返回数据
 * @Param $msg string
 * @Param $data array
 * @Param $result boolean
 * @Param $code int
 *
 */
function callback($code=0, $msg='', $data=array(),$result=true){
    echo json_encode(array(
        "msg"    => $msg,
        "data"   => $data,
        "result" => $result,
        "code"   => $code,
    ));
    exit;
    return;
}

/**
 * 随机字条串
 *
 * @author xie
 * @param int $leng 长度
 * @return string
 */
function randStr($leng = 6) {
    $string_s = "qwertyuipkjhgfdsazxcvbnm123456789";
    $new_str = "";
    for ($i = 0; $i < $leng; $i++) {
        $new_str .= $string_s{rand(0, strlen($string_s) - 1)};
    }
    return $new_str;
}

/**
 * 获取语言包
 *
 * @author ldb
 * @param string $la 语言包标签
 * @param string $lang_type 语言包类型，默认为当前用户语言
 * @return string
 */
function _($la,$lang_type='') {
    return I18nHelper::getInstance()->getLang($la,$lang_type);
}


/**
 * 对一个字符串或者array中的字符串 htmlspecialchars
 *
 * @author ldb
 * @param mixed $mixed 要处理的数据
 * @return mixed
 */

function cat_html($mixed, $quote_style = ENT_QUOTES, $charset = 'UTF-8') {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = cat_html($value, $quote_style, $charset);
        }
    } elseif (is_string($mixed)) {
        $mixed = htmlspecialchars($mixed, $quote_style, $charset);
    }
    return $mixed;
}

/**
 * 对一个字符串或者array中的字符串 escape
 *
 * @author ldb
 * @param mixed $mixed 要处理的数据
 * @return mixed
 */
function cat_escape($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = cat_escape($value);
        }
    } elseif (is_string($mixed)) {
        $mixed = mysql_escape_string($mixed);
    }
    return $mixed;
}

/**
 * 提示并返回上级页面
 * @param  string $str 弹出提示
 * @return
 */
function gobacktip($str) {

    // echo  "
    // <script>
    // alert('{$str}');
    // window.history.back();
    // </script>";
    echo  '
    <script type="text/javascript" language="javascript">
      function sAlert(str){
      var msgw,msgh,bordercolor;
      msgw=400;//提示窗口的宽度
      msgh=100;//提示窗口的高度关闭
      titleheight=25 //提示窗口标题高度
      bordercolor="#336699";//提示窗口的边框颜色
      titlecolor="#99CCFF";//提示窗口的标题颜色

      var sWidth,sHeight;
      sWidth=document.body.offsetWidth;
      sHeight=screen.height;
      var bgObj=document.createElement("div");
      bgObj.setAttribute("id","bgDiv");
      bgObj.style.position="absolute";
      bgObj.style.top="0";
      bgObj.style.background="#777";
      bgObj.style.filter="progid:DXImageTransform.Microsoft.Alpha(style=3,opacity=25,finishOpacity=75";
      bgObj.style.opacity="0.6";
      bgObj.style.left="0";
      bgObj.style.width=sWidth + "px";
      bgObj.style.height=sHeight + "px";
      bgObj.style.zIndex = "10000";
      document.body.appendChild(bgObj);

      var msgObj=document.createElement("div")
      msgObj.setAttribute("id","msgDiv");
      msgObj.setAttribute("align","center");
      msgObj.style.background="white";
      msgObj.style.border="1px solid " + bordercolor;
       msgObj.style.position = "absolute";
              msgObj.style.left = "50%";
              msgObj.style.top = "50%";
              msgObj.style.font="12px/1.6em Verdana, Geneva, Arial, Helvetica, sans-serif";
              msgObj.style.marginLeft = "-225px" ;
              msgObj.style.marginTop = -75+document.documentElement.scrollTop+"px";
              msgObj.style.width = msgw + "px";
              msgObj.style.height =msgh + "px";
              msgObj.style.textAlign = "center";
              msgObj.style.lineHeight ="25px";
              msgObj.style.zIndex = "10001";

       var title=document.createElement("h4");
       title.setAttribute("id","msgTitle");
       title.setAttribute("align","right");
       title.style.margin="0";
       title.style.padding="3px";
       title.style.background=bordercolor;
       title.style.filter="progid:DXImageTransform.Microsoft.Alpha(startX=20, startY=20, finishX=100, finishY=100,style=1,opacity=75,finishOpacity=100);";
       title.style.opacity="0.75";
       title.style.border="1px solid " + bordercolor;
       title.style.height="18px";
       title.style.font="12px Verdana, Geneva, Arial, Helvetica, sans-serif";
       title.style.color="white";
       title.style.cursor="pointer";
       title.innerHTML="X";
       title.onclick=function(){
            document.body.removeChild(bgObj);
                  document.getElementById("msgDiv").removeChild(title);
                  document.body.removeChild(msgObj);
                  window.history.back();
                  }
       document.body.appendChild(msgObj);
       document.getElementById("msgDiv").appendChild(title);
       var txt=document.createElement("p");
       txt.style.margin="1em 0"
       txt.setAttribute("id","msgTxt");
       txt.innerHTML=str;
             document.getElementById("msgDiv").appendChild(txt);
              }
     </script>';

     echo  "
     <body>
     <script>
     sAlert('{$str}');
     </script>
     </body>";
    return;
}

/**
 * 格式化
 */
function print_rr($data){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}