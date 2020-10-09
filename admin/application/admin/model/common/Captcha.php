<?php

namespace app\admin\model\common;


class Captcha
{

    protected $captchaId = '7737e7fbf6e34e338da078992e15f9f8';

    protected $secretId = '6bc895d122564d9f87ed8ccac49658a3';

    protected $secretKey = 'c17fbe25484941d8905f29310e349638';

    protected $version = '1.0';

    protected $error = '';

    public function getSignature($param)
    {
        ksort($param);
        $str = '';
        foreach($param as $key => $val)
            $str .= $key . $val;
        $str .= $this->secretKey;
        return md5($str);
    }

    public function verify( $authenticate, $token)
    {
        $param = array();
        $param["authenticate"] = $authenticate;//用户验证通过后，返回的参数
        $param["token"] = $token;//前端返回的 token
        $param["captchaId"] = $this->captchaId;//验证应用 id
        $param["secretId"] = $this->secretId;//验证应用 secretId
        $param["version"] = "1.0";//版本，固定值1.0
        $param["timestamp"] = sprintf("%d", round(microtime(true)*1000));// 当前时间戳的毫秒值，如1541064141441    
        $param["nonce"] = sprintf("%d", rand(1,99999)); //随机正整数, 在 1-99999 之间

        $param["signature"] = $this->getSignature($param);//签名信息，见签名计算方法

        $url="https://captcha.yunpian.com/v1/api/authenticate";

        $result = curlRequest($url , 'POST', ['Content-Type:application/x-www-form-urlencoded'], $param);
        
        $result = json_decode($result, true);
        
        if( !$result || $result['code'] != 0 ){
            if( $result['msg'] ) $this->setError($result['msg']);
            return false;
        }
        
        return true;

    }

    public function setError($error)
    {
        $this->error =  $error;
        return true;
    }

    public function getError()
    {
        return $this->error ? : '';
    }

}

