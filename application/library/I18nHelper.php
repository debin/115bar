<?php

/**
 * 获取用户语言版本
 * @author chenming
 * @package mxcommon
 */


/**
 * 获取用户语言版本
 * @author chenming
 * @package mxcommon_model
 */
class I18nHelper extends Singleton{

    /**
     * 用户语言版本
     * @var string cs en my id ct
     */
    public $user_lang;

    /**
     * 默认语言版本
     * @var string
     */
    public $default_lang = 'cs';

    /**
     * 语言包
     * @var string cs en my id ct
     */
    public $lang = array(
        'cs'=>array(),
        'en'=>array(),
        'ct'=>array(),
        'my'=>array(),
        'id'=>array(),
        );

    /**
     * 浏览器的header里面，accept　language值
     * @var array
     */
    public static $accept_language_map = array(   //注意顺序，zh要放zh-CN前面，从前往后匹配
        'zh'    =>'cs',     //  中文
        'zh-cn' =>'cs',  // 中文(简体)
        'zh-hk' =>'ct', //中文(香港)
        'zh-mo' =>'ct',   //    中文(澳门)
        'zh-sg' =>'ct', //中文(新加坡)
        'zh-tw' =>'ct',  //中文(繁体)
        'ms-my' =>'my',   //马来语(马来西亚)
        'en'    =>'en',   //
        );

    /**
     * 初始化用户语言设置
     * @return string
     */
    public function getUserLang(){

        if(!empty($this->user_lang)){
            return $this->user_lang;
        }

        // 先中文
        // $lang = 'cs';
        $lang = $this->__getUserLangFromCookie();
        if(empty($lang)){  // cookie拿不到
            $lang = $this->__getUserLangFromHeader();
        }
        $get_lang = $this->__getUserLangFromUrl();
        if(!empty($get_lang) && $get_lang != $lang){   //　需要设置语言
            $this->__setUserLang2Cookie($get_lang);
            $this->user_lang = $get_lang;
            return $get_lang;
        }

        if(empty($lang)){
            $lang = $this->default_lang;
        }
        // var_dump($this->user_lang,$lang);
        $this->user_lang = $lang;
        return $lang;
    }

    /**
     * 获取语言包
     *
     * @param string $la 语言包标签
     * @param string $lang_type 语言包类型，默认为当前用户语言
     * @return string
     */


    public function getLang($la,$lang_type=''){

        // 默认是当前用户语言
        $user_lang = $lang_type;
        if(empty($user_lang)){
            $user_lang =  $this->user_lang;
        }
        // 加载语言
        if (empty($this->lang[$user_lang])) {
            $lang_file = APPLICATION_PATH . '/i18n/Lang' . strtoupper($user_lang) . '.php';
            if (file_exists($lang_file)) {
                $this->lang[$user_lang] = include $lang_file;
            }
        }
        return $this->lang[$user_lang][$la];
    }


    /**
     * 从url请求中的get参数拿语言设置
     */
    private function __getUserLangFromUrl(){
        if(isset($_REQUEST['_lang_']) && in_array($_REQUEST['_lang_'], array('cs','en','my','id','ct'))){
            return $_REQUEST['_lang_'];
        }
        return null;
    }

    /**
     * 将语言写入cookie
     * @param string $mx_lang
     */
    private function __setUserLang2Cookie($mx_lang){
        setcookie('km_lang', $mx_lang, time()+86400*365,'/');
    }

    /**
     * 从cookie信息里面拿用户语言
     */
    private function __getUserLangFromCookie(){
        if(isset($_COOKIE['km_lang']) && in_array($_COOKIE['km_lang'], array('cs','en','my','id','ct'))){
            return $_COOKIE['km_lang'];
        }
        return null;
    }

    /**
     * 从浏览器的 header 头信息 accept language  拿用户语言设置
     */
    private function __getUserLangFromHeader(){
        if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
            return $this->default_lang;
        }
        $accept_language = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);

        $result = null;
        foreach (self::$accept_language_map as $key=>$value){
            if(preg_match('/'.$key.'/', $accept_language)){
                $result = $value;
                break;
            }
        }
        return $result;
    }

}