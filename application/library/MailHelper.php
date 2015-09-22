<?php/** * 邮件发送类 * @author ldb * @package library */class MailHelper extends Singleton{    /**     * 发出错误时，发送相关提示到邮箱     * @param  [type] $subject [description]     * @param  [type] $text    [description]     * @param  string $html    [description]     * @return [type]          [description]     */    public function sendTip($subject, $text, $html = '')    {        // redis判断300s内只发送一封        $redis_key = "115:lasttip";        try {            $redis = RedisHelper::getInstance();            $output = (int)$redis->get($redis_key);        } catch (Exception $e) {            $output = 0;        }        $now = time();        $limit = $now - $output;        if ($limit<300) {            return false;        }        try {            $redis->set($redis_key,$now);        } catch (Exception $e) {        }        // 从配置文件里面读取 sendgrid 相关        $config = Yaf\Registry::get("config");        $user   = $config->mail->user;        $pass   = $config->mail->pass;        $url    = $config->mail->url;        $from   = $config->mail->from;        $to     = $config->mail->to;        $subject = $subject;        $html    = $html;        $text    = $text;        $params = array(            'api_user'  => $user,            'api_key'   => $pass,            'to'        => $to,            'subject'   => $subject,            'html'      => $html,            'text'      => $text,            'from'      => $from,          );        $request =  $url.'api/mail.send.json';        // Generate curl request        $session = curl_init($request);        // Tell curl to use HTTP POST        curl_setopt ($session, CURLOPT_POST, true);        // Tell curl that this is the body of the POST        curl_setopt ($session, CURLOPT_POSTFIELDS, $params);        // Tell curl not to return headers, but do return the response        curl_setopt($session, CURLOPT_HEADER, false);        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);        // obtain response        $response = curl_exec($session);        if ($response === false) {            $err_msg = curl_error($session);            // echo $err_msg;        }        curl_close($session);        return true;        // print everything out        // echo $response;    }}