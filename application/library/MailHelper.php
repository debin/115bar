<?phpclass MailHelper extends Singleton{    public function sendTip($subject,$text,$html=''){        $redis = RedisHelper::getInstance();        $redis_key = "115:lasttip";        $output = (int)$redis->get($redis_key);        $now = time();        $limit = $now - $output;        if ($limit<300) {            return false;        }        $redis->set($redis_key,$now);        $url  = 'https://api.sendgrid.com/';        $user = 'youyou';        $pass = 'youyou123456';        // $to      = 'touch777@qq.com';        $to      = 'admin@zhidaohu.com';        $from    = 'tip@115bar.com';        $subject = $subject;        $html    = $html;        $text    = $text;        $params = array(            'api_user'  => $user,            'api_key'   => $pass,            'to'        => $to,            'subject'   => $subject,            'html'      => $html,            'text'      => $text,            'from'      => $from,          );        $request =  $url.'api/mail.send.json';        // Generate curl request        $session = curl_init($request);        // Tell curl to use HTTP POST        curl_setopt ($session, CURLOPT_POST, true);        // Tell curl that this is the body of the POST        curl_setopt ($session, CURLOPT_POSTFIELDS, $params);        // Tell curl not to return headers, but do return the response        curl_setopt($session, CURLOPT_HEADER, false);        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);        // obtain response        $response = curl_exec($session);        if ($response===false) {            $err_msg = curl_error($session);            // echo $err_msg;        }        curl_close($session);        return true;        // print everything out        // echo $response;    }}?>